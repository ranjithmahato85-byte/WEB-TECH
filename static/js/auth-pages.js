(function (root) {
  'use strict';

  function showMessage(element, message, type) {
    element.textContent = message;
    element.className = 'auth-message is-visible is-' + type;
  }

  function setBusy(button, busy) {
    button.disabled = busy;
    button.textContent = busy ? 'Please wait...' : button.dataset.label;
  }

  function bindLogin() {
    const form = document.querySelector('[data-auth-form="login"]');
    if (!form) return;

    const message = form.querySelector('[data-auth-message]');
    const button = form.querySelector('button[type="submit"]');
    button.dataset.label = button.textContent;

    form.addEventListener('submit', async function (event) {
      event.preventDefault();
      message.className = 'auth-message';
      setBusy(button, true);

      try {
        const data = await root.VotingAuth.signIn(
          form.elements.email.value,
          form.elements.password.value
        );
        const profile = await root.VotingAuth.getCurrentProfile();
        const requestedNext = new URLSearchParams(window.location.search).get('next');
        const destination = requestedNext || (
          profile && profile.role === 'admin'
            ? '/pages/admin/dashboard.html'
            : '/pages/voter/dashboard.html'
        );

        showMessage(
          message,
          data.session
            ? 'Signed in successfully. Opening your portal...'
            : 'Sign-in completed, but no active session was returned.',
          data.session ? 'success' : 'error'
        );

        if (data.session) {
          window.setTimeout(function () {
            window.location.href = destination;
          }, 500);
        }
      } catch (error) {
        showMessage(message, error.message || 'Sign-in failed. Please try again.', 'error');
      } finally {
        setBusy(button, false);
      }
    });
  }

  function bindRegistration() {
    const form = document.querySelector('[data-auth-form="register"]');
    if (!form) return;

    const message = form.querySelector('[data-auth-message]');
    const button = form.querySelector('button[type="submit"]');
    button.dataset.label = button.textContent;

    form.addEventListener('submit', async function (event) {
      event.preventDefault();
      message.className = 'auth-message';

      if (form.elements.password.value !== form.elements.confirmPassword.value) {
        showMessage(message, 'Passwords do not match.', 'error');
        return;
      }

      setBusy(button, true);
      try {
        const data = await root.VotingAuth.signUp({
          fullName: form.elements.fullName.value,
          email: form.elements.email.value,
          password: form.elements.password.value,
          citizenshipNumber: form.elements.citizenshipNumber.value,
          dateOfBirth: form.elements.dateOfBirth.value,
          province: form.elements.province.value,
          district: form.elements.district.value,
          municipality: form.elements.municipality.value,
          ward: form.elements.ward.value
        });

        const emailConfirmationRequired = !data.session;
        showMessage(
          message,
          emailConfirmationRequired
            ? 'Registration received. Check your email, then wait for administrator verification before voting.'
            : 'Registration received. Your account is pending administrator verification.',
          'success'
        );
        form.reset();
      } catch (error) {
        showMessage(message, error.message || 'Registration failed. Please review your details.', 'error');
      } finally {
        setBusy(button, false);
      }
    });
  }

  function reportMissingAuth() {
    if (root.VotingAuth) return;
    document.querySelectorAll('[data-auth-message]').forEach(function (element) {
      showMessage(element, 'Supabase is not configured on this page. Copy supabase-config.example.js to supabase-config.js first.', 'error');
    });
  }

  bindLogin();
  bindRegistration();
  reportMissingAuth();
}(window));
