-- Phase 5: auditable nomination decisions and guarded election certification.

create or replace function public.review_nomination(
  nomination_id uuid,
  decision public.nomination_status,
  note text default null
)
returns public.nominations
language plpgsql
security definer
set search_path = public
as $$
declare
  result public.nominations;
begin
  if not public.is_admin() then
    raise exception 'Administrator access is required';
  end if;

  if decision not in ('approved', 'rejected', 'withdrawn') then
    raise exception 'Invalid nomination decision';
  end if;

  update public.nominations
  set status = decision,
      reviewed_by = auth.uid(),
      reviewed_at = now(),
      review_note = nullif(trim(note), '')
  where id = nomination_id
    and not exists (
      select 1 from public.elections e
      where e.id = nominations.election_id
        and e.status in ('active', 'closed', 'certified', 'archived')
    )
  returning * into result;

  if result.id is null then
    raise exception 'Nomination does not exist or its election is no longer editable';
  end if;

  insert into public.audit_logs (actor_id, action, entity_type, entity_id, metadata)
  values (
    auth.uid(),
    'nomination_' || decision::text,
    'nomination',
    result.id,
    jsonb_build_object('election_id', result.election_id, 'candidate_id', result.candidate_id)
  );

  return result;
end;
$$;

revoke all on function public.review_nomination(uuid, public.nomination_status, text) from public;
grant execute on function public.review_nomination(uuid, public.nomination_status, text) to authenticated;

create or replace function public.change_election_status(
  p_election_id uuid,
  p_next_status public.election_status
)
returns public.elections
language plpgsql
security definer
set search_path = public
as $$
declare
  result public.elections;
begin
  if not public.is_admin() then
    raise exception 'Administrator access is required';
  end if;

  if p_next_status = 'active' and not exists (
    select 1 from public.nominations n
    where n.election_id = p_election_id and n.status = 'approved'
  ) then
    raise exception 'An election needs at least one approved nomination before activation';
  end if;

  if p_next_status = 'certified' then
    if not exists (select 1 from public.elections e where e.id = p_election_id and e.status = 'closed') then
      raise exception 'Only closed elections can be certified';
    end if;
  end if;

  update public.elections
  set status = p_next_status,
      certified_at = case when p_next_status = 'certified' then now() else certified_at end,
      certified_by = case when p_next_status = 'certified' then auth.uid() else certified_by end
  where id = p_election_id
    and (
      status = 'draft' and p_next_status = 'scheduled'
      or status = 'scheduled' and p_next_status = 'active'
      or status = 'active' and p_next_status = 'closed'
      or status = 'closed' and p_next_status = 'certified'
      or status = 'certified' and p_next_status = 'archived'
    )
  returning * into result;

  if result.id is null then
    raise exception 'Invalid election status transition';
  end if;

  insert into public.audit_logs (actor_id, action, entity_type, entity_id, metadata)
  values (auth.uid(), 'election_status_changed', 'election', result.id,
    jsonb_build_object('status', p_next_status));
  return result;
end;
$$;

revoke all on function public.change_election_status(uuid, public.election_status) from public;
grant execute on function public.change_election_status(uuid, public.election_status) to authenticated;
