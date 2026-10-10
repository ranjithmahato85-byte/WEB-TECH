<?php
$page_title = "Results: " . htmlspecialchars($election['title']) . " - Nepal Online Voting System";
$section_title = "Official Election Results";
$section_subtitle = "Audited Results & Voter Turnout Analysis";
$colors = ['#dc2626', '#0284c7', '#b91c1c', '#0284c7', '#d97706', '#059669', '#7c3aed', '#db2777', '#475569'];
?>
<div class="container" style="padding-top: 1.5rem; padding-bottom: 3rem;">
  
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
      <a href="<?= url_for('results_list') ?>" style="font-size: 0.85rem; color: var(--text-muted);">&larr; Back to All Results</a>
      <h1 style="font-size: 1.85rem; font-weight: 800; color: #0f172a; margin-top: 4px;"><?= htmlspecialchars($election['title']) ?></h1>
      <span style="font-size: 0.88rem; color: var(--text-muted);"><?= htmlspecialchars($election['election_type']) ?> &bull; <?= htmlspecialchars($election['location']) ?></span>
    </div>

    <div style="display: flex; align-items: center; gap: 8px;">
      <span class="badge badge-completed" style="font-size: 0.82rem; padding: 5px 12px;">Official Final Tally</span>
    </div>
  </div>

  <!-- 3 STAT METRIC CARDS -->
  <div class="results-summary-cards">
    <div class="result-metric-card">
      <div class="result-metric-icon" style="background: #e0f2fe; color: #0284c7;">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
      </div>
      <div>
        <span class="result-metric-label">Total Registered Voters</span>
        <div class="result-metric-value"><?= number_format($total_registered ?? 1200) ?></div>
      </div>
    </div>

    <div class="result-metric-card">
      <div class="result-metric-icon" style="background: #16a34a; color: #ffffff;">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
      </div>
      <div>
        <span class="result-metric-label">Total Votes Cast</span>
        <div class="result-metric-value"><?= number_format($total_votes ?? 0) ?></div>
      </div>
    </div>

    <div class="result-metric-card">
      <div class="result-metric-icon" style="background: #f3e8ff; color: #9333ea;">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <path d="M12 6v6l4 2"></path>
        </svg>
      </div>
      <div>
        <span class="result-metric-label">Voter Turnout</span>
        <div class="result-metric-value"><?= $turnout_percent ?? 0 ?>%</div>
      </div>
    </div>
  </div>

  <!-- RESULTS CONTENT SPLIT (TABLE + DONUT CHART) -->
  <div class="results-content-split">
    
    <!-- RESULTS TABLE -->
    <div class="table-card">
      <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
        <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a;">Candidate Vote Tally</h3>
        <span style="font-size: 0.8rem; color: var(--text-muted);">Ranked by valid votes</span>
      </div>

      <div class="table-responsive">
        <table class="custom-table">
          <thead>
            <tr>
              <th style="width: 40px;">#</th>
              <th>Candidate</th>
              <th>Party</th>
              <th>Votes</th>
              <th>Percentage</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($candidate_results)): ?>
              <?php $idx = 1; foreach ($candidate_results as $item): ?>
              <?php $row_color = $colors[($idx - 1) % count($colors)]; ?>
              <tr>
                <td>
                  <span style="font-weight: 800; font-size: 0.95rem; color: <?= ($idx === 1) ? '#eab308' : (($idx === 2) ? '#94a3b8' : (($idx === 3) ? '#b45309' : '#64748b')) ?>;">
                    <?= $idx ?>
                  </span>
                </td>
                <td>
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="/static/images/candidates/<?= htmlspecialchars($item['candidate']['photo_url'] ?? 'candidate-1.svg') ?>" alt="<?= htmlspecialchars($item['candidate']['name'] ?? '') ?>" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1.5px solid #cbd5e1;">
                    <div>
                      <strong style="color: #0f172a; font-size: 0.95rem;"><?= htmlspecialchars($item['candidate']['name'] ?? '') ?></strong>
                      <?php if ($idx === 1 && ($total_votes ?? 0) > 0): ?>
                        <span class="badge badge-active" style="margin-left: 6px; font-size: 0.68rem; padding: 2px 7px;">Highest Votes</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td>
                  <div style="display: flex; align-items: center; gap: 6px;">
                    <img src="/static/images/<?= htmlspecialchars($item['candidate']['symbol_image'] ?? 'tree.svg') ?>" alt="" style="width: 20px; height: 20px;">
                    <span style="font-weight: 600; color: #334155;"><?= htmlspecialchars($item['candidate']['party'] ?? '') ?></span>
                  </div>
                </td>
                <td>
                  <strong style="font-size: 1rem; color: #0f172a;"><?= number_format($item['votes']) ?></strong>
                </td>
                <td style="min-width: 140px;">
                  <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.85rem; font-weight: 700; margin-bottom: 4px;">
                    <span style="color: #0f172a;"><?= $item['percentage'] ?>%</span>
                  </div>
                  <div class="progress-bar-container" style="background: #f1f5f9; height: 7px; border-radius: 999px; overflow: hidden;">
                    <div class="progress-bar-fill" style="width: <?= $item['percentage'] ?>%; background: <?= $row_color ?>; height: 100%;"></div>
                  </div>
                </td>
              </tr>
              <?php $idx++; endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" style="text-align: center; padding: 2.5rem 1rem; color: #64748b;">
                  No candidate vote tallies recorded for this election yet.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- DONUT CHART VISUALIZATION -->
    <div class="donut-card">
      <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; text-align: center;">
        Vote Distribution
      </h3>

      <div class="donut-svg-wrapper">
        <svg viewBox="0 0 100 100" width="100%" height="100%">
          <circle cx="50" cy="50" r="38" fill="none" stroke="#e2e8f0" stroke-width="12" />
          
          <?php 
          $accum = 0;
          $cIdx = 0;
          foreach (($candidate_results ?? []) as $item):
            $pct = (float)$item['percentage'];
            $strokeLen = $pct * 238.76 / 100;
            $dashArray = round($strokeLen, 2) . ' ' . (238.76 - round($strokeLen, 2));
            $dashOffset = -$accum;
            $segColor = $colors[$cIdx % count($colors)];
          ?>
            <circle cx="50" cy="50" r="38" fill="none" 
                    stroke="<?= $segColor ?>" 
                    stroke-width="12"
                    stroke-dasharray="<?= $dashArray ?>" 
                    stroke-dashoffset="<?= $dashOffset ?>" 
                    transform="rotate(-90 50 50)"/>
          <?php 
            $accum += $strokeLen;
            $cIdx++;
          endforeach; 
          ?>
        </svg>

        <div class="donut-center-text">
          <div class="donut-center-total" style="font-size: 1.05rem; font-weight: 800;"><?= number_format($total_votes ?? 0) ?></div>
          <div class="donut-center-label" style="font-size: 0.72rem; text-transform: uppercase;">Total Votes</div>
        </div>
      </div>

      <!-- DONUT LEGEND -->
      <div class="donut-legend">
        <?php $lIdx = 0; foreach (($candidate_results ?? []) as $item): ?>
        <div class="legend-item" style="padding: 4px 0;">
          <div style="display: flex; align-items: center; gap: 8px;">
            <span class="legend-color-dot" style="background: <?= $colors[$lIdx % count($colors)] ?>; width: 10px; height: 10px; border-radius: 50%; display: inline-block; flex-shrink: 0;"></span>
            <span style="color: #334155; font-size: 0.82rem; font-weight: 600;"><?= htmlspecialchars($item['candidate']['name'] ?? '') ?></span>
          </div>
          <strong style="color: #0f172a; font-size: 0.82rem;"><?= $item['percentage'] ?>%</strong>
        </div>
        <?php $lIdx++; endforeach; ?>
      </div>
    </div>

  </div>

</div>
