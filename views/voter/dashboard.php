<?php
$page_title = "Voter Dashboard - Nepal Online Voting System";
$section_title = "Dashboard";
$section_subtitle = "Personal Voting Overview & Civic Activity";
?>
<!-- WELCOME BANNER -->
<div class="welcome-banner">
  <div>
    <h2 class="welcome-title">Welcome, <?= htmlspecialchars($current_user['full_name'] ?? 'Citizen') ?></h2>
    <p class="welcome-subtitle">
      Voter ID: <strong><?= htmlspecialchars($current_user['voter_id'] ?? '') ?></strong> &bull; Constituency: <strong><?= htmlspecialchars($current_user['district'] ?? '') ?> - Ward <?= htmlspecialchars($current_user['ward'] ?? '') ?>, <?= htmlspecialchars($current_user['province'] ?? '') ?> Province</strong>
    </p>
  </div>
  <div>
    <?php if (empty($current_user['is_verified'])): ?>
      <span class="btn btn-outline btn-sm" style="background: #fef3c7; color: #92400e; border-color: #fcd34d;">
        Verification Pending
      </span>
    <?php elseif (!empty($has_already_voted)): ?>
      <a href="<?= url_for('voting_status') ?>" class="btn btn-outline btn-sm" style="background: #ffffff; color: #0f172a; border-color: #cbd5e1;">
        View Voting Status
      </a>
    <?php else: ?>
      <a href="<?= url_for('my_elections') ?>" class="btn btn-crimson btn-sm">
        Cast Your Vote
      </a>
    <?php endif; ?>
  </div>
</div>

<?php if (empty($current_user['is_verified'])): ?>
<div style="background-color: #fffbeb; border: 1px solid #fcd34d; border-left: 4px solid #d97706; border-radius: var(--radius-md); padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
  <strong style="color: #92400e;">Your voter account is awaiting administrator verification.</strong>
  <p style="color: #78350f; font-size: 0.85rem; margin: 4px 0 0;">
    An Election Commission administrator must review your identity and citizenship details before you can vote.
  </p>
</div>
<?php endif; ?>

<?php if (!empty($has_already_voted)): ?>
<div style="background-color: #166534; border-radius: var(--radius-md); padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
  <div style="display: flex; align-items: center; gap: 10px;">
    <span class="badge" style="background: rgba(255, 255, 255, 0.16); color: #ffffff; font-size: 0.85rem; padding: 6px 14px;">Secure Session Active</span>
    <div>
      <strong style="color: #ffffff; font-size: 0.92rem;">One Verified Voter, One Vote Per Election</strong>
      <p style="color: #d1fae5; font-size: 0.82rem; margin: 2px 0 0 0;">
        Your vote has been securely recorded for this election. You may vote in another separate election if eligible.
      </p>
    </div>
  </div>
  <span class="badge" style="background: rgba(255, 255, 255, 0.16); color: #ffffff; font-size: 0.75rem;">Per-election limit</span>
</div>
<?php endif; ?>

<!-- STAT CARDS -->
<div class="stats-grid" style="margin-top: 0; margin-bottom: 2rem;">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
    </div>
    <div class="stat-info">
      <span class="stat-title">Active Elections</span>
      <span class="stat-value"><?= $active_elections_count ?? 0 ?></span>
      <span class="stat-subtext">Voting Open Now</span>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon amber">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>
    <div class="stat-info">
      <span class="stat-title">Upcoming Elections</span>
      <span class="stat-value"><?= $upcoming_elections_count ?? 0 ?></span>
      <span class="stat-subtext">Scheduled Soon</span>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon green">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
      </svg>
    </div>
    <div class="stat-info">
      <span class="stat-title">Completed Elections</span>
      <span class="stat-value"><?= $completed_elections_count ?? 0 ?></span>
      <span class="stat-subtext">Official Results Out</span>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon purple">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
      </svg>
    </div>
    <div class="stat-info">
      <span class="stat-title">Voting Status</span>
      <span class="stat-value" style="font-size: 1.15rem; color: #15803d;">
        <?= $participation_count ?? 0 ?> Voted
      </span>
      <span class="stat-subtext"><a href="<?= url_for('voting_status') ?>" style="color: var(--primary-blue);">View Status Log</a></span>
    </div>
  </div>
</div>

<!-- SECTION: UPCOMING ELECTIONS -->
<div style="margin-bottom: 2.5rem;">
  <div class="section-header">
    <div>
      <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a;">Upcoming Elections</h3>
      <p style="font-size: 0.85rem; color: var(--text-muted);">Upcoming federal and local elections scheduled in your jurisdiction</p>
    </div>
    <a href="<?= url_for('my_elections') ?>" class="btn btn-outline btn-sm">View All</a>
  </div>

  <div class="elections-grid">
    <?php foreach (($upcoming_elections ?? []) as $el): ?>
    <div class="election-card">
      <div class="election-card-image">
        <img src="/static/images/<?= htmlspecialchars($el['image_url'] ?: 'nepal-hero.jpg') ?>" alt="<?= htmlspecialchars($el['title']) ?>">
        <div class="election-badge-pos">
          <span class="badge badge-upcoming"><?= htmlspecialchars($el['status']) ?></span>
        </div>
      </div>
      <div class="election-card-body">
        <h4 class="election-card-title"><?= htmlspecialchars($el['title']) ?></h4>
        <div class="election-meta">
          <div class="election-meta-item">
            <span><?= htmlspecialchars($el['election_type']) ?> &bull; <?= htmlspecialchars($el['location']) ?></span>
          </div>
          <div class="election-meta-item">
            <span>Dates: <?= htmlspecialchars($el['start_date']) ?> - <?= htmlspecialchars($el['end_date']) ?></span>
          </div>
          <div class="election-meta-item">
            <span><?= count($el['candidates'] ?? []) ?> Candidates</span>
          </div>
        </div>
        <div class="election-card-footer">
          <a href="<?= url_for('election_detail', ['election_id' => $el['id']]) ?>" class="btn btn-outline btn-sm">
            View Details
          </a>
          <span class="badge badge-upcoming">Upcoming</span>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- SECTION: AVAILABLE ELECTIONS -->
<div style="margin-bottom: 2rem;">
  <div class="section-header">
    <div>
      <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a;">Available Elections</h3>
      <p style="font-size: 0.85rem; color: var(--text-muted);">Elections in which you are eligible to cast your ballot</p>
    </div>
  </div>

  <div class="table-card" style="padding: 1.5rem;">
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Election Title</th>
            <th>Type / Level</th>
            <th>Constituency</th>
            <th>Ballot Period</th>
            <th>Status</th>
            <th>Your Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($all_elections ?? []) as $el): ?>
          <?php $voted = in_array($el['id'], $user_voted_election_ids ?? []); ?>
          <tr>
            <td>
              <strong style="color: #0f172a; font-size: 0.95rem;"><?= htmlspecialchars($el['title']) ?></strong>
            </td>
            <td><?= htmlspecialchars($el['election_type']) ?></td>
            <td><?= htmlspecialchars($el['location']) ?></td>
            <td><?= htmlspecialchars($el['start_date']) ?> - <?= htmlspecialchars($el['end_date']) ?></td>
            <td>
              <span class="badge badge-<?= strtolower($el['status']) ?>"><?= htmlspecialchars($el['status']) ?></span>
            </td>
            <td>
              <?php if ($el['status'] === 'Active'): ?>
                <?php if ($voted): ?>
                  <span class="badge badge-active" style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd;">
                    Already Voted
                  </span>
                <?php elseif (!empty($has_already_voted)): ?>
                  <span class="badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;" title="You have already voted in this election.">
                    1 Vote Cast
                  </span>
                <?php else: ?>
                  <a href="<?= url_for('election_vote', ['election_id' => $el['id']]) ?>" class="btn btn-primary btn-sm">
                    Vote Now
                  </a>
                <?php endif; ?>
              <?php elseif ($el['status'] === 'Completed'): ?>
                <a href="<?= url_for('results_detail', ['election_id' => $el['id']]) ?>" class="btn btn-outline btn-sm">
                  View Results
                </a>
              <?php else: ?>
                <a href="<?= url_for('election_detail', ['election_id' => $el['id']]) ?>" class="btn btn-outline btn-sm">
                  View Details
                </a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
