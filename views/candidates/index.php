<?php
$page_title = "Candidates Directory - Nepal Online Voting System";
$section_title = "Candidate Directory";
$section_subtitle = "Explore nominated candidates and official electoral symbols";
?>
<div class="container" style="padding-top: 1.5rem; padding-bottom: 3rem;">
  
  <!-- SEARCH & FILTER TOOLBAR -->
  <div style="background: #ffffff; border-radius: var(--radius-lg); border: 1px solid var(--border-color); padding: 1.25rem 1.5rem; margin-bottom: 2rem; box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div style="flex-grow: 1; max-width: 400px; position: relative;">
      <input type="text" id="candidateSearch" class="form-control" placeholder="Search candidate by name or party..." style="padding-left: 36px;">
      <svg style="position: absolute; left: 12px; top: 12px; width: 16px; height: 16px; color: var(--text-light);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
    </div>

    <div style="display: flex; align-items: center; gap: 12px;">
      <label style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Filter Party:</label>
      <select id="partyFilter" class="form-control" style="width: auto;">
        <option value="all">All Parties</option>
        <option value="Nepali Congress">Nepali Congress</option>
        <option value="CPN (UML)">CPN (UML)</option>
        <option value="Maoist Center">Maoist Center</option>
        <option value="Rastriya Swatantra Party">Rastriya Swatantra Party</option>
        <option value="Independent">Independent</option>
      </select>
    </div>
  </div>

  <!-- CANDIDATES GRID -->
  <div class="candidates-grid" id="candidateListGrid">
    <?php foreach (($candidates ?? []) as $candidate): ?>
    <div class="candidate-card candidate-dir-card" data-name="<?= htmlspecialchars($candidate['name']) ?>" data-party="<?= htmlspecialchars($candidate['party']) ?>">
      <div class="candidate-photo-wrapper">
        <img src="/static/images/candidates/<?= htmlspecialchars($candidate['photo_url']) ?>" alt="<?= htmlspecialchars($candidate['name']) ?>" class="candidate-photo">
        <div class="candidate-symbol-badge" title="<?= htmlspecialchars($candidate['symbol_name']) ?>">
          <img src="/static/images/<?= htmlspecialchars($candidate['symbol_image']) ?>" alt="<?= htmlspecialchars($candidate['symbol_name']) ?>" style="width: 18px; height: 18px;">
        </div>
      </div>

      <h3 class="candidate-name"><?= htmlspecialchars($candidate['name']) ?></h3>
      <div class="candidate-party"><?= htmlspecialchars($candidate['party']) ?></div>
      <div class="candidate-position"><?= htmlspecialchars($candidate['position']) ?> &bull; <?= htmlspecialchars($candidate['election_title'] ?? '') ?></div>
      
      <div style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 1rem;">
        <span>Symbol:</span>
        <strong style="color: #0f172a;"><?= htmlspecialchars($candidate['symbol_name']) ?></strong>
      </div>

      <a href="<?= url_for('candidate_profile', ['candidate_id' => $candidate['id']]) ?>" class="btn btn-outline-primary btn-sm" style="width: 100%; margin-top: auto;">
        View Profile
      </a>
    </div>
    <?php endforeach; ?>
  </div>

</div>
