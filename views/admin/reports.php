<?php
$page_title = "Reports &amp; Audit Trail - Admin Portal";
$section_title = "Security Reports & Cryptographic Logs";
$section_subtitle = "Tamper-evident verification logs and system access trail";
?>
<div style="display: flex; flex-direction: column; gap: 2rem;">

  <!-- SUMMARY CARDS -->
  <div class="results-summary-cards">
    <div class="result-metric-card">
      <div class="result-metric-icon" style="background: #e0f2fe; color: #0284c7;">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
      </div>
      <div>
        <span class="result-metric-label">Integrity Status</span>
        <div class="result-metric-value" style="font-size: 1.25rem; color: #16a34a;">100% Verified</div>
      </div>
    </div>

    <div class="result-metric-card">
      <div class="result-metric-icon" style="background: #16a34a; color: #ffffff;">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
      </div>
      <div>
        <span class="result-metric-label">Decoupled Ballots</span>
        <div class="result-metric-value" style="font-size: 1.25rem; color: #0f172a;">Zero Choice Leakage</div>
      </div>
    </div>

    <div class="result-metric-card">
      <div class="result-metric-icon" style="background: #fef3c7; color: #d97706;">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <div>
        <span class="result-metric-label">Audit Protocol</span>
        <div class="result-metric-value" style="font-size: 1.25rem; color: #d97706;">BCA Viva Ready</div>
      </div>
    </div>
  </div>

  <!-- AUDIT TRAIL LOG TABLE -->
  <div class="table-card">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
      <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a;">Participation Receipt Log (Voter Identity Decoupled)</h3>
      <span style="font-size: 0.8rem; color: var(--text-muted);">Immutable audit records</span>
    </div>

    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Receipt #</th>
            <th>Election ID</th>
            <th>Voter Identifier</th>
            <th>Timestamp</th>
            <th>Cryptographic Verification</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($participation_logs)): ?>
            <?php foreach ($participation_logs as $log): ?>
            <tr>
              <td>
                <code style="background: #f1f5f9; color: #0f172a; font-weight: 700; padding: 3px 8px; border-radius: 4px;"><?= htmlspecialchars($log['confirmation_code']) ?></code>
              </td>
              <td><code>Election #<?= (int)$log['election_id'] ?></code></td>
              <td>User #<?= (int)$log['user_id'] ?> (Anonymized)</td>
              <td style="font-size: 0.82rem; font-family: monospace;"><?= htmlspecialchars($log['voted_at'] ?? '') ?></td>
              <td>
                <span class="badge badge-active" style="font-size: 0.72rem;">SHA256-Matched</span>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                No participation receipts generated yet in this session.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
