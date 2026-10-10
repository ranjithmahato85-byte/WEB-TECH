-- Nepal Online Voting System
-- Phase 4: Auditable voter and citizenship-document review operations

drop policy if exists profiles_admin_manage on public.profiles;
drop policy if exists verification_documents_admin_manage on public.verification_documents;
drop policy if exists profiles_admin_read on public.profiles;
drop policy if exists verification_documents_admin_read on public.verification_documents;

create policy profiles_admin_read
  on public.profiles for select
  to authenticated
  using (public.is_admin());

create policy verification_documents_admin_read
  on public.verification_documents for select
  to authenticated
  using (public.is_admin());

create or replace function public.review_voter(
  requested_voter_id uuid,
  requested_status public.verification_status,
  requested_note text default null
)
returns public.profiles
language plpgsql
security definer
set search_path = public
as $$
declare
  reviewed_voter public.profiles;
  clean_note text;
begin
  if not public.is_admin() then
    raise exception using
      errcode = '42501',
      message = 'Administrator access is required';
  end if;

  if requested_voter_id = auth.uid() then
    raise exception using
      errcode = '22023',
      message = 'An administrator cannot review their own account';
  end if;

  if requested_status not in ('approved', 'rejected', 'suspended') then
    raise exception using
      errcode = '22023',
      message = 'Invalid voter review status';
  end if;

  clean_note := nullif(trim(coalesce(requested_note, '')), '');

  update public.profiles
  set
    verification_status = requested_status,
    citizenship_verified = requested_status = 'approved',
    is_active = requested_status <> 'suspended',
    verified_by = auth.uid(),
    verified_at = now(),
    verification_note = clean_note
  where id = requested_voter_id
    and role = 'voter'
  returning * into reviewed_voter;

  if not found then
    raise exception using
      errcode = 'P0002',
      message = 'Voter profile was not found';
  end if;

  insert into public.audit_logs (
    actor_id,
    action,
    entity_type,
    entity_id,
    metadata
  )
  values (
    auth.uid(),
    'voter_reviewed',
    'profile',
    requested_voter_id,
    jsonb_build_object(
      'status', requested_status,
      'note_present', clean_note is not null
    )
  );

  return reviewed_voter;
end;
$$;

revoke all on function public.review_voter(uuid, public.verification_status, text)
  from public, anon, authenticated;
grant execute on function public.review_voter(uuid, public.verification_status, text)
  to authenticated;

create or replace function public.review_verification_document(
  requested_document_id uuid,
  requested_status public.verification_status,
  requested_note text default null
)
returns public.verification_documents
language plpgsql
security definer
set search_path = public
as $$
declare
  reviewed_document public.verification_documents;
  clean_note text;
begin
  if not public.is_admin() then
    raise exception using
      errcode = '42501',
      message = 'Administrator access is required';
  end if;

  if requested_status not in ('approved', 'rejected', 'suspended') then
    raise exception using
      errcode = '22023',
      message = 'Invalid document review status';
  end if;

  clean_note := nullif(trim(coalesce(requested_note, '')), '');

  update public.verification_documents
  set
    status = requested_status,
    reviewed_by = auth.uid(),
    reviewed_at = now(),
    review_note = clean_note
  where id = requested_document_id
  returning * into reviewed_document;

  if not found then
    raise exception using
      errcode = 'P0002',
      message = 'Verification document was not found';
  end if;

  insert into public.audit_logs (
    actor_id,
    action,
    entity_type,
    entity_id,
    metadata
  )
  values (
    auth.uid(),
    'verification_document_reviewed',
    'verification_document',
    requested_document_id,
    jsonb_build_object(
      'status', requested_status,
      'note_present', clean_note is not null
    )
  );

  return reviewed_document;
end;
$$;

revoke all on function public.review_verification_document(uuid, public.verification_status, text)
  from public, anon, authenticated;
grant execute on function public.review_verification_document(uuid, public.verification_status, text)
  to authenticated;
