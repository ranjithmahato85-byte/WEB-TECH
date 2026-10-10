<?php
$page_title = "Election Management - Admin Portal";
$section_title = "Election Management";
$section_subtitle = "Create, configure, and control electoral contests";
?>
<div>
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
    <div>
      <h3 style="font-size: 1.2rem; font-weight: 700; color: #0f172a;">All Contests</h3>
      <p style="font-size: 0.85rem; color: var(--text-muted);">Manage status, voting windows, and electoral jurisdictions</p>
    </div>

    <button class="btn btn-primary btn-sm" data-open-modal="modal-create-election">
      + Create New Election
    </button>
  </div>

  <!-- ELECTIONS TABLE -->
  <div class="table-card">
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Election Title</th>
            <th>Type</th>
            <th>Location</th>
            <th>Timeline</th>
            <th>Candidates</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($elections ?? []) as $el): ?>
          <tr>
            <td><code>#<?= (int)$el['id'] ?></code></td>
            <td>
              <strong style="color: #0f172a;"><?= htmlspecialchars($el['title']) ?></strong>
              <?php if (!empty($el['is_historical'])): ?>
                <span class="badge badge-completed" style="font-size: 0.7rem; margin-left: 4px;">Historical</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($el['election_type']) ?></td>
            <td><?= htmlspecialchars($el['location']) ?></td>
            <td style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($el['start_date']) ?> <?= htmlspecialchars($el['end_date']) ?></td>
            <td><strong><?= count($el['candidates'] ?? []) ?></strong></td>
            <td>
              <span class="badge badge-<?= strtolower(htmlspecialchars($el['status'])) ?>"><?= htmlspecialchars($el['status']) ?></span>
            </td>
            <td>
              <?php if (empty($el['is_historical'])): ?>
                <div style="display: flex; gap: 6px;">
                  <?php if ($el['status'] === 'Upcoming'): ?>
                    <form action="<?= url_for('admin_update_election_status', ['election_id' => $el['id']]) ?>" method="POST" style="display: inline;">
                      <?= csrf_field() ?>
                      <input type="hidden" name="status" value="Active">
                      <button type="submit" class="btn btn-outline btn-sm" style="color: #16a34a; border-color: #16a34a;">Activate</button>
                    </form>
                  <?php elseif ($el['status'] === 'Active'): ?>
                    <form action="<?= url_for('admin_update_election_status', ['election_id' => $el['id']]) ?>" method="POST" style="display: inline;">
                      <?= csrf_field() ?>
                      <input type="hidden" name="status" value="Completed">
                      <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #dc2626;">Close &amp; Certify</button>
                    </form>
                  <?php else: ?>
                    <form action="<?= url_for('admin_update_election_status', ['election_id' => $el['id']]) ?>" method="POST" style="display: inline;">
                      <?= csrf_field() ?>
                      <input type="hidden" name="status" value="Active">
                      <button type="submit" class="btn btn-outline btn-sm">Re-open</button>
                    </form>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <span style="font-size: 0.78rem; color: #94a3b8; font-style: italic;">Historical / Locked</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- CREATE ELECTION MODAL -->
<div class="modal-backdrop" id="modal-create-election">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="modal-title">Create New Election</h3>
      <button class="btn btn-outline btn-sm" data-close-modal style="padding: 4px 8px;">&times;</button>
    </div>

    <form action="<?= url_for('admin_create_election') ?>" method="POST">
      <?= csrf_field() ?>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label" for="title">Election Title <span class="required">*</span></label>
          <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Provincial Assembly By-Election 2026" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="description">Description <span class="required">*</span></label>
          <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief context on this electoral contest..." required></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="form-row">
          <div class="form-group">
            <label class="form-label" for="election_type">Election Type <span class="required">*</span></label>
            <select id="election_type" name="election_type" class="form-control" required>
              <option value="General Election">General Election (Federal)</option>
              <option value="Provincial Election">Provincial Election</option>
              <option value="Local Government">Local Government Election</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="location">Location / Constituency <span class="required">*</span></label>
            <input type="text" id="location" name="location" class="form-control" placeholder="e.g. Bagmati Province" required>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="form-row">
          <div class="form-group">
            <label class="form-label" for="start_date">Start Date <span class="required">*</span></label>
            <input type="date" id="start_date" name="start_date" class="form-control" value="2026-10-01" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="end_date">End Date <span class="required">*</span></label>
            <input type="date" id="end_date" name="end_date" class="form-control" value="2026-10-15" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="status">Initial Status <span class="required">*</span></label>
          <select id="status" name="status" class="form-control" required>
            <option value="Upcoming">Upcoming (Scheduled)</option>
            <option value="Active">Active (Live Voting Immediately)</option>
            <option value="Completed">Completed</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-close-modal>Cancel</button>
        <button type="submit" class="btn btn-primary">Save &amp; Launch Election</button>
      </div>
    </form>
  </div>
</div>
