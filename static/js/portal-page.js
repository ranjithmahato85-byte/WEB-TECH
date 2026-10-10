(function (root) {
  'use strict';

  const nameElement = document.querySelector('[data-profile-name]');
  const statusElement = document.querySelector('[data-profile-status]');
  const signOutButton = document.querySelector('[data-sign-out]');
  const requiredRole = document.body.dataset.requiredRole;

  function setStatus(profile) {
    if (profile.role === 'admin') {
      statusElement.className = 'portal-status is-approved';
      statusElement.textContent = 'Administrator access is active.';
      return;
    }

    const approved = root.VotingAuth.isApprovedVoter(profile);
    statusElement.className = 'portal-status' + (approved ? ' is-approved' : '');
    statusElement.textContent = approved
      ? 'Your account is verified. Election access can be enabled for eligible active elections.'
      : 'Your account is pending administrator verification. You cannot vote until your identity and citizenship details are approved.';
  }

  async function loadProfile() {
    if (!root.VotingAuth) {
      throw new Error('Supabase is not configured on this page.');
    }

    const profile = await root.VotingAuth.getCurrentProfile();
    if (!profile) {
      window.location.href = '../login.html';
      return;
    }

    if (requiredRole && profile.role !== requiredRole) {
      throw new Error('This portal is not available for your account role.');
    }

    nameElement.textContent = profile.full_name;
    setStatus(profile);
  }

  if (signOutButton) {
    signOutButton.addEventListener('click', async function () {
      signOutButton.disabled = true;
      try {
        await root.VotingAuth.signOut();
        window.location.href = '../auth/login.html';
      } catch (error) {
        signOutButton.disabled = false;
        statusElement.className = 'portal-status';
        statusElement.textContent = error.message || 'Sign out failed.';
      }
    });
  }

  loadProfile().catch(function (error) {
    statusElement.className = 'portal-status';
    statusElement.textContent = error.message || 'Could not load your account.';
  });
}(window));
