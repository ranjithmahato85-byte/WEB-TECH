# Supabase Integration

This directory contains the Supabase backend configuration and migrations for the Nepal Online Voting system.

## Overview

The project has been migrated from a legacy PHP + SQLite application to a static HTML/CSS/JavaScript frontend backed by Supabase. The migration was staged to preserve legacy behavior while switching the main routes to Supabase.

## Current Status

The Supabase static frontend is now the primary application surface. All main application routes have been redirected to Supabase pages:

- `/` → `pages/public/index.html`
- `/login` → `pages/auth/login.html`
- `/register` → `pages/auth/register.html`
- `/logout` → `pages/auth/logout.html`
- `/admin/*` → Supabase admin pages
- `/voter/*` → Supabase voter pages
- `/public/*` → Supabase public pages

## What Works

- Supabase database schema is deployed
- RLS is enabled on all relevant tables
- Supabase functions and views are deployed
- Supabase static frontend is the primary application surface
- A confirmed active admin profile exists for `ranjithmahato85@gmail.com`
- The admin dashboard loads pending voter applications and the citizenship-document queue
- The public homepage loads from Supabase and correctly reports that no public elections exist yet
- All Supabase HTML pages return HTTP 200
- All JavaScript files pass syntax checks
- All PHP files pass syntax checks
- The local application is running at `http://127.0.0.1:8000/`

## What Still Needs to Be Done

- Creating a real election through the Supabase admin UI
- Creating and approving a candidate identity and nomination
- Uploading and approving a citizenship document
- Approving a voter after document approval
- Activating an election
- Casting a vote through `cast_vote()`
- Duplicate-vote rejection
- Certified results through `election_results()`
- Ballot secrecy with separate admin and voter sessions
- Production hosting and HTTPS deployment

## Technical Details

- Environment: Windows, PHP 8.3.33, Node/npm
- Supabase target: `ohnuzzkthvvmfhchuvgs`
- URL: `https://ohnuzzkthvvmfhchuvgs.supabase.co`
- The project intentionally uses browser Supabase client code, not Next.js SSR
- Auth profile creation is handled by a database trigger from `auth.users`
- New accounts begin as role: `voter`, verification status: `pending`, citizenship verification: false, active status: true
- The first user account was promoted to `admin` using email: `ranjithmahato85@gmail.com`
- Voting architecture uses `voter_participation` and `anonymous_ballots` tables with `cast_vote()` RPC
- Election lifecycle: Draft → Scheduled → Active → Closed → Certified → Archived
- RLS is enabled on: profiles, elections, candidate_profiles, nominations, voter_participation, anonymous_ballots, verification_documents, audit_logs, storage.objects

## Migration Notes

The legacy PHP application and SQLite database remain in the repository as legacy source. They are not currently used by the primary GET routes after the cutover.

## Next Steps

1. Test the full end-to-end workflow with separate admin and voter accounts
2. Deploy the static frontend to an HTTPS host
3. Configure production Supabase Auth redirect URLs

## Migration Details

The migration in `migrations/20261010145100_initial_voting_schema.sql` is the
Phase 2 schema foundation for the voting system.

The migration in `migrations/20261010145300_security_policies_and_vote_rpc.sql`
adds the Phase 3 security boundary.

It provides:

- one normalized, unique citizenship identity per voter and candidate;
- pending, approved, rejected, and suspended verification states;
- election-specific candidate nominations;
- one participation record per voter and election;
- anonymous ballots that do not contain `voter_id`;
- document-review records and an administrative audit log.

Phase 3 adds:

- row-level security for voter, admin, election, nomination, and document data;
- a private `citizenship-documents` Storage bucket;
- `cast_vote(election_id, nomination_id)` as the only browser-callable vote
  operation;
- `election_results(election_id)` aggregation from anonymous ballots;
- a safe public candidate directory that never exposes citizenship numbers.

Apply it with the Supabase CLI from the project root:

```bash

supabase db push
```

The migrations are deployed in the configured Supabase project. Before a
production launch, test them with separate admin and voter accounts.

The browser authentication helpers are in
`static/js/supabase-auth.js`. Copy
`static/js/supabase-config.example.js` to `static/js/supabase-config.js`, then
load the Supabase browser bundle, the local configuration,
`supabase-client.js`, and then
`supabase-auth.js`, in that order. The helper requires full name, email,
password, citizenship number, date of birth, and local address during
registration so the database trigger can create a pending profile with the
eligibility fields required by voting.

The first static auth surfaces are:

- `pages/auth/login.html`
- `pages/auth/register.html`
- `pages/voter/dashboard.html`
- `pages/admin/dashboard.html`

They are the primary frontend surfaces. They require the local
`supabase-config.js` file and deployed migrations.

The verification review functions are in
`migrations/20261010150000_verification_review_functions.sql`. The admin
dashboard reads voter profiles through RLS and submits decisions through
`review_voter(...)`, which updates only the review fields and writes an audit
record. Direct profile updates are not used for approval decisions.

The citizenship workflow is in
`migrations/20261010150500_citizenship_document_workflow.sql`. Voters can
upload only their own JPG, PNG, or PDF file up to 5 MB into a user-scoped
private Storage path. Administrators receive a five-minute signed preview URL
and review documents through `review_verification_document(...)`. A voter
cannot be fully approved until an approved citizenship document exists.

The public directory views are in
`migrations/20261010151000_public_directory_views.sql`. The public
pages are under `pages/public/` and read election data from
`public_election_directory`, candidate data from
`public_candidate_directory`, and certified counts from
`election_results(...)`. They do not read private citizenship fields or raw
ballot rows.

The voter voting pages are under `pages/voter/`. `elections.html` reads only
active public elections and the authenticated voter's participation records.
`vote.html` reads approved nominations and calls `cast_vote(...)`; it never
inserts directly into `anonymous_ballots`.

Candidate and election administration is available under `pages/admin/`.
`candidates.html` reserves normalized candidate citizenship identities,
creates election nominations, and sends nomination decisions through the
audited `review_nomination(...)` function. Election status changes and result
certification use the audited `change_election_status(...)` function, which
enforces the allowed lifecycle and requires an approved nomination before
activation. `audit.html` provides the admin-only activity log.
