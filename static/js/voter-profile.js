(function (root) {
  'use strict';

  const profileFields = {
    full_name: document.querySelector('[data-profile-full-name]'),
    date_of_birth: document.querySelector('[data-profile-dob]'),
    mobile: document.querySelector('[data-profile-mobile]'),
    province: document.querySelector('[data-profile-province]'),
    district: document.querySelector('[data-profile-district]'),
    municipality: document.querySelector('[data-profile-municipality]'),
    ward: document.querySelector('[data-profile-ward]')
  };
  const email = document.querySelector('[data-profile-email]');
  const status = document.querySelector('[data-profile-status]');
  const documents = document.querySelector('[data-profile-documents]');

  function setText(element, value) {
    if (element) element.textContent = value || 'Not provided';
  }

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function showStatus(profile) {
    if (!status) return;
    const approved = root.VotingAuth.isApprovedVoter(profile);
    status.className = 'portal-status' + (approved ? ' is-approved' : '');
    status.textContent = approved
      ? 'Approved voter account. You can access eligible active elections.'
      : 'Pending administrator verification. Voting remains unavailable until your identity is approved.';
  }

  function renderDocuments(items) {
    if (!documents) return;
    documents.textContent = '';
    if (!items.length) {
      documents.textContent = 'No citizenship document has been submitted.';
      return;
    }

    items.forEach(function (item) {
      const row = document.createElement('article');
      row.className = 'verification-item';
      const date = new Date(item.created_at).toLocaleDateString('en-NP');
      row.innerHTML = '<h3>' + escapeHtml(item.document_type === 'citizenship' ? 'Citizenship document' : 'Verification document') + '</h3>' +
        '<p class="verification-meta">Submitted ' + escapeHtml(date) + '</p>' +
        '<p><strong>Status:</strong> ' + escapeHtml(item.status) + '</p>' +
        (item.review_note ? '<p class="auth-help">Review note: ' + escapeHtml(item.review_note) + '</p>' : '');
      documents.appendChild(row);
    });
  }

  async function loadProfile() {
    const profile = await root.VotingAuth.getCurrentProfile();
    if (!profile) {
      window.location.href = '../login.html';
      return;
    }

    const session = await root.VotingAuth.getSession();
    setText(email, session && session.user ? session.user.email : '');
    Object.keys(profileFields).forEach(function (key) {
      setText(profileFields[key], profile[key]);
    });
    setText(document.querySelector('[data-profile-name]'), profile.full_name);
    setText(document.querySelector('[data-profile-verification]'), profile.verification_status);
    setText(document.querySelector('[data-profile-citizenship]'), profile.citizenship_verified ? 'Verified' : 'Pending review');
    showStatus(profile);
    renderDocuments(await root.VotingAuth.getMyDocuments());
  }

  loadProfile().catch(function (error) {
    if (status) {
      status.className = 'portal-status';
      status.textContent = error.message || 'Could not load your profile.';
    }
  });
}(window));
