(function (root) {
  'use strict';

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function date(value) {
    if (!value) return 'Date not published';
    return new Intl.DateTimeFormat('en-NP', { dateStyle: 'medium' }).format(new Date(value));
  }

  function image(value) {
    return value ? '../static/images/' + encodeURIComponent(value) : '../static/images/parliament.jpg';
  }

  function setMessage(target, text, error) {
    target.textContent = text;
    target.className = error ? 'public-message public-message-error' : 'public-message';
  }

  function status(value) {
    return String(value || '').replace(/^\w/, function (letter) { return letter.toUpperCase(); });
  }

  async function loadElections() {
    var target = document.querySelector('[data-elections]');
    if (!target) return;
    try {
      var records = await root.VotingPublicApi.elections();
      target.innerHTML = '';
      if (!records.length) {
        setMessage(target, 'No public elections are available yet.', false);
        return;
      }
      records.forEach(function (election) {
        var card = document.createElement('article');
        card.className = 'election-card';
        card.innerHTML =
          '<div class="election-card-image"><img src="' + escapeHtml(image(election.image_url)) + '" alt=""></div>' +
          '<div class="election-card-body"><div class="election-card-title">' + escapeHtml(election.title) + '</div>' +
          '<div class="election-meta"><div class="election-meta-item">Status: ' + escapeHtml(status(election.status)) + '</div>' +
          '<div class="election-meta-item">' + escapeHtml(election.election_type) + ' | ' + escapeHtml(election.location) + '</div>' +
          '<div class="election-meta-item">' + escapeHtml(date(election.start_at)) + ' to ' + escapeHtml(date(election.end_at)) + '</div>' +
          '<div class="election-meta-item">' + escapeHtml(election.approved_candidate_count) + ' approved candidates</div></div>' +
          '<p class="election-description">' + escapeHtml(election.description || 'Election information will be published by the Election Commission.') + '</p>' +
          '<div class="election-card-footer"><a class="btn btn-outline-primary btn-sm" href="./candidates.html?election=' + encodeURIComponent(election.id) + '">View candidates</a>' +
          (['closed', 'certified', 'archived'].indexOf(election.status) >= 0 ? '<a class="btn btn-outline btn-sm" href="./results.html?election=' + encodeURIComponent(election.id) + '">View results</a>' : '') +
          '</div></div>';
        target.appendChild(card);
      });
    } catch (error) {
      setMessage(target, error.message || 'Election information could not be loaded.', true);
    }
  }

  async function loadCandidates() {
    var target = document.querySelector('[data-candidates]');
    if (!target) return;
    try {
      var records = await root.VotingPublicApi.candidates();
      target.innerHTML = '';
      if (!records.length) {
        setMessage(target, 'No approved candidates are available yet.', false);
        return;
      }
      records.forEach(function (candidate) {
        var card = document.createElement('article');
        card.className = 'candidate-card';
        card.dataset.election = candidate.election_id;
        card.innerHTML =
          '<div class="candidate-photo-wrapper"><img class="candidate-photo" src="' + escapeHtml(candidate.photo_url || '../static/images/candidates/voter-avatar.svg') + '" alt=""></div>' +
          '<h3 class="candidate-name">' + escapeHtml(candidate.full_name) + '</h3>' +
          '<p class="candidate-party">' + escapeHtml(candidate.party) + '</p>' +
          '<p class="candidate-election">' + escapeHtml(candidate.election_title) + '</p>' +
          '<p class="candidate-bio">' + escapeHtml(candidate.biography || 'No biography published.') + '</p>' +
          '<div class="candidate-symbol">' + escapeHtml(candidate.symbol_name || 'Symbol pending') + '</div>';
        target.appendChild(card);
      });
      var election = new URLSearchParams(window.location.search).get('election');
      var search = document.querySelector('[data-candidate-search]');
      function filter() {
        var term = (search ? search.value : '').toLowerCase().trim();
        target.querySelectorAll('.candidate-card').forEach(function (card) {
          var matchesElection = !election || card.dataset.election === election;
          card.hidden = !matchesElection || !card.textContent.toLowerCase().includes(term);
        });
      }
      if (search) search.addEventListener('input', filter);
      filter();
    } catch (error) {
      setMessage(target, error.message || 'Candidate information could not be loaded.', true);
    }
  }

  async function loadResults() {
    var target = document.querySelector('[data-results]');
    if (!target) return;
    var electionId = new URLSearchParams(window.location.search).get('election');
    if (!electionId) {
      setMessage(target, 'Choose a closed or certified election to view results.', false);
      return;
    }
    try {
      var records = await root.VotingPublicApi.results(electionId);
      target.innerHTML = '';
      if (!records.length) {
        setMessage(target, 'Results are not certified or no ballots have been counted.', false);
        return;
      }
      var total = records.reduce(function (sum, record) { return sum + Number(record.vote_total || 0); }, 0);
      records.forEach(function (result, index) {
        var percentage = total ? ((Number(result.vote_total || 0) / total) * 100).toFixed(2) : '0.00';
        var row = document.createElement('div');
        row.className = 'result-row';
        row.innerHTML = '<strong>' + (index + 1) + '. ' + escapeHtml(result.candidate_name) + '</strong>' +
          '<span class="result-votes">' + escapeHtml(result.vote_total) + ' votes (' + percentage + '%)</span>';
        target.appendChild(row);
      });
    } catch (error) {
      setMessage(target, error.message || 'Results could not be loaded.', true);
    }
  }

  loadElections();
  loadCandidates();
  loadResults();
}(window));
