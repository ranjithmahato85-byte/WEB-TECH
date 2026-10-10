(function (root) {
  'use strict';

  function client() {
    if (!root.VotingSupabase || !root.VotingSupabase.client) {
      throw new Error('Supabase is not configured.');
    }
    return root.VotingSupabase.client;
  }

  async function elections() {
    var response = await client().from('public_election_directory').select('*').order('start_at', { ascending: true });
    if (response.error) throw response.error;
    return response.data || [];
  }

  async function candidates() {
    var response = await client().from('public_candidate_directory').select('*').order('full_name', { ascending: true });
    if (response.error) throw response.error;
    return response.data || [];
  }

  async function results(electionId) {
    var response = await client().rpc('election_results', { requested_election_id: electionId });
    if (response.error) throw response.error;
    return response.data || [];
  }

  root.VotingPublicApi = {
    elections: elections,
    candidates: candidates,
    results: results
  };
}(window));
