<?php
$page_title = "Cast Your Ballot - " . htmlspecialchars($election['title']);
$section_title = "Official Electronic Ballot";
$section_subtitle = "Select your preferred candidate to continue";
?>
<div class="voting-container">
  
  <!-- VOTING HEADER -->
  <div class="voting-header-card">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
      <span class="badge badge-active" style="padding: 4px 12px; font-size: 0.8rem;">
        Live Official Ballot
      </span>
      <div style="display: flex; align-items: center; gap: 8px;">
        <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 0.75rem; border: 1px solid #cbd5e1;">
          One Person, One Vote (Strictly 1 Ballot Allowed)
        </span>
        <span style="font-size: 0.82rem; color: var(--text-muted);">Step 1 of 2: Selection</span>
      </div>
    </div>

    <h2 class="voting-election-title"><?= htmlspecialchars($election['title']) ?></h2>

    <div class="voting-meta-grid">
      <div class="voting-meta-col">
        <span>Election Type</span>
        <strong><?= htmlspecialchars($election['election_type']) ?></strong>
      </div>
      <div class="voting-meta-col">
        <span>Location / Constituency</span>
        <strong><?= htmlspecialchars($election['location']) ?></strong>
      </div>
      <div class="voting-meta-col">
        <span>Voting Period</span>
        <strong><?= htmlspecialchars($election['start_date']) ?> - <?= htmlspecialchars($election['end_date']) ?></strong>
      </div>
    </div>
  </div>

  <!-- 0. ONE VERIFIED VOTER, ONE VOTE PER ELECTION BANNER -->
  <?php if (!empty($has_already_voted)): ?>
  <div style="background-color: #dc2626; border-radius: var(--radius-md); padding: 1.1rem 1.25rem; font-size: 0.88rem; margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: 14px;">
    <svg width="26" height="26" fill="none" stroke="#ffffff" viewBox="0 0 24 24" stroke-width="2.2" style="flex-shrink: 0; margin-top: 2px;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>
    <div>
      <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        <strong style="font-size: 1rem; color: #ffffff;">One Verified Voter, One Vote Per Election</strong>
        <span class="badge" style="background: rgba(255, 255, 255, 0.16); color: #ffffff; font-size: 0.72rem; padding: 2px 8px;">Official Vote Cast</span>
      </div>
      <p style="margin-top: 4px; color: #fecaca; line-height: 1.45;">
        You have already voted in this election. You cannot submit another ballot for the same election.
      </p>
    </div>
  </div>
  <?php endif; ?>

  <!-- 1. AGE INELIGIBILITY BANNER (UNDER 18 YEARS OLD) -->
  <?php if (!empty($is_underage)): ?>
  <div style="background-color: #dc2626; border-radius: var(--radius-md); padding: 1.1rem 1.25rem; font-size: 0.88rem; margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: 14px;">
    <svg width="26" height="26" fill="none" stroke="#ffffff" viewBox="0 0 24 24" stroke-width="2.2" style="flex-shrink: 0; margin-top: 2px;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
    </svg>
    <div>
      <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        <strong style="font-size: 1rem; color: #ffffff;">Age Ineligibility: Below 18 Years Old (उमेर हद अयोग्यता)</strong>
        <span class="badge" style="background: rgba(255, 255, 255, 0.16); color: #ffffff; font-size: 0.72rem; padding: 2px 8px;">Age: <?= htmlspecialchars($user_age ?? '0') ?> Years</span>
      </div>
      <p style="margin-top: 4px; color: #fecaca; line-height: 1.45;">
        Under <strong>Article 84 of the Constitution of Nepal</strong> and Section 4 of the Electoral Rolls Act, only citizens who have completed <strong>18 years of age</strong> are legally eligible to vote. Because your recorded age is <strong><?= htmlspecialchars($user_age ?? '0') ?></strong>, this electronic ballot is strictly locked.
      </p>
    </div>
  </div>
  <?php endif; ?>

  <!-- 2. CITIZENSHIP IDENTITY VERIFICATION BANNER -->
  <?php if (!empty($has_citizenship)): ?>
  <div style="background-color: #166534; border-radius: var(--radius-md); padding: 0.85rem 1.25rem; font-size: 0.85rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
    <div style="display: flex; align-items: center; gap: 10px;">
      <svg width="22" height="22" fill="none" stroke="#ffffff" viewBox="0 0 24 24" stroke-width="2.2" style="flex-shrink: 0;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
      </svg>
      <div>
        <strong style="color: #ffffff;">Citizenship Identity Verified (नागरिकता प्रमाणित):</strong>
        <span style="font-family: monospace; background: rgba(255, 255, 255, 0.16); padding: 2px 8px; border-radius: 4px; font-weight: 700; color: #ffffff; margin-left: 4px;"><?= htmlspecialchars($voter['citizenship_no'] ?? '') ?></span>
        <?php if (empty($is_underage)): ?>
          <span style="margin-left: 6px; color: #d1fae5;">&bull; Age <?= htmlspecialchars($user_age ?? '0') ?> &bull; Eligible to vote</span>
        <?php endif; ?>
      </div>
    </div>
    <span class="badge" style="background: rgba(255, 255, 255, 0.16); color: #ffffff; font-size: 0.75rem;">Verified Citizen</span>
  </div>
  <?php else: ?>
  <div style="background-color: #dc2626; border-radius: var(--radius-md); padding: 1rem 1.25rem; font-size: 0.88rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
    <div style="display: flex; align-items: flex-start; gap: 10px;">
      <svg width="24" height="24" fill="none" stroke="#ffffff" viewBox="0 0 24 24" stroke-width="2.2" style="flex-shrink: 0; margin-top: 2px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      <div>
        <strong style="font-size: 0.95rem;">Citizenship Identity Required (नागरिकता आवश्यक):</strong>
        <p style="margin-top: 3px; color: #7f1d1d;">
          You cannot vote without a registered Nepali Citizenship Certificate Number. Please add your citizenship identity in your Voter Profile to activate your ballot.
        </p>
      </div>
    </div>
    <a href="<?= url_for('voter_profile') ?>" class="btn btn-crimson btn-sm">
      Update Citizenship in Profile
    </a>
  </div>
  <?php endif; ?>

  <!-- INSTRUCTIONS CALLOUT -->
  <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: var(--radius-md); padding: 0.85rem 1.25rem; font-size: 0.85rem; color: #1e40af; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="flex-shrink: 0;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>
      <?php if (!empty($has_already_voted)): ?>
        Ballot is locked because you have already voted in this election.
      <?php elseif (!empty($is_underage)): ?>
        Ballot is locked because voter is under 18 years of age. Underage individuals cannot cast votes.
      <?php elseif (empty($has_citizenship)): ?>
        Ballot is locked. You must verify your Nepali Citizenship Certificate to select a candidate.
      <?php else: ?>
        Please click on a candidate to mark your choice. You may submit one ballot for this election. You will confirm your selection on the next screen.
      <?php endif; ?>
    </span>
  </div>

  <!-- CANDIDATE SELECTION FORM -->
  <form action="<?= url_for('election_vote_confirm', ['election_id' => $election['id']]) ?>" method="GET">
    <input type="hidden" name="candidate_id" id="selected-candidate-id" value="" required>

    <div class="ballot-selection-list" <?= (empty($has_citizenship) || !empty($is_underage) || !empty($has_already_voted)) ? 'style="opacity: 0.55; pointer-events: none; filter: grayscale(50%);"' : '' ?>>
      <?php foreach (($election['candidates'] ?? []) as $candidate): ?>
      <div class="ballot-candidate-card" data-candidate-id="<?= $candidate['id'] ?>">
        <div class="ballot-left">
          <div class="custom-radio">
            <div class="custom-radio-inner"></div>
          </div>
          <div class="ballot-candidate-img">
            <img src="/static/images/candidates/<?= htmlspecialchars($candidate['photo_url']) ?>" alt="<?= htmlspecialchars($candidate['name']) ?>">
          </div>
          <div class="ballot-candidate-details">
            <h4><?= htmlspecialchars($candidate['name']) ?></h4>
            <p><?= htmlspecialchars($candidate['party']) ?></p>
          </div>
        </div>

        <div class="ballot-symbol-box">
          <img src="/static/images/<?= htmlspecialchars($candidate['symbol_image']) ?>" alt="<?= htmlspecialchars($candidate['symbol_name']) ?>" class="ballot-symbol-icon">
          <span class="ballot-symbol-name"><?= htmlspecialchars($candidate['symbol_name']) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- ACTION BUTTONS -->
    <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 1rem; border-top: 1px solid var(--border-color); flex-wrap: wrap; gap: 10px;">
      <a href="<?= url_for('election_detail', ['election_id' => $election['id']]) ?>" class="btn btn-outline">
        &larr; Cancel &amp; Return
      </a>

      <?php if (!empty($has_already_voted)): ?>
      <a href="<?= url_for('voting_status') ?>" class="btn btn-crimson btn-lg" style="padding-left: 1.5rem; padding-right: 1.5rem;">
        View Cast Vote Status
      </a>
      <?php elseif (!empty($is_underage)): ?>
      <button type="button" class="btn btn-crimson btn-lg" disabled style="padding-left: 1.5rem; padding-right: 1.5rem; opacity: 0.8; cursor: not-allowed;">
        Under 18 Ineligible to Vote (Age: <?= htmlspecialchars($user_age ?? '0') ?>)
      </button>
      <?php elseif (empty($has_citizenship)): ?>
      <a href="<?= url_for('voter_profile') ?>" class="btn btn-crimson btn-lg" style="padding-left: 1.5rem; padding-right: 1.5rem;">
        Add Citizenship to Vote
      </a>
      <?php else: ?>
      <button type="submit" id="btn-continue-vote" class="btn btn-primary btn-lg" disabled style="padding-left: 2rem; padding-right: 2rem;">
        Continue to Confirmation
      </button>
      <?php endif; ?>
    </div>
  </form>

</div>
