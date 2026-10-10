(function (root) {
  'use strict';

  const electionSelect = document.querySelector('[data-results-election]');
  const resultsTarget = document.querySelector('[data-admin-results]');
  const message = document.querySelector('[data-results-message]');
  const printButton = document.querySelector('[data-print-results]');

  function showMessage(text, type) {
    message.textContent = text;
    message.className = 'auth-message is-visible is-' + type;
  }

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  async function requireAdmin() {
    const profile = await root.VotingAuth.getCurrentProfile();
    if (!profile || profile.role !== 'admin' || !profile.is_active) {
      throw new Error('Administrator access is required.');
    }
  }

  async function loadElections() {
    const response = await root.VotingSupabase.client
      .from('elections')
      .select('id, title, status, start_at, end_at')
      .in('status', ['closed', 'certified', 'archived'])
      .order('end_at', { ascending: false });
    if (response.error) throw response.error;
    electionSelect.innerHTML = '<option value="">Select a closed or certified election</option>';
    response.data.forEach(function (election) {
      const option = document.createElement('option');
      option.value = election.id;
      option.textContent = election.title + ' (' + election.status + ')';
      electionSelect.appendChild(option);
    });
  }

  async function loadResults(electionId) {
    if (!electionId) {
      resultsTarget.textContent = 'Select an election to view certified results.';
      return;
    }
    resultsTarget.textContent = 'Loading certified results...';
    const response = await root.VotingSupabase.client.rpc('election_results', {
      requested_election_id: electionId
    });
    if (response.error) throw response.error;
    const records = response.data || [];
    if (!records.length) {
      resultsTarget.textContent = 'No certified results or counted ballots are available.';
      return;
    }
    const total = records.reduce(function (sum, record) {
      return sum + Number(record.vote_total || 0);
    }, 0);
    resultsTarget.innerHTML = '<div class="results-summary"><strong>Total ballots:</strong> ' + total + '</div>';
    records.forEach(function (record, index) {
      const percentage = total ? ((Number(record.vote_total || 0) / total) * 100).toFixed(2) : '0.00';
      const row = document.createElement('div');
      row.className = 'result-row';
      row.innerHTML = '<strong>' + (index + 1) + '. ' + escapeHtml(record.candidate_name) + '</strong>' +
        '<span class="result-votes">' + escapeHtml(record.vote_total) + ' votes (' + percentage + '%)</span>';
      resultsTarget.appendChild(row);
    });
  }

  async function init() {
    await requireAdmin();
    await loadElections();
    electionSelect.addEventListener('change', function () {
      loadResults(electionSelect.value).catch(function (error) {
        showMessage(error.message || 'Results could not be loaded.', 'error');
      });
    });
    printButton.addEventListener('click', function () {
      if (!electionSelect.value) {
        showMessage('Select an election before printing results.', 'error');
        return;
      }
      window.print();
    });
  }

  init().catch(function (error) {
    showMessage(error.message || 'Could not load admin results.', 'error');
    resultsTarget.textContent = '';
  });
}(window));
