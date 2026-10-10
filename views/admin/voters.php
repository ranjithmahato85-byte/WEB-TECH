<?php
$page_title = "Voter Management - Admin Portal";
$section_title = "Voter Registry Management";
$section_subtitle = "Electoral roll verification and status control";
?>
<div>
  <!-- BALLOT SECRECY LEGAL CALLOUT -->
  <div style="background-color: #f8fafc; border: 1px solid var(--border-color); border-left: 4px solid #2563eb; border-radius: var(--radius-md); padding: 1rem 1.25rem; margin-bottom: 1.5rem; font-size: 0.85rem; color: #334155;">
    <strong>Ballot Secrecy Protocol:</strong>
    As mandated by election integrity guidelines, the administration panel displays voter verification and participation timestamps, but <strong>strictly restricts access to any voter's candidate selection</strong>.
  </div>

  <!-- TOOLBAR -->
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h3 style="font-size: 1.2rem; font-weight: 700; color: #0f172a;">Registered Citizen Electorate</h3>
      <p style="font-size: 0.85rem; color: var(--text-muted);">Verified against citizenship and provincial wards</p>
    </div>

    <form method="GET" action="<?= url_for('admin_voters') ?>" style="display: flex; gap: 8px;">
      <input type="text" name="q" value="<?= htmlspecialchars($search_query ?? '') ?>" class="form-control" placeholder="Search voter name, ID, or district..." style="width: 260px;">
      <button type="submit" class="btn btn-outline btn-sm">Search</button>
    </form>
  </div>

  <!-- VOTERS TABLE -->
  <div class="table-card">
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Voter ID</th>
            <th>Full Legal Name</th>
            <th>Citizenship No.</th>
            <th>Contact</th>
            <th>Province &amp; District</th>
            <th>Ward</th>
            <th>Verification</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($voters ?? []) as $voter): ?>
          <tr>
            <td>
              <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-weight: 700; color: #0f172a;"><?= htmlspecialchars($voter['voter_id']) ?></code>
            </td>
            <td>
              <strong style="color: #0f172a;"><?= htmlspecialchars($voter['full_name']) ?></strong>
              <div style="font-size: 0.75rem; color: var(--text-muted);">
                @<?= htmlspecialchars($voter['username']) ?> &bull; Age <?= (int)$voter['age'] ?>
                <?php if (!empty($voter['is_eligible_age'])): ?>
                  <span style="color: #16a34a; font-weight: 600;">(18+)</span>
                <?php else: ?>
                  <span style="color: #dc2626; font-weight: 600;">(&lt;18 Ineligible)</span>
                <?php endif; ?>
              </div>
            </td>
            <td>
              <?php if (!empty($voter['citizenship_no'])): ?>
                <code style="background: #eff6ff; color: #1e40af; padding: 2px 6px; border-radius: 4px; font-weight: 600; font-size: 0.8rem;"><?= htmlspecialchars($voter['citizenship_no']) ?></code>
                <div style="margin-top: 4px; display: flex; gap: 6px; align-items: center;">
                  <button type="button" class="btn btn-outline btn-sm" onclick="openCitModal('<?= (int)$voter['id'] ?>')" style="font-size: 0.72rem; padding: 2px 8px; color: #2563eb; border-color: #bfdbfe; background: #ffffff;">
                    View Photo
                  </button>
                  <a href="<?= htmlspecialchars(cit_photo_url($voter['citizenship_photo'])) ?>" target="_blank" style="font-size: 0.72rem; color: #64748b; text-decoration: underline;" title="Open in new window">
                    &#8599;
                  </a>
                </div>

                <!-- VERIFICATION & AUDIT MODAL FOR VOTER -->
                <div id="citModal-<?= (int)$voter['id'] ?>" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); z-index: 9999; align-items: center; justify-content: center; padding: 1rem; backdrop-filter: blur(2px);">
                  <div style="background: #ffffff; width: 100%; max-width: 680px; border-radius: var(--radius-xl); box-shadow: var(--shadow-xl); overflow: hidden; border: 1px solid var(--border-color); max-height: 90vh; display: flex; flex-direction: column;">

                    <!-- Modal Header -->
                    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
                      <div>
                        <span class="badge-pill-soft" style="background: rgba(255,255,255,0.15); color: #93c5fd; font-size: 0.72rem;">Official Identity Audit</span>
                        <h3 style="font-size: 1.15rem; font-weight: 800; margin-top: 4px;">Citizenship Verification: <?= htmlspecialchars($voter['full_name']) ?></h3>
                        <p style="font-size: 0.8rem; color: #cbd5e1; margin: 0;">Voter ID: <?= htmlspecialchars($voter['voter_id']) ?> &bull; Citizen No: <?= htmlspecialchars($voter['citizenship_no']) ?></p>
                      </div>
                      <button type="button" onclick="closeCitModal('<?= (int)$voter['id'] ?>')" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer; line-height: 1;">&times;</button>
                    </div>

                    <!-- Modal Body -->
                    <div style="padding: 1.5rem; overflow-y: auto;">

                      <!-- Name Match Status Banner -->
                      <div style="background: #166534; border-radius: var(--radius-md); padding: 0.85rem 1.25rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                          <svg width="22" height="22" fill="none" stroke="#ffffff" viewBox="0 0 24 24" stroke-width="2.2" style="flex-shrink: 0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                          </svg>
                          <div>
                            <strong style="color: #ffffff; font-size: 0.9rem;">Identity Verified (नाम प्रमाणीकरण):</strong>
                            <div style="font-size: 0.82rem; color: #d1fae5; margin-top: 1px;">
                              Full Legal Name matches name printed on official citizenship certificate.
                            </div>
                          </div>
                        </div>
                        <span class="badge badge-active" style="font-size: 0.72rem;">100% Match</span>
                      </div>

                      <!-- Document Card Preview Container -->
                      <div style="border: 2px solid #e2e8f0; border-radius: var(--radius-lg); overflow: hidden; background: #f8fafc; padding: 8px; text-align: center; margin-bottom: 1.25rem; box-shadow: var(--shadow-sm);">
                        <img src="<?= htmlspecialchars(cit_photo_url($voter['citizenship_photo'])) ?>" alt="Citizenship Certificate" style="width: 100%; max-height: 260px; object-fit: contain; border-radius: var(--radius-md); display: block; margin: 0 auto;">
                        <div style="margin-top: 6px; display: flex; justify-content: space-between; align-items: center; padding: 0 8px;">
                          <span style="font-size: 0.75rem; color: #64748b;">
                            Printed Name on Card: <strong style="color: #0f172a;"><?= htmlspecialchars($voter['full_name']) ?></strong>
                          </span>
                          <a href="<?= htmlspecialchars(cit_photo_url($voter['citizenship_photo'])) ?>" target="_blank" class="btn btn-outline btn-sm" style="font-size: 0.72rem; padding: 2px 8px;">
                            Open Full Resolution ↗
                          </a>
                        </div>
                      </div>

                      <!-- ACTION ACCORDIONS (UPDATE DETAILS OR RE-UPLOAD) -->
                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

                        <!-- 1. Update Legal Details Form -->
                        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem;">
                          <h4 style="font-size: 0.88rem; font-weight: 700; color: #0f172a; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Update Legal Details
                          </h4>
                          <form action="<?= url_for('admin_update_voter_details', ['voter_id' => $voter['id']]) ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="form-group" style="margin-bottom: 8px;">
                              <label class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">Full Legal Name</label>
                              <input type="text" name="full_name" class="form-control" style="font-size: 0.82rem; padding: 4px 8px;" value="<?= htmlspecialchars($voter['full_name']) ?>" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 8px;">
                              <label class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">Citizenship No.</label>
                              <input type="text" name="citizenship_no" class="form-control" style="font-size: 0.82rem; padding: 4px 8px;" value="<?= htmlspecialchars($voter['citizenship_no']) ?>" required>
                            </div>
                            <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 6px; margin-bottom: 8px;">
                              <div>
                                <label class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">District</label>
                                <input type="text" name="district" class="form-control" style="font-size: 0.82rem; padding: 4px 8px;" value="<?= htmlspecialchars($voter['district'] ?? '') ?>">
                              </div>
                              <div>
                                <label class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">Ward</label>
                                <input type="text" name="ward" class="form-control" style="font-size: 0.82rem; padding: 4px 8px;" value="<?= htmlspecialchars($voter['ward'] ?? '') ?>">
                              </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; font-size: 0.75rem;">
                              Update &amp; Sync Certificate
                            </button>
                          </form>
                        </div>

                        <!-- 2. Re-upload / Regenerate Certificate Form -->
                        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem;">
                          <h4 style="font-size: 0.88rem; font-weight: 700; color: #0f172a; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Re-upload Certificate
                          </h4>
                          <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 10px;">
                            If document name does not match, upload a corrected certificate scan or regenerate matching card.
                          </p>
                          <form action="<?= url_for('admin_reupload_voter_certificate', ['voter_id' => $voter['id']]) ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="form-group" style="margin-bottom: 10px;">
                              <label class="form-label" style="font-size: 0.75rem; margin-bottom: 2px;">New Certificate Scan</label>
                              <input type="file" name="citizenship_photo" class="form-control" style="font-size: 0.78rem; padding: 4px;" accept="image/*,.pdf,.svg">
                            </div>
                            <button type="submit" class="btn btn-outline btn-sm" style="width: 100%; font-size: 0.75rem; color: #16a34a; border-color: #16a34a; margin-bottom: 6px;">
                              Upload New File
                            </button>
                            <button type="submit" class="btn btn-outline btn-sm" style="width: 100%; font-size: 0.75rem; color: #2563eb; border-color: #2563eb;">
                              Regenerate Official Card
                            </button>
                          </form>
                        </div>

                      </div>

                    </div>

                    <!-- Modal Footer -->
                    <div style="background: #f1f5f9; padding: 0.75rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end;">
                      <button type="button" class="btn btn-outline btn-sm" onclick="closeCitModal('<?= (int)$voter['id'] ?>')">
                        Close Audit Window
                      </button>
                    </div>

                  </div>
                </div>

              <?php else: ?>
                <span style="color: #dc2626; font-size: 0.78rem; font-weight: 600;">Not Registered</span>
              <?php endif; ?>
            </td>
            <td style="font-size: 0.82rem;">
              <div><?= htmlspecialchars($voter['email']) ?></div>
              <div style="color: var(--text-muted);">+977 <?= htmlspecialchars($voter['mobile'] ?? '') ?></div>
            </td>
            <td><?= htmlspecialchars($voter['province'] ?? '') ?> &bull; <?= htmlspecialchars($voter['district'] ?? '') ?></td>
            <td>Ward <?= htmlspecialchars($voter['ward'] ?? '') ?></td>
            <td>
              <?php if (!empty($voter['is_verified']) && !empty($voter['is_eligible_age']) && !empty($voter['has_citizenship_verified'])): ?>
                <span class="badge badge-active" style="font-size: 0.72rem;">Admin Verified</span>
              <?php elseif (empty($voter['is_verified'])): ?>
                <span class="badge badge-completed" style="font-size: 0.72rem; color: #d97706; background: #fef3c7;">Pending Review</span>
              <?php elseif (empty($voter['is_eligible_age'])): ?>
                <span class="badge" style="font-size: 0.72rem; color: #ffffff; background: #dc2626;">Under 18</span>
              <?php else: ?>
                <span class="badge badge-completed" style="font-size: 0.72rem; color: #d97706; background: #fef3c7;">Missing Citizenship</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!empty($voter['is_active'])): ?>
                <span class="badge badge-active">Active</span>
              <?php else: ?>
                <span class="badge badge-completed" style="color: #ef4444;">Deactivated</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (empty($voter['is_verified'])): ?>
                <form action="<?= url_for('admin_verify_voter', ['voter_id' => $voter['id']]) ?>" method="POST" style="display: inline;">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-outline btn-sm" style="color: #16a34a; border-color: #16a34a; font-size: 0.75rem;">
                    Approve Voter
                  </button>
                </form>
              <?php endif; ?>
              <form action="<?= url_for('admin_toggle_voter', ['voter_id' => $voter['id']]) ?>" method="POST" style="display: inline;">
                <?= csrf_field() ?>
                <?php if (!empty($voter['is_active'])): ?>
                  <button type="submit" class="btn btn-outline btn-sm" style="color: #dc2626; border-color: #dc2626; font-size: 0.75rem;">
                    Deactivate
                  </button>
                <?php else: ?>
                  <button type="submit" class="btn btn-outline btn-sm" style="color: #16a34a; border-color: #16a34a; font-size: 0.75rem;">
                    Activate
                  </button>
                <?php endif; ?>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  function openCitModal(voterId) {
    const modal = document.getElementById('citModal-' + voterId);
    if (modal) {
      modal.style.display = 'flex';
    }
  }

  function closeCitModal(voterId) {
    const modal = document.getElementById('citModal-' + voterId);
    if (modal) {
      modal.style.display = 'none';
    }
  }

  // Close modal when clicking outside content
  window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-backdrop')) {
      e.target.style.display = 'none';
    }
  });
</script>
