(function (root) {
  'use strict';

  function client() {
    if (!root.VotingSupabase || !root.VotingSupabase.client) {
      throw new Error('Supabase is not configured on this page.');
    }
    return root.VotingSupabase.client;
  }

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function message(element, text, type) {
    element.textContent = text;
    element.className = 'auth-message is-visible is-' + type;
  }

  async function requireVoter() {
    const profile = await root.VotingAuth.getCurrentProfile();
    if (!profile) {
      window.location.href = '../login.html';
      throw new Error('Sign in to continue.');
    }
    if (!root.VotingAuth.isApprovedVoter(profile)) {
      throw new Error('Your voter account must be approved before you can vote.');
    }
    if (!profile.date_of_birth) {
      throw new Error('Your date of birth must be verified before you can vote.');
    }
    return profile;
  }

  async function loadElectionList() {
    const target = document.querySelector('[data-voter-elections]');
    const status = document.querySelector('[data-voter-message]');
    const profile = await requireVoter();
    const elections = await client()
      .from('public_election_directory')
      .select('*')
      .eq('status', 'active')
      .order('start_at', { ascending: true });
    if (elections.error) throw elections.error;

    const participation = await client()
      .from('voter_participation')
      .select('election_id')
      .eq('voter_id', profile.id);
    if (participation.error) throw participation.error;
    const voted = new Set(participation.data.map(function (item) { return item.election_id; }));

    target.textContent = '';
    if (!elections.data.length) {
      message(status, 'There are no active elections accepting votes.', 'success');
      return;
    }

    elections.data.forEach(function (election) {
      const card = document.createElement('article');
      card.className = 'public-card';
      const alreadyVoted = voted.has(election.id);
      card.innerHTML =
        '<div class="public-card-body"><h2>' + escapeHtml(election.title) + '</h2>' +
        '<p>' + escapeHtml(election.description || 'Review the approved candidates before voting.') + '</p>' +
        '<div class="public-meta">' + escapeHtml(election.election_type) + ' | ' + escapeHtml(election.location) + '</div>' +
        '<p class="public-meta">' + (alreadyVoted ? 'You have already voted in this election.' : 'One vote is permitted in this election.') + '</p>' +
        (alreadyVoted
          ? '<span class="public-meta">Participation recorded</span>'
          : '<a class="public-button" href="vote.html?election=' + encodeURIComponent(election.id) + '">Review ballot</a>') +
        '</div>';
      target.appendChild(card);
    });
  }

  async function loadBallot() {
    const target = document.querySelector('[data-ballot]');
    if (!target) return;
    const status = document.querySelector('[data-vote-message]');
    const electionId = new URLSearchParams(window.location.search).get('election');
    if (!electionId) {
      throw new Error('Select an election before opening a ballot.');
    }
    await requireVoter();
    const electionResponse = await client()
      .from('public_election_directory')
      .select('*')
      .eq('id', electionId)
      .eq('status', 'active')
      .maybeSingle();
    if (electionResponse.error) throw electionResponse.error;
    if (!electionResponse.data) throw new Error('This election is not available for voting.');

    const nominationResponse = await client()
      .from('public_candidate_directory')
      .select('nomination_id, full_name, party, position, symbol_name, biography')
      .eq('election_id', electionId)
      .eq('election_status', 'active')
      .order('full_name', { ascending: true });
    if (nominationResponse.error) throw nominationResponse.error;

    document.querySelector('[data-election-title]').textContent = electionResponse.data.title;
    target.textContent = '';
    nominationResponse.data.forEach(function (candidate) {
      const label = document.createElement('label');
      label.className = 'ballot-option';
      label.innerHTML =
        '<input type="radio" name="nomination" value="' + escapeHtml(candidate.nomination_id) + '">' +
        '<span><strong>' + escapeHtml(candidate.full_name) + '</strong><br>' +
        '<small>' + escapeHtml(candidate.party) + ' | ' + escapeHtml(candidate.position) + '</small><br>' +
        '<small>' + escapeHtml(candidate.symbol_name) + '</small></span>';
      target.appendChild(label);
    });

    document.querySelector('[data-cast-vote]').addEventListener('click', async function () {
      const selected = target.querySelector('input[name="nomination"]:checked');
      if (!selected) {
        message(status, 'Select one approved candidate before continuing.', 'error');
        return;
      }
      const confirmed = window.confirm('Confirm your selection. Your vote cannot be changed after submission.');
      if (!confirmed) return;
      const button = this;
      button.disabled = true;
      try {
        const response = await client().rpc('cast_vote', {
          requested_election_id: electionId,
          requested_nomination_id: selected.value
        });
        if (response.error) throw response.error;
        const receipt = response.data[0];
        message(status, 'Vote recorded. Your confirmation code is ' + receipt.confirmation_code + '.', 'success');
        target.querySelectorAll('input').forEach(function (input) { input.disabled = true; });
      } catch (error) {
        message(status, error.message || 'The vote could not be recorded.', 'error');
        button.disabled = false;
      }
    });
  }

  Promise.all([loadElectionList(), loadBallot()]).catch(function (error) {
    document.querySelectorAll('[data-voter-message], [data-vote-message]').forEach(function (element) {
      message(element, error.message || 'The voter area could not be loaded.', 'error');
    });
  });
}(window));
