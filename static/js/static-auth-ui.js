(function (root) {
  'use strict';

  function message(text, isError) {
    var element = document.getElementById('auth-message');
    if (!element) return;
    element.hidden = false;
    element.className = 'alert ' + (isError ? 'alert-danger' : 'alert-success');
    element.textContent = text;
  }

  function value(id) {
    var element = document.getElementById(id);
    return element ? element.value.trim() : '';
  }

  async function login(event) {
    event.preventDefault();
    try {
      var data = await root.VotingAuth.signIn(value('email'), value('password'));
      var profile = await root.VotingAuth.getCurrentProfile();
      message('Login successful. Redirecting...', false);
      window.setTimeout(function () {
        window.location.href = profile && profile.role === 'admin' ? './admin/index.html' : './voter/index.html';
      }, 250);
      return data;
    } catch (error) {
      message(error.message || 'Unable to sign in.', true);
    }
  }

  async function register(event) {
    event.preventDefault();
    if (value('password') !== value('confirm_password')) {
      message('Passwords do not match.', true);
      return;
    }
    try {
      await root.VotingAuth.signUp({
        email: value('email'),
        password: value('password'),
        fullName: value('full_name'),
        citizenshipNumber: value('citizenship_number'),
        dateOfBirth: value('date_of_birth'),
        province: value('province'),
        district: value('district'),
        municipality: value('municipality'),
        ward: value('ward')
      });
      message('Registration submitted. Check your email, then wait for voter and citizenship verification.', false);
      document.getElementById('register-form').reset();
    } catch (error) {
      message(error.message || 'Unable to complete registration.', true);
    }
  }

  async function resetPassword() {
    try {
      var email = value('email');
      if (!email) throw new Error('Enter your email address first.');
      var response = await root.VotingSupabase.client.auth.resetPasswordForEmail(email, { redirectTo: window.location.href });
      if (response.error) throw response.error;
      message('If the account exists, a password reset email has been sent.', false);
    } catch (error) {
      message(error.message || 'Unable to request a password reset.', true);
    }
  }

  var loginForm = document.getElementById('login-form');
  var registerForm = document.getElementById('register-form');
  if (loginForm) loginForm.addEventListener('submit', login);
  if (registerForm) registerForm.addEventListener('submit', register);
  var reset = document.getElementById('reset-password');
  if (reset) reset.addEventListener('click', resetPassword);

  var authActions = document.querySelector('[data-auth-actions]');
  if (authActions && root.VotingAuth) {
    root.VotingAuth.getCurrentProfile().then(function (profile) {
      if (!profile) return;
      var dashboard = profile.role === 'admin' ? './admin/index.html' : './voter/index.html';
      authActions.innerHTML = '<a href="' + dashboard + '" class="btn btn-outline-primary btn-sm">Dashboard</a><button type="button" class="btn btn-outline btn-sm" data-signout>Logout</button>';
      authActions.querySelector('[data-signout]').addEventListener('click', function () {
        root.VotingAuth.signOut().then(function () { window.location.reload(); });
      });
    }).catch(function () {});
  }
}(window));
