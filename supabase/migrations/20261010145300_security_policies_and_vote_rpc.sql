-- Nepal Online Voting System
-- Phase 3: Row Level Security and atomic vote casting

create or replace function public.is_admin()
returns boolean
language sql
stable
security definer
set search_path = public
as $$
  select exists (
    select 1
    from public.profiles
    where id = auth.uid()
      and role = 'admin'
      and is_active = true
  );
$$;

create or replace function public.is_verified_voter()
returns boolean
language sql
stable
security definer
set search_path = public
as $$
  select exists (
    select 1
    from public.profiles
    where id = auth.uid()
      and role = 'voter'
      and is_active = true
      and verification_status = 'approved'
      and citizenship_verified = true
  );
$$;

alter table public.profiles enable row level security;
alter table public.elections enable row level security;
alter table public.candidate_profiles enable row level security;
alter table public.nominations enable row level security;
alter table public.voter_participation enable row level security;
alter table public.anonymous_ballots enable row level security;
alter table public.verification_documents enable row level security;
alter table public.audit_logs enable row level security;

create policy profiles_select_own_or_admin
  on public.profiles for select
  to authenticated
  using (id = auth.uid() or public.is_admin());

create policy profiles_update_own
  on public.profiles for update
  to authenticated
  using (id = auth.uid())
  with check (
    id = auth.uid()
    and role = 'voter'
    and verification_status = 'pending'
    and citizenship_verified = false
    and verified_by is null
    and verified_at is null
  );

create policy profiles_admin_manage
  on public.profiles for all
  to authenticated
  using (public.is_admin())
  with check (public.is_admin());

create policy elections_public_read
  on public.elections for select
  to anon, authenticated
  using (status in ('scheduled', 'active', 'closed', 'certified', 'archived'));

create policy elections_admin_manage
  on public.elections for all
  to authenticated
  using (public.is_admin())
  with check (public.is_admin());

create policy candidate_profiles_admin_read
  on public.candidate_profiles for select
  to anon, authenticated
  using (false);

create policy candidate_profiles_admin_manage
  on public.candidate_profiles for all
  to authenticated
  using (public.is_admin())
  with check (public.is_admin());

create or replace view public.candidate_directory
with (security_barrier = true)
as
  select
    cp.id,
    cp.full_name,
    cp.biography,
    cp.photo_url
  from public.candidate_profiles cp
  where exists (
    select 1
    from public.nominations n
    join public.elections e on e.id = n.election_id
    where n.candidate_id = cp.id
      and n.status = 'approved'
      and e.status in ('scheduled', 'active', 'closed', 'certified', 'archived')
  );

grant select on public.candidate_directory to anon, authenticated;

create policy nominations_public_read
  on public.nominations for select
  to anon, authenticated
  using (
    status = 'approved'
    and exists (
      select 1
      from public.elections e
      where e.id = nominations.election_id
        and e.status in ('scheduled', 'active', 'closed', 'certified', 'archived')
    )
  );

create policy nominations_admin_manage
  on public.nominations for all
  to authenticated
  using (public.is_admin())
  with check (public.is_admin());

create policy participation_select_own_or_admin
  on public.voter_participation for select
  to authenticated
  using (voter_id = auth.uid() or public.is_admin());

create policy participation_admin_read
  on public.voter_participation for select
  to authenticated
  using (public.is_admin());

create policy verification_documents_select_own_or_admin
  on public.verification_documents for select
  to authenticated
  using (voter_id = auth.uid() or public.is_admin());

create policy verification_documents_admin_manage
  on public.verification_documents for all
  to authenticated
  using (public.is_admin())
  with check (public.is_admin());

create policy audit_logs_admin_read
  on public.audit_logs for select
  to authenticated
  using (public.is_admin());

-- Ballots have no browser-readable policy. Results are exposed through the
-- aggregate function below, not through individual ballot rows.

create or replace function public.cast_vote(
  requested_election_id uuid,
  requested_nomination_id uuid
)
returns table (
  confirmation_code text,
  voted_at timestamptz
)
language plpgsql
security definer
set search_path = public
as $$
declare
  current_voter public.profiles%rowtype;
  selected_nomination public.nominations%rowtype;
  selected_election public.elections%rowtype;
  participation_code text;
  participation_time timestamptz;
begin
  if auth.uid() is null then
    raise exception using
      errcode = '28000',
      message = 'Authentication is required to vote';
  end if;

  select *
  into current_voter
  from public.profiles
  where id = auth.uid()
  for share;

  if not found
     or current_voter.role <> 'voter'
     or not current_voter.is_active
     or current_voter.verification_status <> 'approved'
     or not current_voter.citizenship_verified
     or current_voter.date_of_birth is null
     or current_voter.date_of_birth > (current_date - interval '18 years')::date then
    raise exception using
      errcode = '42501',
      message = 'Your voter account is not eligible to vote';
  end if;

  select *
  into selected_election
  from public.elections
  where id = requested_election_id
  for share;

  if not found
     or selected_election.status <> 'active'
     or now() < selected_election.start_at
     or now() > selected_election.end_at then
    raise exception using
      errcode = '22023',
      message = 'This election is not currently accepting votes';
  end if;

  select *
  into selected_nomination
  from public.nominations
  where id = requested_nomination_id
    and election_id = requested_election_id
    and status = 'approved'
  for share;

  if not found then
    raise exception using
      errcode = '22023',
      message = 'The selected candidate is not approved for this election';
  end if;

  participation_time := clock_timestamp();
  participation_code := encode(gen_random_bytes(12), 'hex');

  insert into public.voter_participation (
    voter_id,
    election_id,
    confirmation_code,
    voted_at
  )
  values (
    auth.uid(),
    requested_election_id,
    participation_code,
    participation_time
  );

  insert into public.anonymous_ballots (
    election_id,
    nomination_id,
    cast_at
  )
  values (
    requested_election_id,
    requested_nomination_id,
    participation_time
  );

  insert into public.audit_logs (
    actor_id,
    action,
    entity_type,
    entity_id,
    metadata
  )
  values (
    auth.uid(),
    'vote_cast',
    'election',
    requested_election_id,
    jsonb_build_object('nomination_id', requested_nomination_id)
  );

  return query
  select participation_code, participation_time;
exception
  when unique_violation then
    raise exception using
      errcode = '23505',
      message = 'You have already voted in this election';
end;
$$;

revoke all on function public.cast_vote(uuid, uuid) from public, anon, authenticated;
grant execute on function public.cast_vote(uuid, uuid) to authenticated;

create or replace function public.election_results(requested_election_id uuid)
returns table (
  nomination_id uuid,
  candidate_id uuid,
  candidate_name text,
  vote_total bigint
)
language sql
stable
security invoker
set search_path = public
as $$
  select
    n.id,
    n.candidate_id,
    cp.full_name,
    count(ab.id)::bigint
  from public.nominations n
  join public.candidate_profiles cp on cp.id = n.candidate_id
  left join public.anonymous_ballots ab
    on ab.nomination_id = n.id
   and ab.election_id = n.election_id
  join public.elections e on e.id = n.election_id
  where n.election_id = requested_election_id
    and n.status = 'approved'
    and e.status in ('closed', 'certified', 'archived')
  group by n.id, n.candidate_id, cp.full_name
  order by count(ab.id) desc, cp.full_name asc;
$$;

revoke all on function public.election_results(uuid) from public;
grant execute on function public.election_results(uuid) to anon, authenticated;

-- Storage policies target a private bucket named citizenship-documents. The
-- bucket itself is created separately so deployment can choose its limits.
insert into storage.buckets (id, name, public, file_size_limit, allowed_mime_types)
values (
  'citizenship-documents',
  'citizenship-documents',
  false,
  5242880,
  array['image/jpeg', 'image/png', 'application/pdf']::text[]
)
on conflict (id) do update
set public = false,
    file_size_limit = excluded.file_size_limit,
    allowed_mime_types = excluded.allowed_mime_types;

create policy citizenship_documents_owner_read
  on storage.objects for select
  to authenticated
  using (
    bucket_id = 'citizenship-documents'
    and (owner_id = auth.uid()::text or public.is_admin())
  );

create policy citizenship_documents_owner_upload
  on storage.objects for insert
  to authenticated
  with check (
    bucket_id = 'citizenship-documents'
    and owner_id = auth.uid()::text
  );

create policy citizenship_documents_admin_manage
  on storage.objects for update
  to authenticated
  using (bucket_id = 'citizenship-documents' and public.is_admin())
  with check (bucket_id = 'citizenship-documents' and public.is_admin());

create policy citizenship_documents_admin_delete
  on storage.objects for delete
  to authenticated
  using (bucket_id = 'citizenship-documents' and public.is_admin());

comment on function public.cast_vote(uuid, uuid) is
  'Atomic one-vote-per-election operation. It creates participation and an anonymous ballot without exposing the voter-to-choice link.';
comment on function public.election_results(uuid) is
  'Returns counts from anonymous_ballots only for closed, certified, or archived elections.';
