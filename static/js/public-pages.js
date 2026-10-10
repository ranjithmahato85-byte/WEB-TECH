(function (root) {
  'use strict';

  function client() {
    if (!root.VotingSupabase || !root.VotingSupabase.client) {
      throw new Error('Supabase is not configured. Add static/js/supabase-config.js.');
    }
    return root.VotingSupabase.client;
  }

  function formatDate(value) {
    return new Intl.DateTimeFormat('en-NP', { dateStyle: 'medium' }).format(new Date(value));
  }

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function imageUrl(value) {
    return value ? '../../static/images/' + encodeURIComponent(value) : '../../static/images/nepal-hero.jpg';
  }

  function setMessage(element, text) {
    element.textContent = text;
    element.className = 'public-message';
  }

  async function loadElections() {
    const target = document.querySelector('[data-elections]');
    if (!target) return;
    const response = await client().from('public_election_directory').select('*').order('start_at', { ascending: true });
    if (response.error) throw response.error;
    target.textContent = '';
    if (!response.data.length) {
      setMessage(target, 'No public elections are available yet.');
      return;
    }
    response.data.forEach(function (election) {
      const card = document.createElement('article');
      card.className = 'public-card';
      card.innerHTML =
        '<img class="public-card-image" src="' + escapeHtml(imageUrl(election.image_url)) + '" alt="">' +
        '<div class="public-card-body"><h2>' + escapeHtml(election.title) + '</h2>' +
        '<p>' + escapeHtml(election.description || 'Election information will be published by the Election Commission.') + '</p>' +
        '<div class="public-meta">' + escapeHtml(election.election_type) + ' | ' + escapeHtml(election.location) + '</div>' +
        '<div class="public-meta">' + escapeHtml(formatDate(election.start_at)) + ' to ' + escapeHtml(formatDate(election.end_at)) + '</div>' +
        '<p class="public-meta">' + escapeHtml(election.approved_candidate_count) + ' approved candidates</p>' +
        '<a class="public-button" href="candidates.html?election=' + encodeURIComponent(election.id) + '">View candidates</a></div>';
      target.appendChild(card);
    });
  }

  async function loadCandidates() {
    const target = document.querySelector('[data-candidates]');
    if (!target) return;
    const response = await client().from('public_candidate_directory').select('*').order('full_name', { ascending: true });
    if (response.error) throw response.error;
    target.textContent = '';
    response.data.forEach(function (candidate) {
      const card = document.createElement('article');
      card.className = 'public-card';
      card.innerHTML =
        '<div class="public-card-body"><h2>' + escapeHtml(candidate.full_name) + '</h2>' +
        '<p>' + escapeHtml(candidate.party) + ' | ' + escapeHtml(candidate.position) + '</p>' +
        '<div class="public-meta">' + escapeHtml(candidate.election_title) + '</div>' +
        '<p>' + escapeHtml(candidate.biography || 'No biography published.') + '</p>' +
        '<div class="public-meta">Symbol: ' + escapeHtml(candidate.symbol_name) + '</div></div>';
      target.appendChild(card);
    });
    const search = document.querySelector('[data-candidate-search]');
    if (search) search.addEventListener('input', function () {
      const term = search.value.toLowerCase().trim();
      target.querySelectorAll('.public-card').forEach(function (card) {
        card.hidden = !card.textContent.toLowerCase().includes(term);
      });
    });
  }

  async function loadResults() {
    const target = document.querySelector('[data-results]');
    if (!target) return;
    const electionId = new URLSearchParams(window.location.search).get('election');
    if (!electionId) {
      setMessage(target, 'Choose an election to view certified results.');
      return;
    }
    const response = await client().rpc('election_results', { requested_election_id: electionId });
    if (response.error) throw response.error;
    target.textContent = '';
    if (!response.data.length) {
      setMessage(target, 'Results are not certified or no ballots have been counted.');
      return;
    }
    response.data.forEach(function (result, index) {
      const row = document.createElement('div');
      row.className = 'result-row';
      row.innerHTML = '<strong>' + (index + 1) + '. ' + escapeHtml(result.candidate_name) + '</strong><br><span>' + escapeHtml(result.vote_total) + ' votes</span>';
      target.appendChild(row);
    });
  }

  Promise.all([loadElections(), loadCandidates(), loadResults()]).catch(function (error) {
    document.querySelectorAll('[data-public-error]').forEach(function (element) {
      setMessage(element, error.message || 'Public election data could not be loaded.');
    });
  });
}(window));
