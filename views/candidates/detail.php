<?php
$page_title = htmlspecialchars($candidate['name']) . " - Candidate Profile";
$section_title = "Candidate Profile";
$section_subtitle = "Official Nominee Record & Academic Profile";
?>
<div class="container" style="max-width: 900px; padding-top: 1.5rem; padding-bottom: 3rem;">
  
  <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">
    <a href="<?= url_for('candidates_list') ?>">&larr; Back to Candidates Directory</a>
  </div>

  <!-- ACADEMIC DEMO NOTICE -->
  <div style="background-color: #fff7ed; border: 1px solid #fed7aa; border-left: 4px solid #ea580c; border-radius: var(--radius-md); padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-size: 0.85rem; color: #9a3412;">
    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="flex-shrink: 0;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>
      <strong>Sample Candidate - Academic Demonstration:</strong> This candidate profile is generated strictly for academic demonstration and system evaluation purposes. It does not represent real-world political affiliations or living individuals.
    </span>
  </div>

  <!-- PROFILE CARD -->
  <div style="background: #ffffff; border-radius: var(--radius-xl); border: 1px solid var(--border-color); box-shadow: var(--shadow-md); overflow: hidden;">
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; padding: 2.5rem; display: flex; align-items: center; gap: 24px; flex-wrap: wrap;">
      <div style="width: 110px; height: 110px; border-radius: 50%; overflow: hidden; border: 4px solid #ffffff; box-shadow: var(--shadow-md); flex-shrink: 0;">
        <img src="/static/images/candidates/<?= htmlspecialchars($candidate['photo_url']) ?>" alt="<?= htmlspecialchars($candidate['name']) ?>" style="width: 100%; height: 100%; object-fit: cover; object-position: top center;">
      </div>

      <div style="flex-grow: 1;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
          <h1 style="font-size: 1.85rem; font-weight: 800; color: #ffffff;"><?= htmlspecialchars($candidate['name']) ?></h1>
        </div>
        <div style="font-size: 1.05rem; color: #93c5fd; font-weight: 600;"><?= htmlspecialchars($candidate['party']) ?></div>
        <div style="font-size: 0.85rem; color: #cbd5e1; margin-top: 4px;">
          Contesting for: <strong><?= htmlspecialchars($candidate['position']) ?></strong> &bull; <?= htmlspecialchars($candidate['election_title'] ?? '') ?>
        </div>
      </div>

      <div style="background: rgba(255,255,255,0.08); padding: 12px 20px; border-radius: var(--radius-lg); text-align: center; border: 1px solid rgba(255,255,255,0.15);">
        <img src="/static/images/<?= htmlspecialchars($candidate['symbol_image']) ?>" alt="<?= htmlspecialchars($candidate['symbol_name']) ?>" style="width: 44px; height: 44px; margin: 0 auto 4px;">
        <div style="font-size: 0.78rem; font-weight: 700; color: #ffffff; text-transform: uppercase;">Symbol: <?= htmlspecialchars($candidate['symbol_name']) ?></div>
      </div>
    </div>

    <!-- PROFILE DETAILS & BIOGRAPHY -->
    <div style="padding: 2.5rem;">
      <h3 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">Candidate Biography &amp; Vision</h3>
      <p style="color: #475569; font-size: 0.95rem; line-height: 1.7; margin-bottom: 2rem;">
        <?= nl2br(htmlspecialchars($candidate['biography'])) ?>
      </p>

      <h3 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">Contesting Election</h3>
      <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
        <div>
          <h4 style="font-size: 1.05rem; font-weight: 700; color: #0f172a;"><?= htmlspecialchars($candidate['election_title'] ?? '') ?></h4>
          <span style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars($candidate['position']) ?></span>
        </div>
        <a href="<?= url_for('election_detail', ['election_id' => $candidate['election_id']]) ?>" class="btn btn-outline btn-sm">
          View Election
        </a>
      </div>
    </div>
  </div>

</div>
