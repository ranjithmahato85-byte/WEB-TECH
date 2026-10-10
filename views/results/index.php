<?php
$page_title = "Election Results - Nepal Online Voting System";
$section_title = "Official Election Results";
$section_subtitle = "Audited election tabulations and democratic outcomes";
?>
<div class="container" style="padding-top: 1.5rem; padding-bottom: 3rem;">
  <div class="section-header">
    <div>
      <h2 class="section-title">Completed &amp; Audited Elections</h2>
      <p class="section-subtitle">Official final results certified by the Election Commission audit protocol</p>
    </div>
  </div>

  <div class="elections-grid">
    <?php foreach (($elections ?? []) as $election): ?>
    <div class="election-card">
      <div class="election-card-image">
        <img src="/static/images/<?= htmlspecialchars($election['image_url'] ?: 'nepal-hero.jpg') ?>" alt="<?= htmlspecialchars($election['title']) ?>">
        <div class="election-badge-pos">
          <span class="badge badge-completed">Results Certified</span>
        </div>
      </div>

      <div class="election-card-body">
        <h3 class="election-card-title"><?= htmlspecialchars($election['title']) ?></h3>
        <div class="election-meta">
          <div class="election-meta-item">
            <span><?= htmlspecialchars($election['election_type']) ?> &bull; <?= htmlspecialchars($election['location']) ?></span>
          </div>
          <div class="election-meta-item">
            <span>Completed: <?= htmlspecialchars($election['end_date']) ?></span>
          </div>
          <div class="election-meta-item">
            <span><?= count($election['candidates'] ?? []) ?> Contesting Candidates</span>
          </div>
        </div>

        <div class="election-card-footer">
          <span style="font-size: 0.8rem; color: #16a34a; font-weight: 700;">Audited</span>
          <a href="<?= url_for('results_detail', ['election_id' => $election['id']]) ?>" class="btn btn-primary btn-sm">
            View Results
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
