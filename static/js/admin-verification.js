(function (root) {
  'use strict';

  const queue = document.querySelector('[data-voter-queue]');
  const message = document.querySelector('[data-admin-message]');
  const template = document.querySelector('[data-voter-row-template]');
  const documentQueue = document.querySelector('[data-document-queue]');
  const documentTemplate = document.querySelector('[data-document-row-template]');

  function showMessage(text, type) {
    message.textContent = text;
    message.className = 'auth-message is-visible is-' + type;
  }

  function escapeText(value) {
    return value == null ? '' : String(value);
  }

  function statusLabel(status) {
    return status.charAt(0).toUpperCase() + status.slice(1);
  }

  function renderVoter(voter) {
    const row = template.content.cloneNode(true);
    row.querySelector('[data-voter-name]').textContent = escapeText(voter.full_name);
    row.querySelector('[data-voter-location]').textContent = [
      voter.district,
      voter.municipality,
      voter.ward ? 'Ward ' + voter.ward : ''
    ].filter(Boolean).join(', ') || 'Location not supplied';
    row.querySelector('[data-voter-status]').textContent = statusLabel(voter.verification_status);
    row.querySelector('[data-voter-citizenship]').textContent = voter.citizenship_verified
      ? 'Citizenship reviewed'
      : 'Citizenship review pending';

    const note = row.querySelector('[data-voter-note]');
    const controls = row.querySelector('[data-voter-controls]');
    controls.querySelectorAll('button').forEach(function (button) {
      button.addEventListener('click', async function () {
        controls.querySelectorAll('button').forEach(function (item) {
          item.disabled = true;
        });
        try {
          await root.VotingAuth.reviewVoter(voter.id, button.dataset.status, note.value);
          await loadQueue();
          showMessage('Voter review saved and recorded in the audit log.', 'success');
        } catch (error) {
          controls.querySelectorAll('button').forEach(function (item) {
            item.disabled = false;
          });
          showMessage(error.message || 'The voter review could not be saved.', 'error');
        }
      });
    });

    return row;
  }

  async function loadQueue() {
    queue.textContent = 'Loading voter applications...';
    const response = await root.VotingSupabase.client
      .from('profiles')
      .select('id, full_name, date_of_birth, district, municipality, ward, citizenship_number, verification_status, citizenship_verified, is_active, verification_note, created_at')
      .eq('role', 'voter')
      .order('created_at', { ascending: true });

    if (response.error) {
      throw response.error;
    }

    queue.textContent = '';
    if (!response.data.length) {
      queue.textContent = 'No voter applications are waiting for review.';
      return;
    }

    response.data.forEach(function (voter) {
      queue.appendChild(renderVoter(voter));
    });
  }

  async function renderDocuments() {
    if (!documentQueue || !documentTemplate) return;
    const response = await root.VotingSupabase.client
      .from('verification_documents')
      .select('id, voter_id, storage_path, status, review_note, created_at, profiles!verification_documents_voter_id_fkey(full_name)')
      .eq('status', 'pending')
      .order('created_at', { ascending: true });

    if (response.error) {
      throw response.error;
    }

    documentQueue.textContent = '';
    if (!response.data.length) {
      documentQueue.textContent = 'No citizenship documents are waiting for review.';
      return;
    }

    response.data.forEach(function (document) {
      const row = documentTemplate.content.cloneNode(true);
      row.querySelector('[data-document-voter]').textContent = document.profiles.full_name;
      const preview = row.querySelector('[data-document-preview]');
      const controls = row.querySelector('[data-document-controls]');
      controls.querySelectorAll('button').forEach(function (button) {
        button.addEventListener('click', async function () {
          controls.querySelectorAll('button').forEach(function (item) {
            item.disabled = true;
          });
          try {
            await root.VotingAuth.reviewDocument(
              document.id,
              button.dataset.status,
              row.querySelector('[data-document-note]').value
            );
            await renderDocuments();
            showMessage('Document review saved and recorded in the audit log.', 'success');
          } catch (error) {
            controls.querySelectorAll('button').forEach(function (item) {
              item.disabled = false;
            });
            showMessage(error.message || 'The document review could not be saved.', 'error');
          }
        });
      });
      preview.addEventListener('click', async function () {
        preview.disabled = true;
        try {
          const signed = await root.VotingSupabase.client.storage
            .from('citizenship-documents')
            .createSignedUrl(document.storage_path, 300);
          if (signed.error) throw signed.error;
          window.open(signed.data.signedUrl, '_blank', 'noopener,noreferrer');
        } catch (error) {
          showMessage(error.message || 'Could not open the private document preview.', 'error');
        } finally {
          preview.disabled = false;
        }
      });
      documentQueue.appendChild(row);
    });
  }

  async function init() {
    if (!root.VotingAuth || !root.VotingSupabase) {
      throw new Error('Supabase is not configured on this page.');
    }
    const profile = await root.VotingAuth.getCurrentProfile();
    if (!profile || profile.role !== 'admin' || !profile.is_active) {
      throw new Error('Administrator access is required.');
    }
    await loadQueue();
    await renderDocuments();
  }

  init().catch(function (error) {
    showMessage(error.message || 'Could not load voter applications.', 'error');
    queue.textContent = '';
  });
}(window));
