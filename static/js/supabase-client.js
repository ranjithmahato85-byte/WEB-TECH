/**
 * Browser-only Supabase client.
 *
 * This project is not a Next.js app, so it does not use @supabase/ssr,
 * next/headers, or Next.js middleware. Create static/js/supabase-config.js
 * locally with the values from supabase-config.example.js; that file is ignored by git.
 */
(function (root) {
  'use strict';

  const config = root.SUPABASE_CONFIG || {};
  const sdk = root.supabase;

  if (!sdk || typeof sdk.createClient !== 'function') {
    console.warn('Supabase SDK is not loaded. Add the supabase-js browser bundle before supabase-client.js.');
    root.VotingSupabase = null;
    return;
  }

  if (!config.url || !config.publishableKey) {
    console.warn('Supabase configuration is missing. Add static/js/supabase-config.js locally.');
    root.VotingSupabase = null;
    return;
  }

  const client = sdk.createClient(config.url, config.publishableKey);

  root.VotingSupabase = {
    client: client,
    auth: client.auth,
    db: client
  };
}(window));
