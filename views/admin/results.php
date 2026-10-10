<?php
$page_title = "Results Audit - Admin Portal";
$section_title = "Electoral Results & Audit Oversight";
$section_subtitle = "Live tabulation telemetry & verified tally logs";
?>
<div style="display: flex; flex-direction: column; gap: 2rem;">
  <?php foreach (($election_summaries ?? []) as $item): ?>
  <?php $el = $item['election']; ?>
  <div class="table-card" style="padding: 1.5rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1rem; flex-wrap: wrap; gap: 10px;">
      <div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a;"><?= htmlspecialchars($el['title']) ?></h3>
          <span class="badge badge-<?= strtolower(htmlspecialchars($el['status'])) ?>"><?= htmlspecialchars($el['status']) ?></span>
        </div>
        <span style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars($el['election_type']) ?> &bull; <?= htmlspecialchars($el['location']) ?></span>
      </div>

      <div style="display: flex; gap: 16px; font-size: 0.88rem;">
        <div>
          <span style="color: var(--text-muted); display: block; font-size: 0.72rem; text-transform: uppercase;">Electorate</span>
          <strong><?= number_format($item['total_registered']) ?></strong>
        </div>
        <div>
          <span style="color: var(--text-muted); display: block; font-size: 0.72rem; text-transform: uppercase;">Votes Cast</span>
          <strong><?= number_format($item['total_votes']) ?></strong>
        </div>
        <div>
          <span style="color: var(--text-muted); display: block; font-size: 0.72rem; text-transform: uppercase;">Turnout</span>
          <strong style="color: #16a34a;"><?= $item['turnout_percent'] ?>%</strong>
        </div>
        <a href="<?= url_for('results_detail', ['election_id' => $el['id']]) ?>" class="btn btn-outline btn-sm" target="_blank">
          Public Certificate
        </a>
      </div>
    </div>

    <!-- CANDIDATE BREAKDOWN TABLE -->
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Rank</th>
            <th>Candidate</th>
            <th>Party</th>
            <th>Official Symbol</th>
            <th>Tallied Votes</th>
            <th>Percentage</th>
          </tr>
        </thead>
        <tbody>
          <?php $rank = 1; foreach (($item['candidate_results'] ?? []) as $cr): ?>
          <tr>
            <td><strong><?= $rank ?></strong></td>
            <td>
              <div style="display: flex; align-items: center; gap: 8px;">
                <img src="/static/images/candidates/<?= htmlspecialchars($cr['candidate']['photo_url'] ?? 'candidate-1.svg') ?>" style="width: 28px; height: 28px; border-radius: 50%;">
                <strong style="color: #0f172a;"><?= htmlspecialchars($cr['candidate']['name'] ?? '') ?></strong>
              </div>
            </td>
            <td><?= htmlspecialchars($cr['candidate']['party'] ?? '') ?></td>
            <td>
              <div style="display: flex; align-items: center; gap: 6px;">
                <img src="/static/images/<?= htmlspecialchars($cr['candidate']['symbol_image'] ?? 'tree.svg') ?>" style="width: 18px; height: 18px;">
                <span><?= htmlspecialchars($cr['candidate']['symbol_name'] ?? '') ?></span>
              </div>
            </td>
            <td><strong><?= number_format($cr['votes']) ?></strong></td>
            <td style="min-width: 120px;">
              <span style="font-size: 0.82rem; font-weight: 600;"><?= $cr['percentage'] ?>%</span>
              <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: <?= $cr['percentage'] ?>%;"></div>
              </div>
            </td>
          </tr>
          <?php $rank++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endforeach; ?>
</div>
