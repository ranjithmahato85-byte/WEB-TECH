<?php
$page_title = "Elections in Nepal - Nepal Online Voting System";
$section_title = "Elections Directory";
$section_subtitle = "Official federal, provincial, and local election contests";
?>
<div class="container" style="padding-top: 1.5rem; padding-bottom: 3rem;">

  <!-- ONE VERIFIED VOTER, ONE VOTE PER ELECTION BANNER -->
  <?php if (!empty($has_already_voted)): ?>
  <div style="background-color: #166534; border-radius: var(--radius-md); padding: 1.1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 14px; box-shadow: var(--shadow-sm);">
    <svg width="24" height="24" fill="none" stroke="#ffffff" viewBox="0 0 24 24" stroke-width="2.2" style="flex-shrink: 0; margin-top: 2px;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
    </svg>
    <div style="flex-grow: 1;">
      <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
        <strong style="color: #ffffff; font-size: 0.95rem;">One Verified Voter, One Vote Per Election</strong>
        <span class="badge" style="background: rgba(255, 255, 255, 0.16); color: #ffffff; font-size: 0.75rem;">Per-election limit</span>
      </div>
      <p style="color: #d1fae5; font-size: 0.85rem; margin: 4px 0 0 0; line-height: 1.45;">
        Under the electoral model, a verified voter may cast <strong>one vote in each separate election</strong>, but cannot vote twice in the same election.
      </p>
    </div>
    <a href="<?= url_for('voting_status') ?>" class="btn btn-outline btn-sm" style="background: #ffffff; flex-shrink: 0;">
      Audit Status Log
    </a>
  </div>
  <?php endif; ?>

  <!-- PAGE HEADER & ACTION -->
  <div class="section-header" style="margin-bottom: 1.5rem;">
    <div>
      <h1 class="section-title" style="font-size: 1.9rem; font-weight: 800; color: #0f172a; margin-bottom: 4px;">
        Elections in Nepal
      </h1>
      <p class="section-subtitle" style="font-size: 0.92rem; color: var(--text-muted);">
        Official federal, provincial and local elections conducted under Election Commission standards
      </p>
    </div>
    <a href="<?= url_for('candidates_list') ?>" class="btn btn-outline btn-sm">
      Explore Candidates
    </a>
  </div>

  <!-- FILTER TABS & SEARCH TOOLBAR -->
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem;">
    <div class="filter-tabs" style="margin-bottom: 0;">
      <button class="tab-btn active" data-filter="all">All Elections</button>
      <button class="tab-btn" data-filter="active">Active (Voting Open)</button>
      <button class="tab-btn" data-filter="upcoming">Upcoming</button>
      <button class="tab-btn" data-filter="completed">Completed</button>
    </div>

    <div style="position: relative; min-width: 260px; flex-grow: 0;">
      <input type="text" id="electionSearch" class="form-control" placeholder="Search elections..." style="padding-left: 36px; font-size: 0.88rem; height: 40px;">
      <svg style="position: absolute; left: 12px; top: 12px; width: 16px; height: 16px; color: var(--text-light);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
    </div>
  </div>

  <!-- ELECTIONS GRID -->
  <div class="elections-grid" id="electionsListGrid">
    <?php foreach (($elections ?? []) as $election): ?>
    <?php $voted = in_array($election['id'], $user_voted_election_ids ?? []); ?>
    <div class="election-card election-dir-item" data-status="<?= strtolower($election['status']) ?>" data-title="<?= strtolower($election['title']) ?>">
      <div class="election-card-image">
        <img src="/static/images/<?= htmlspecialchars($election['image_url'] ?: 'nepal-hero.jpg') ?>" alt="<?= htmlspecialchars($election['title']) ?>">
        <div class="election-badge-pos">
          <span class="badge badge-<?= strtolower($election['status']) ?>"><?= htmlspecialchars($election['status']) ?></span>
        </div>
      </div>

      <div class="election-card-body">
        <h3 class="election-card-title"><?= htmlspecialchars($election['title']) ?></h3>
        
        <div class="election-meta">
          <div class="election-meta-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span><?= htmlspecialchars($election['election_type']) ?> &bull; <?= htmlspecialchars($election['location']) ?></span>
          </div>
          <div class="election-meta-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Voting: <?= htmlspecialchars($election['start_date']) ?> - <?= htmlspecialchars($election['end_date']) ?></span>
          </div>
          <div class="election-meta-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span><?= count($election['candidates'] ?? []) ?> Contesting Candidates</span>
          </div>
        </div>

        <div class="election-card-footer">
          <a href="<?= url_for('election_detail', ['election_id' => $election['id']]) ?>" class="btn btn-outline btn-sm">
            View Details
          </a>

          <?php if ($election['status'] === 'Active'): ?>
            <?php if ($voted): ?>
              <span class="badge badge-active" style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd;">
                Voted
              </span>
            <?php else: ?>
              <a href="<?= url_for('election_vote', ['election_id' => $election['id']]) ?>" class="btn btn-primary btn-sm">
                Vote Now
              </a>
            <?php endif; ?>
          <?php elseif ($election['status'] === 'Completed'): ?>
            <a href="<?= url_for('results_detail', ['election_id' => $election['id']]) ?>" class="btn btn-outline-primary btn-sm">
              View Results
            </a>
          <?php else: ?>
            <span class="badge badge-upcoming">Starts Soon</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.filter-tabs .tab-btn');
    const cards = document.querySelectorAll('.election-dir-item');
    const searchInput = document.getElementById('electionSearch');

    function filterCards() {
      const activeTab = document.querySelector('.filter-tabs .tab-btn.active');
      const filter = activeTab ? activeTab.getAttribute('data-filter') : 'all';
      const term = searchInput ? searchInput.value.toLowerCase().trim() : '';

      cards.forEach(card => {
        const status = card.getAttribute('data-status');
        const title = card.getAttribute('data-title');
        const matchesTab = (filter === 'all' || status === filter);
        const matchesSearch = (!term || title.includes(term));

        if (matchesTab && matchesSearch) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    }

    tabs.forEach(tab => {
      tab.addEventListener('click', function() {
        tabs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        filterCards();
      });
    });

    if (searchInput) {
      searchInput.addEventListener('input', filterCards);
    }
  });
</script>
