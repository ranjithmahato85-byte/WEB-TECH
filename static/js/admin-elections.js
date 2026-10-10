(function (root) {
  'use strict';

  const list = document.querySelector('[data-election-admin-list]');
  const form = document.querySelector('[data-election-form]');
  const message = document.querySelector('[data-election-admin-message]');

  function showMessage(text, type) {
    message.textContent = text;
    message.className = 'auth-message is-visible is-' + type;
  }

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  async function loadElections() {
    const response = await root.VotingSupabase.client
      .from('elections')
      .select('id, title, election_type, location, start_at, end_at, status')
      .order('start_at', { ascending: false });
    if (response.error) throw response.error;
    list.textContent = '';
    if (!response.data.length) {
      list.textContent = 'No elections have been created.';
      return;
    }
    response.data.forEach(function (election) {
      const item = document.createElement('article');
      item.className = 'verification-item';
      item.innerHTML =
        '<h3>' + escapeHtml(election.title) + '</h3>' +
        '<p class="verification-meta">' + escapeHtml(election.election_type) + ' | ' + escapeHtml(election.location) + '</p>' +
        '<p class="auth-help">Status: ' + escapeHtml(election.status) + '<br>' +
        'Voting period: ' + escapeHtml(new Date(election.start_at).toLocaleString('en-NP')) + ' to ' + escapeHtml(new Date(election.end_at).toLocaleString('en-NP')) + '</p>' +
        '<div class="verification-controls">' +
        '<button type="button" data-election-id="' + escapeHtml(election.id) + '" data-election-status="scheduled">Schedule</button>' +
        '<button type="button" data-election-id="' + escapeHtml(election.id) + '" data-election-status="active">Activate</button>' +
        '<button type="button" data-election-id="' + escapeHtml(election.id) + '" data-election-status="closed">Close</button>' +
        '<button type="button" data-election-id="' + escapeHtml(election.id) + '" data-election-status="certified">Certify</button>' +
        '<button type="button" data-election-id="' + escapeHtml(election.id) + '" data-election-status="archived">Archive</button>' +
        '</div>';
      item.querySelectorAll('button').forEach(function (button) {
        button.addEventListener('click', function () {
          updateStatus(button.dataset.electionId, button.dataset.electionStatus);
        });
      });
      list.appendChild(item);
    });
  }

  async function updateStatus(id, status) {
    try {
      const response = await root.VotingSupabase.client.rpc('change_election_status', {
        p_election_id: id,
        p_next_status: status
      });
      if (response.error) throw response.error;
      await loadElections();
      showMessage('Election status updated.', 'success');
    } catch (error) {
      showMessage(error.message || 'Election status could not be updated.', 'error');
    }
  }

  form.addEventListener('submit', async function (event) {
    event.preventDefault();
    const submit = form.querySelector('button[type="submit"]');
    submit.disabled = true;
    try {
      const profile = await root.VotingAuth.getCurrentProfile();
      const response = await root.VotingSupabase.client.from('elections').insert({
        title: form.elements.title.value.trim(),
        description: form.elements.description.value.trim(),
        election_type: form.elements.electionType.value.trim(),
        location: form.elements.location.value.trim(),
        start_at: new Date(form.elements.startAt.value).toISOString(),
        end_at: new Date(form.elements.endAt.value).toISOString(),
        status: 'draft',
        created_by: profile.id
      });
      if (response.error) throw response.error;
      form.reset();
      await loadElections();
      showMessage('Draft election created.', 'success');
    } catch (error) {
      showMessage(error.message || 'Election could not be created.', 'error');
    } finally {
      submit.disabled = false;
    }
  });

  async function init() {
    const profile = await root.VotingAuth.getCurrentProfile();
    if (!profile || profile.role !== 'admin' || !profile.is_active) {
      throw new Error('Administrator access is required.');
    }
    await loadElections();
  }

  init().catch(function (error) {
    showMessage(error.message || 'Could not load elections.', 'error');
    list.textContent = '';
  });
}(window));
