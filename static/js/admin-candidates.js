(function (root) {
  'use strict';

  const candidateForm = document.querySelector('[data-candidate-form]');
  const nominationForm = document.querySelector('[data-nomination-form]');
  const candidateList = document.querySelector('[data-candidate-list]');
  const nominationList = document.querySelector('[data-nomination-list]');
  const message = document.querySelector('[data-candidate-message]');

  function showMessage(text, type) {
    message.textContent = text;
    message.className = 'auth-message is-visible is-' + type;
  }

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  async function loadData() {
    const candidates = await root.VotingSupabase.client
      .from('candidate_profiles')
      .select('id, full_name, citizenship_number, biography')
      .order('full_name', { ascending: true });
    if (candidates.error) throw candidates.error;
    candidateList.textContent = '';
    nominationForm.elements.candidateId.innerHTML = '<option value="">Select candidate</option>';
    candidates.data.forEach(function (candidate) {
      const option = document.createElement('option');
      option.value = candidate.id;
      option.textContent = candidate.full_name + ' (' + candidate.citizenship_number + ')';
      nominationForm.elements.candidateId.appendChild(option);
      const item = document.createElement('article');
      item.className = 'verification-item';
      item.innerHTML = '<h3>' + escapeHtml(candidate.full_name) + '</h3><p class="verification-meta">Citizenship identity: ' + escapeHtml(candidate.citizenship_number) + '</p><p>' + escapeHtml(candidate.biography || 'No biography supplied.') + '</p>';
      candidateList.appendChild(item);
    });

    const elections = await root.VotingSupabase.client
      .from('elections')
      .select('id, title')
      .in('status', ['draft', 'scheduled']);
    if (elections.error) throw elections.error;
    nominationForm.elements.electionId.innerHTML = '<option value="">Select election</option>';
    elections.data.forEach(function (election) {
      const option = document.createElement('option');
      option.value = election.id;
      option.textContent = election.title;
      nominationForm.elements.electionId.appendChild(option);
    });

    const nominations = await root.VotingSupabase.client
      .from('nominations')
      .select('id, party, position, symbol_name, status, elections(title), candidate_profiles(full_name)')
      .order('created_at', { ascending: false });
    if (nominations.error) throw nominations.error;
    nominationList.textContent = '';
    nominations.data.forEach(function (nomination) {
      const item = document.createElement('article');
      item.className = 'verification-item';
      item.innerHTML =
        '<h3>' + escapeHtml(nomination.candidate_profiles.full_name) + '</h3>' +
        '<p class="verification-meta">' + escapeHtml(nomination.elections.title) + ' | ' + escapeHtml(nomination.party) + ' | ' + escapeHtml(nomination.position) + '</p>' +
        '<p>Status: ' + escapeHtml(nomination.status) + ' | Symbol: ' + escapeHtml(nomination.symbol_name) + '</p>' +
        '<div class="verification-controls">' +
        '<button type="button" data-nomination="' + escapeHtml(nomination.id) + '" data-status="approved">Approve nomination</button>' +
        '<button type="button" data-nomination="' + escapeHtml(nomination.id) + '" data-status="rejected">Reject nomination</button>' +
        '</div>';
      item.querySelectorAll('button').forEach(function (button) {
        button.addEventListener('click', function () {
          updateNomination(button.dataset.nomination, button.dataset.status);
        });
      });
      nominationList.appendChild(item);
    });
  }

  candidateForm.addEventListener('submit', async function (event) {
    event.preventDefault();
    const profile = await root.VotingAuth.getCurrentProfile();
    try {
      const response = await root.VotingSupabase.client.from('candidate_profiles').insert({
        citizenship_number: candidateForm.elements.citizenshipNumber.value.trim(),
        full_name: candidateForm.elements.fullName.value.trim(),
        biography: candidateForm.elements.biography.value.trim(),
        created_by: profile.id
      });
      if (response.error) throw response.error;
      candidateForm.reset();
      await loadData();
      showMessage('Candidate identity created. The citizenship number is now reserved.', 'success');
    } catch (error) {
      showMessage(error.message || 'Candidate identity could not be created.', 'error');
    }
  });

  nominationForm.addEventListener('submit', async function (event) {
    event.preventDefault();
    const profile = await root.VotingAuth.getCurrentProfile();
    try {
      const response = await root.VotingSupabase.client.from('nominations').insert({
        election_id: nominationForm.elements.electionId.value,
        candidate_id: nominationForm.elements.candidateId.value,
        party: nominationForm.elements.party.value.trim(),
        position: nominationForm.elements.position.value.trim(),
        symbol_name: nominationForm.elements.symbolName.value.trim(),
        status: 'pending',
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString(),
        reviewed_by: null,
        reviewed_at: null,
        review_note: null
      });
      if (response.error) throw response.error;
      nominationForm.reset();
      await loadData();
      showMessage('Nomination submitted for review.', 'success');
    } catch (error) {
      showMessage(error.message || 'Nomination could not be created.', 'error');
    }
  });

  async function updateNomination(id, status) {
    try {
      const response = await root.VotingSupabase.client.rpc('review_nomination', {
        nomination_id: id,
        decision: status,
        note: null
      });
      if (response.error) throw response.error;
      await loadData();
      showMessage('Nomination status updated.', 'success');
    } catch (error) {
      showMessage(error.message || 'Nomination status could not be updated.', 'error');
    }
  }

  async function init() {
    const profile = await root.VotingAuth.getCurrentProfile();
    if (!profile || profile.role !== 'admin' || !profile.is_active) throw new Error('Administrator access is required.');
    await loadData();
  }

  init().catch(function (error) {
    showMessage(error.message || 'Could not load candidate management.', 'error');
  });
}(window));
