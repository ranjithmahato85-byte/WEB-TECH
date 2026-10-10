<?php
$page_title = htmlspecialchars($election['title']) . " - Election Details";
$section_title = "Election Details";
$section_subtitle = htmlspecialchars($election['election_type']) . " &bull; " . htmlspecialchars($election['location']);
?>
<div class="container" style="padding-top: 1.5rem; padding-bottom: 3rem;">
  <!-- BREADCRUMBS -->
  <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">
    <a href="<?= !empty($current_user) ? url_for('my_elections') : url_for('index') ?>">&larr; Back to Elections</a>
  </div>

  <!-- ELECTION HERO CARD -->
  <div style="background: #ffffff; border-radius: var(--radius-xl); border: 1px solid var(--border-color); box-shadow: var(--shadow-md); overflow: hidden; margin-bottom: 2.5rem;">
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; padding: 2.5rem; position: relative;">
      <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div style="max-width: 700px;">
          <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
            <span class="badge badge-<?= strtolower($election['status']) ?>" style="font-size: 0.82rem; padding: 4px 12px;">
              <?= htmlspecialchars($election['status']) ?>
            </span>
            <span style="font-size: 0.85rem; color: #cbd5e1;"><?= htmlspecialchars($election['election_type']) ?></span>
          </div>
          <h1 style="font-size: 2rem; font-weight: 800; line-height: 1.25; margin-bottom: 1rem;">
            <?= htmlspecialchars($election['title']) ?>
          </h1>
          <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.6;">
            <?= nl2br(htmlspecialchars($election['description'])) ?>
          </p>
        </div>

        <div style="text-align: right;">
          <?php if ($election['status'] === 'Active'): ?>
            <?php if (!empty($has_voted)): ?>
              <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #a7f3d0; padding: 10px 18px; border-radius: var(--radius-md); font-weight: 700;">
                Vote Cast &amp; Recorded
              </div>
            <?php elseif (!empty($has_already_voted)): ?>
              <div style="background: rgba(241, 245, 249, 0.15); border: 1px solid rgba(255, 255, 255, 0.35); color: #f1f5f9; padding: 10px 18px; border-radius: var(--radius-md); font-weight: 600;">
                1 Vote Cast (Single Ballot Policy)
              </div>
            <?php else: ?>
              <a href="<?= url_for('election_vote', ['election_id' => $election['id']]) ?>" class="btn btn-crimson btn-lg">
                Proceed to Vote
              </a>
            <?php endif; ?>
          <?php elseif ($election['status'] === 'Completed'): ?>
            <a href="<?= url_for('results_detail', ['election_id' => $election['id']]) ?>" class="btn btn-primary btn-lg">
              View Results
            </a>
          <?php else: ?>
            <div style="background: rgba(245, 158, 11, 0.2); border: 1px solid #f59e0b; color: #fde68a; padding: 10px 18px; border-radius: var(--radius-md); font-weight: 600;">
              Voting Not Started
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- METADATA STRIP -->
      <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.88rem;" class="form-row">
        <div>
          <span style="color: #94a3b8; display: block; font-size: 0.75rem; text-transform: uppercase;">Constituency</span>
          <strong><?= htmlspecialchars($election['location']) ?></strong>
        </div>
        <div>
          <span style="color: #94a3b8; display: block; font-size: 0.75rem; text-transform: uppercase;">Start Date</span>
          <strong><?= htmlspecialchars($election['start_date']) ?></strong>
        </div>
        <div>
          <span style="color: #94a3b8; display: block; font-size: 0.75rem; text-transform: uppercase;">End Date</span>
          <strong><?= htmlspecialchars($election['end_date']) ?></strong>
        </div>
        <div>
          <span style="color: #94a3b8; display: block; font-size: 0.75rem; text-transform: uppercase;">Total Candidates</span>
          <strong><?= count($election['candidates'] ?? []) ?> Nominated</strong>
        </div>
      </div>
    </div>
  </div>

  <!-- CANDIDATES NOMINATED IN THIS ELECTION -->
  <div style="margin-bottom: 2rem;">
    <div class="section-header">
      <div>
        <h2 class="section-title">Nominated Candidates</h2>
        <p class="section-subtitle">Review official candidates, verified election symbols, and candidate manifestos</p>
      </div>
    </div>

    <div class="candidates-grid">
      <?php foreach (($election['candidates'] ?? []) as $candidate): ?>
      <div class="candidate-card">
        <div class="candidate-photo-wrapper">
          <img src="/static/images/candidates/<?= htmlspecialchars($candidate['photo_url']) ?>" alt="<?= htmlspecialchars($candidate['name']) ?>" class="candidate-photo">
          <div class="candidate-symbol-badge" title="Symbol: <?= htmlspecialchars($candidate['symbol_name']) ?>">
            <img src="/static/images/<?= htmlspecialchars($candidate['symbol_image']) ?>" alt="<?= htmlspecialchars($candidate['symbol_name']) ?>" style="width: 18px; height: 18px;">
          </div>
        </div>

        <h3 class="candidate-name"><?= htmlspecialchars($candidate['name']) ?></h3>
        <span class="candidate-party"><?= htmlspecialchars($candidate['party']) ?></span>
        <span class="candidate-position">Symbol: <strong><?= htmlspecialchars($candidate['symbol_name']) ?></strong></span>
        
        <p style="font-size: 0.8rem; color: #64748b; line-height: 1.4; margin-bottom: 1.25rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
          <?= htmlspecialchars($candidate['biography'] ?? '') ?>
        </p>

        <a href="<?= url_for('candidate_profile', ['candidate_id' => $candidate['id']]) ?>" class="btn btn-outline btn-sm" style="width: 100%; margin-top: auto;">
          View Profile
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>
