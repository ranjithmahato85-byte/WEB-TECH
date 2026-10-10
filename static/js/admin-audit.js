(function (root) {
  'use strict';

  const target = document.querySelector('[data-audit-list]');
  const message = document.querySelector('[data-audit-message]');

  function showMessage(text, type) {
    message.textContent = text;
    message.className = 'auth-message is-visible is-' + type;
  }

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  async function init() {
    const profile = await root.VotingAuth.getCurrentProfile();
    if (!profile || profile.role !== 'admin' || !profile.is_active) throw new Error('Administrator access is required.');
    const response = await root.VotingSupabase.client
      .from('audit_logs')
      .select('id, actor_id, action, entity_type, entity_id, metadata, created_at')
      .order('created_at', { ascending: false })
      .limit(100);
    if (response.error) throw response.error;
    target.textContent = '';
    if (!response.data.length) {
      target.textContent = 'No audit events have been recorded.';
      return;
    }
    response.data.forEach(function (event) {
      const item = document.createElement('article');
      item.className = 'verification-item';
      item.innerHTML =
        '<h3>' + escapeHtml(event.action) + '</h3>' +
        '<p class="verification-meta">' + escapeHtml(event.entity_type) + ' | ' + escapeHtml(new Date(event.created_at).toLocaleString('en-NP')) + '</p>' +
        '<p class="auth-help">Actor: ' + escapeHtml(event.actor_id || 'system') + '<br>Entity: ' + escapeHtml(event.entity_id || 'none') + '</p>' +
        '<pre class="audit-json">' + escapeHtml(JSON.stringify(event.metadata || {}, null, 2)) + '</pre>';
      target.appendChild(item);
    });
  }

  init().catch(function (error) {
    showMessage(error.message || 'Could not load audit events.', 'error');
    target.textContent = '';
  });
}(window));
