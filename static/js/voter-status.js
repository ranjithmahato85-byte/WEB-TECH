(function (root) {
  'use strict';

  const target = document.querySelector('[data-participation-list]');
  const message = document.querySelector('[data-status-message]');

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function showMessage(text, type) {
    message.textContent = text;
    message.className = 'auth-message is-visible is-' + type;
  }

  async function loadStatus() {
    if (!root.VotingAuth || !root.VotingSupabase) {
      throw new Error('Supabase is not configured on this page.');
    }

    const profile = await root.VotingAuth.getCurrentProfile();
    if (!profile) {
      window.location.href = '../login.html';
      return;
    }

    const participation = await root.VotingAuth.getMyParticipation();
    const electionIds = participation.map(function (item) { return item.election_id; });
    let elections = [];

    if (electionIds.length) {
      const response = await root.VotingSupabase.client
        .from('public_election_directory')
        .select('id, title, election_type, location')
        .in('id', electionIds);
      if (response.error) throw response.error;
      elections = response.data;
    }

    const names = new Map(elections.map(function (election) {
      return [election.id, election];
    }));

    target.textContent = '';
    if (!participation.length) {
      showMessage('No voting participation has been recorded for this account.', 'success');
      return;
    }

    participation.forEach(function (record) {
      const election = names.get(record.election_id);
      const item = document.createElement('article');
      item.className = 'verification-item';
      item.innerHTML =
        '<h3>' + escapeHtml(election ? election.title : 'Election') + '</h3>' +
        '<p class="verification-meta">' + escapeHtml(election ? election.election_type + ' | ' + election.location : 'Election details unavailable') + '</p>' +
        '<p><strong>Participation recorded</strong><br>Time: ' + escapeHtml(new Date(record.voted_at).toLocaleString('en-NP')) + '</p>' +
        '<p class="auth-help">Confirmation code: <code>' + escapeHtml(record.confirmation_code) + '</code></p>';
      target.appendChild(item);
    });
  }

  loadStatus().catch(function (error) {
    showMessage(error.message || 'Could not load voting status.', 'error');
    target.textContent = '';
  });
}(window));
