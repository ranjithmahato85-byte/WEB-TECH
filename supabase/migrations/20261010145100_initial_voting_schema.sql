-- Nepal Online Voting System
-- Phase 2: Supabase schema foundation
--
-- Security policies and the vote-casting RPC are added in the next phase.
-- Do not expose service-role credentials in the browser.

create extension if not exists pgcrypto;

create type public.account_role as enum ('voter', 'admin');
create type public.verification_status as enum (
  'pending',
  'approved',
  'rejected',
  'suspended'
);
create type public.election_status as enum (
  'draft',
  'scheduled',
  'active',
  'closed',
  'certified',
  'archived'
);
create type public.nomination_status as enum (
  'pending',
  'approved',
  'rejected',
  'withdrawn'
);

create or replace function public.normalize_citizenship_number(value text)
returns text
language sql
immutable
strict
as $$
  select nullif(lower(regexp_replace(trim(value), '[^a-z0-9]', '', 'g')), '');
$$;

create table public.profiles (
  id uuid primary key references auth.users(id) on delete cascade,
  full_name text not null check (char_length(trim(full_name)) between 2 and 120),
  role public.account_role not null default 'voter',
  date_of_birth date,
  mobile text,
  province text,
  district text,
  municipality text,
  ward text,
  citizenship_number text,
  citizenship_number_normalized text generated always as (
    public.normalize_citizenship_number(citizenship_number)
  ) stored,
  verification_status public.verification_status not null default 'pending',
  citizenship_verified boolean not null default false,
  is_active boolean not null default true,
  verified_by uuid references public.profiles(id),
  verified_at timestamptz,
  verification_note text,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  constraint profiles_citizenship_number_required check (
    role = 'admin' or citizenship_number_normalized is not null
  ),
  constraint profiles_verification_consistency check (
    (verification_status = 'approved' and citizenship_verified = true)
    or verification_status <> 'approved'
  )
);

create unique index profiles_citizenship_number_unique
  on public.profiles (citizenship_number_normalized)
  where citizenship_number_normalized is not null;

create index profiles_verification_status_idx
  on public.profiles (verification_status);

create table public.elections (
  id uuid primary key default gen_random_uuid(),
  title text not null check (char_length(trim(title)) between 3 and 200),
  description text not null default '',
  election_type text not null,
  location text not null,
  start_at timestamptz not null,
  end_at timestamptz not null,
  status public.election_status not null default 'draft',
  certified_at timestamptz,
  certified_by uuid references public.profiles(id),
  image_url text,
  created_by uuid not null references public.profiles(id),
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  constraint elections_valid_period check (end_at > start_at)
);

create index elections_status_period_idx
  on public.elections (status, start_at, end_at);

create table public.candidate_profiles (
  id uuid primary key default gen_random_uuid(),
  citizenship_number text not null,
  citizenship_number_normalized text generated always as (
    public.normalize_citizenship_number(citizenship_number)
  ) stored,
  full_name text not null check (char_length(trim(full_name)) between 2 and 120),
  biography text not null default '',
  photo_url text,
  created_by uuid not null references public.profiles(id),
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  constraint candidate_citizenship_number_valid check (
    citizenship_number_normalized is not null
  )
);

create unique index candidate_profiles_citizenship_unique
  on public.candidate_profiles (citizenship_number_normalized);

create table public.nominations (
  id uuid primary key default gen_random_uuid(),
  election_id uuid not null references public.elections(id) on delete cascade,
  candidate_id uuid not null references public.candidate_profiles(id) on delete restrict,
  party text not null,
  position text not null,
  symbol_name text not null,
  symbol_image text,
  status public.nomination_status not null default 'pending',
  reviewed_by uuid references public.profiles(id),
  reviewed_at timestamptz,
  review_note text,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  unique (election_id, candidate_id),
  constraint nominations_review_consistency check (
    (status = 'approved' and reviewed_by is not null and reviewed_at is not null)
    or status <> 'approved'
  )
);

create index nominations_election_status_idx
  on public.nominations (election_id, status);

create table public.voter_participation (
  id uuid primary key default gen_random_uuid(),
  voter_id uuid not null references public.profiles(id) on delete restrict,
  election_id uuid not null references public.elections(id) on delete restrict,
  confirmation_code text not null unique default encode(gen_random_bytes(12), 'hex'),
  voted_at timestamptz not null default now(),
  unique (voter_id, election_id)
);

create index voter_participation_election_idx
  on public.voter_participation (election_id);

create table public.anonymous_ballots (
  id uuid primary key default gen_random_uuid(),
  election_id uuid not null references public.elections(id) on delete restrict,
  nomination_id uuid not null references public.nominations(id) on delete restrict,
  cast_at timestamptz not null default now()
);

create index anonymous_ballots_results_idx
  on public.anonymous_ballots (election_id, nomination_id);

create table public.verification_documents (
  id uuid primary key default gen_random_uuid(),
  voter_id uuid not null references public.profiles(id) on delete cascade,
  storage_path text not null unique,
  document_type text not null default 'citizenship',
  status public.verification_status not null default 'pending',
  reviewed_by uuid references public.profiles(id),
  reviewed_at timestamptz,
  review_note text,
  created_at timestamptz not null default now(),
  constraint verification_documents_review_consistency check (
    (status in ('approved', 'rejected', 'suspended')
      and reviewed_by is not null and reviewed_at is not null)
    or status = 'pending'
  )
);

create index verification_documents_voter_status_idx
  on public.verification_documents (voter_id, status);

create table public.audit_logs (
  id bigint generated always as identity primary key,
  actor_id uuid references public.profiles(id) on delete set null,
  action text not null,
  entity_type text not null,
  entity_id uuid,
  metadata jsonb not null default '{}'::jsonb,
  created_at timestamptz not null default now()
);

create index audit_logs_entity_idx
  on public.audit_logs (entity_type, entity_id, created_at desc);

create or replace function public.set_updated_at()
returns trigger
language plpgsql
as $$
begin
  new.updated_at = now();
  return new;
end;
$$;

create trigger profiles_set_updated_at
  before update on public.profiles
  for each row execute function public.set_updated_at();

create trigger elections_set_updated_at
  before update on public.elections
  for each row execute function public.set_updated_at();

create trigger candidate_profiles_set_updated_at
  before update on public.candidate_profiles
  for each row execute function public.set_updated_at();

create trigger nominations_set_updated_at
  before update on public.nominations
  for each row execute function public.set_updated_at();

create or replace function public.handle_new_auth_user()
returns trigger
language plpgsql
security definer
set search_path = public
as $$
begin
  insert into public.profiles (
    id, full_name, citizenship_number, date_of_birth, province,
    district, municipality, ward
  )
  values (
    new.id,
    coalesce(nullif(trim(new.raw_user_meta_data ->> 'full_name'), ''), 'New voter'),
    nullif(trim(new.raw_user_meta_data ->> 'citizenship_number'), ''),
    nullif(trim(new.raw_user_meta_data ->> 'date_of_birth'), '')::date,
    nullif(trim(new.raw_user_meta_data ->> 'province'), ''),
    nullif(trim(new.raw_user_meta_data ->> 'district'), ''),
    nullif(trim(new.raw_user_meta_data ->> 'municipality'), ''),
    nullif(trim(new.raw_user_meta_data ->> 'ward'), '')
  );
  return new;
end;
$$;

create trigger on_auth_user_created
  after insert on auth.users
  for each row execute function public.handle_new_auth_user();

comment on table public.profiles is
  'One identity per authenticated person. Citizenship uniqueness is normalized and database-enforced.';
comment on table public.nominations is
  'Election-specific candidacy. A candidate profile may be nominated in multiple elections, but only once per election.';
comment on table public.voter_participation is
  'Eligibility/receipt record only; it deliberately stores no candidate choice.';
comment on table public.anonymous_ballots is
  'Ballot choice without voter_id. Results must aggregate this table, never a mutable counter.';
