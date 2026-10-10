<?php
$page_title = "Candidate Management - Admin Portal";
$section_title = "Candidate Management";
$section_subtitle = "Review candidate nominations, party tickets, and symbols";
?>
<div>
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
    <div>
      <h3 style="font-size: 1.2rem; font-weight: 700; color: #0f172a;">Official Nominees</h3>
      <p style="font-size: 0.85rem; color: var(--text-muted);">Manage candidate profiles across all active and scheduled contests</p>
    </div>

    <button class="btn btn-primary btn-sm" data-open-modal="modal-create-candidate">
      + Add New Candidate
    </button>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Candidate</th>
            <th>Party</th>
            <th>Election Contest</th>
            <th>Position</th>
            <th>Symbol</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($candidates ?? []) as $cand): ?>
          <tr>
            <td><code>#<?= (int)$cand['id'] ?></code></td>
            <td>
              <div style="display: flex; align-items: center; gap: 10px;">
                <img src="/static/images/candidates/<?= htmlspecialchars($cand['photo_url']) ?>" alt="" style="width: 32px; height: 32px; border-radius: 50%;">
                <strong style="color: #0f172a;"><?= htmlspecialchars($cand['name']) ?></strong>
              </div>
            </td>
            <td>
              <span class="badge-pill-soft" style="font-size: 0.75rem;"><?= htmlspecialchars($cand['party']) ?></span>
            </td>
            <td><?= htmlspecialchars($cand['election_title'] ?? '') ?></td>
            <td><?= htmlspecialchars($cand['position']) ?></td>
            <td>
              <div style="display: flex; align-items: center; gap: 6px;">
                <img src="/static/images/<?= htmlspecialchars($cand['symbol_image']) ?>" alt="" style="width: 20px; height: 20px;">
                <span style="font-size: 0.8rem; font-weight: 600;"><?= htmlspecialchars($cand['symbol_name']) ?></span>
              </div>
            </td>
            <td>
              <div style="display: flex; gap: 6px;">
                <a href="<?= url_for('candidate_profile', ['candidate_id' => $cand['id']]) ?>" class="btn btn-outline btn-sm" target="_blank">
                  View
                </a>
                <form action="<?= url_for('admin_delete_candidate', ['candidate_id' => $cand['id']]) ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this candidate?');" style="display: inline;">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #dc2626;">
                    Delete
                  </button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ADD CANDIDATE MODAL -->
<div class="modal-backdrop" id="modal-create-candidate">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="modal-title">Add Official Candidate</h3>
      <button class="btn btn-outline btn-sm" data-close-modal style="padding: 4px 8px;">&times;</button>
    </div>

    <form action="<?= url_for('admin_create_candidate') ?>" method="POST">
      <?= csrf_field() ?>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label" for="cand_name">Candidate Full Name <span class="required">*</span></label>
          <input type="text" id="cand_name" name="name" class="form-control" placeholder="e.g. Maya Devi Shrestha" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="form-row">
          <div class="form-group">
            <label class="form-label" for="cand_party">Political Party <span class="required">*</span></label>
            <input type="text" id="cand_party" name="party" class="form-control" placeholder="e.g. Nepali Congress / Independent" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="cand_election_id">Contesting Election <span class="required">*</span></label>
            <select id="cand_election_id" name="election_id" class="form-control" required>
              <?php foreach (($elections ?? []) as $el): ?>
                <option value="<?= (int)$el['id'] ?>"><?= htmlspecialchars($el['title']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="form-row">
          <div class="form-group">
            <label class="form-label" for="cand_position">Position / Office <span class="required">*</span></label>
            <input type="text" id="cand_position" name="position" class="form-control" placeholder="e.g. Member of Parliament" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="cand_symbol">Election Symbol <span class="required">*</span></label>
            <select id="cand_symbol" name="symbol" class="form-control" required>
              <option value="Tree|tree.svg">Tree (रुख)</option>
              <option value="Sun|sun.svg">Sun (सूर्य)</option>
              <option value="Hammer &amp; Sickle|hammer-sickle.svg">Hammer &amp; Sickle (हँसिया हथौडा)</option>
              <option value="Bell|bell.svg">Bell / Ghanti (घण्टी)</option>
              <option value="Torch|torch.svg">Torch / Mashaal (मशाल)</option>
              <option value="Soil|soil.svg">Soil / Mato (माटो)</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="cand_bio">Candidate Biography &amp; Manifesto <span class="required">*</span></label>
          <textarea id="cand_bio" name="biography" class="form-control" rows="3" placeholder="Summary of public service, education and agenda..." required></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-close-modal>Cancel</button>
        <button type="submit" class="btn btn-primary">Save Candidate</button>
      </div>
    </form>
  </div>
</div>
