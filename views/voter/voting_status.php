<?php
$page_title = "Voting Status - Nepal Online Voting System";
$section_title = "Voting Status & Audit Log";
$section_subtitle = "Your official democratic participation records";
?>
<div style="max-width: 960px; margin: 0 auto;">
  
  <!-- BALLOT SECRECY INFO BANNER -->
  <div style="background-color: #166534; border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 14px;">
    <svg width="24" height="24" fill="none" stroke="#ffffff" viewBox="0 0 24 24" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
    </svg>
    <div>
      <h4 style="font-size: 0.95rem; font-weight: 700; color: #ffffff;">Constitution of Nepal &bull; Article 84 Secret Ballot Guarantee</h4>
      <p style="font-size: 0.85rem; color: #d1fae5; margin-top: 4px; line-height: 1.5;">
        This audit record confirms your electoral participation and verifies that your vote was successfully counted. In strict accordance with constitutional ballot secrecy, the specific candidate you voted for is <strong>never displayed, stored, or revealed</strong> in this log.
      </p>
    </div>
  </div>

  <!-- STATUS RECORDS TABLE -->
  <div class="table-card">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
      <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a;">Election Participation Records</h3>
      <span style="font-size: 0.82rem; color: var(--text-muted);">Voter ID: <strong><?= htmlspecialchars($current_user['voter_id'] ?? '') ?></strong></span>
    </div>

    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Election Name</th>
            <th>Type</th>
            <th>Participation Status</th>
            <th>Date / Timestamp</th>
            <th>Confirmation Code</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($participation_data ?? []) as $item): ?>
          <tr>
            <td>
              <strong style="color: #0f172a;"><?= htmlspecialchars($item['election']['title']) ?></strong>
            </td>
            <td><?= htmlspecialchars($item['election']['election_type']) ?></td>
            <td>
              <?php if (!empty($item['has_voted'])): ?>
                <span class="badge badge-active" style="display: inline-flex; align-items: center; gap: 4px;">
                  Voted
                </span>
              <?php else: ?>
                <span class="badge badge-completed">
                  Not Voted
                </span>
              <?php endif; ?>
            </td>
            <td style="font-size: 0.82rem; color: #475569;">
              <?= !empty($item['has_voted']) ? htmlspecialchars($item['voted_at']) : '-' ?>
            </td>
            <td>
              <?php if (!empty($item['has_voted'])): ?>
                <code style="background: #e2e8f0; padding: 3px 8px; border-radius: 4px; font-size: 0.82rem; color: #0f172a; font-weight: 700;">
                  <?= htmlspecialchars($item['confirmation_code']) ?>
                </code>
              <?php else: ?>
                <span style="color: #94a3b8; font-size: 0.82rem;">None</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!empty($item['has_voted'])): ?>
                <a href="<?= url_for('election_detail', ['election_id' => $item['election']['id']]) ?>" class="btn btn-outline btn-sm">
                  View Election
                </a>
              <?php else: ?>
                <?php if ($item['election']['status'] === 'Active'): ?>
                  <?php if (!empty($has_already_voted)): ?>
                    <span class="badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;" title="Under 1 Citizen 1 Vote Policy, you have already cast your official vote.">
                      1 Vote Cast
                    </span>
                  <?php else: ?>
                    <a href="<?= url_for('election_vote', ['election_id' => $item['election']['id']]) ?>" class="btn btn-primary btn-sm">
                      Vote Now
                    </a>
                  <?php endif; ?>
                <?php else: ?>
                  <a href="<?= url_for('election_detail', ['election_id' => $item['election']['id']]) ?>" class="btn btn-outline btn-sm">
                    View Details
                  </a>
                <?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
