(function (root) {
  'use strict';

  const form = document.querySelector('[data-document-form]');
  const message = document.querySelector('[data-document-message]');
  if (!form) return;

  form.addEventListener('submit', async function (event) {
    event.preventDefault();
    const button = form.querySelector('button[type="submit"]');
    const file = form.elements.document.files[0];
    button.disabled = true;
    message.className = 'auth-message';

    try {
      await root.VotingAuth.submitCitizenshipDocument(file);
      message.textContent = 'Document submitted for administrator review.';
      message.className = 'auth-message is-visible is-success';
      form.reset();
    } catch (error) {
      message.textContent = error.message || 'Document upload failed.';
      message.className = 'auth-message is-visible is-error';
    } finally {
      button.disabled = false;
    }
  });
}(window));
