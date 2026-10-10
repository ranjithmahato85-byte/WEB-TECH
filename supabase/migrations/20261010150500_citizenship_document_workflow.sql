-- Nepal Online Voting System
-- Phase 4: Private citizenship-document submission and review

drop policy if exists verification_documents_owner_insert on public.verification_documents;
drop policy if exists verification_documents_owner_delete_pending on public.verification_documents;

create policy verification_documents_owner_insert
  on public.verification_documents for insert
  to authenticated
  with check (
    voter_id = auth.uid()
    and status = 'pending'
    and reviewed_by is null
    and reviewed_at is null
  );

create policy verification_documents_owner_delete_pending
  on public.verification_documents for delete
  to authenticated
  using (
    voter_id = auth.uid()
    and status = 'pending'
  );

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

  if requested_status = 'approved'
     and not exists (
       select 1
       from public.verification_documents
       where voter_id = requested_voter_id
         and document_type = 'citizenship'
         and status = 'approved'
     ) then
    raise exception using
      errcode = '42501',
      message = 'An approved citizenship document is required first';
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
    actor_id, action, entity_type, entity_id, metadata
  )
  values (
    auth.uid(),
    'voter_reviewed',
    'profile',
    requested_voter_id,
    jsonb_build_object('status', requested_status, 'note_present', clean_note is not null)
  );

  return reviewed_voter;
end;
$$;

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
    actor_id, action, entity_type, entity_id, metadata
  )
  values (
    auth.uid(),
    'verification_document_reviewed',
    'verification_document',
    requested_document_id,
    jsonb_build_object('status', requested_status, 'note_present', clean_note is not null)
  );

  return reviewed_document;
end;
$$;

drop policy if exists citizenship_documents_owner_upload on storage.objects;

create policy citizenship_documents_owner_upload
  on storage.objects for insert
  to authenticated
  with check (
    bucket_id = 'citizenship-documents'
    and owner_id = auth.uid()::text
    and (storage.foldername(name))[1] = auth.uid()::text
  );

drop policy if exists citizenship_documents_owner_read on storage.objects;

create policy citizenship_documents_owner_read
  on storage.objects for select
  to authenticated
  using (
    bucket_id = 'citizenship-documents'
    and (
      owner_id = auth.uid()::text
      or public.is_admin()
    )
  );
