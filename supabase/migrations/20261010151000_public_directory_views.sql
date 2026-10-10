-- Nepal Online Voting System
-- Public read-only directory views for the static frontend

create or replace view public.public_election_directory
with (security_barrier = true)
as
  select
    e.id,
    e.title,
    e.description,
    e.election_type,
    e.location,
    e.start_at,
    e.end_at,
    e.status,
    e.image_url,
    count(n.id)::integer as approved_candidate_count
  from public.elections e
  left join public.nominations n
    on n.election_id = e.id
   and n.status = 'approved'
  where e.status in ('scheduled', 'active', 'closed', 'certified', 'archived')
  group by e.id;

create or replace view public.public_candidate_directory
with (security_barrier = true)
as
  select
    n.id as nomination_id,
    n.election_id,
    n.party,
    n.position,
    n.symbol_name,
    n.symbol_image,
    cp.id as candidate_id,
    cp.full_name,
    cp.biography,
    cp.photo_url,
    e.title as election_title,
    e.status as election_status
  from public.nominations n
  join public.candidate_profiles cp on cp.id = n.candidate_id
  join public.elections e on e.id = n.election_id
  where n.status = 'approved'
    and e.status in ('scheduled', 'active', 'closed', 'certified', 'archived');

grant select on public.public_election_directory to anon, authenticated;
grant select on public.public_candidate_directory to anon, authenticated;

comment on view public.public_candidate_directory is
  'Public candidate information only. Citizenship numbers and private identity fields are excluded.';
