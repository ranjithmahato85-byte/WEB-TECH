<?php
$page_title = "Admin Dashboard - Election Commission of Nepal";
$section_title = "Administrative Dashboard";
$section_subtitle = "National Electoral Oversight & Balloting Telemetry";
?>
<!-- 4 TOP STAT CARDS -->
<div class="stats-grid" style="margin-top: 0; margin-bottom: 2rem;">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
      </svg>
    </div>
    <div class="stat-info">
      <span class="stat-title">Total Elections</span>
      <span class="stat-value"><?= $total_elections_count ?? 0 ?></span>
      <span class="stat-subtext">Federal, Prov. &amp; Local</span>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon purple">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
      </svg>
    </div>
    <div class="stat-info">
      <span class="stat-title">Total Candidates</span>
      <span class="stat-value"><?= $total_candidates_count ?? 0 ?></span>
      <span class="stat-subtext">Nominated Across All</span>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon green">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
    </div>
    <div class="stat-info">
      <span class="stat-title">Registered Voters</span>
      <span class="stat-value"><?= $total_voters_count ?? 0 ?></span>
      <span class="stat-subtext">Verified Citizens</span>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-icon amber">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
      </svg>
    </div>
    <div class="stat-info">
      <span class="stat-title">Total Votes Cast</span>
      <span class="stat-value"><?= number_format($total_votes_count ?? 0) ?></span>
      <span class="stat-subtext">Ballots Counted</span>
    </div>
  </div>
</div>

<!-- ACTIVITY GRAPH & RECENT EVENTS ROW -->
<div style="display: grid; grid-template-columns: 1.3fr 0.7fr; gap: 1.5rem; margin-bottom: 2rem;" class="results-content-split">
  
  <!-- VOTING ACTIVITY SVG GRAPH -->
  <div class="table-card" style="padding: 1.5rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
      <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a;">Voting Activity Trend</h3>
      <span style="font-size: 0.8rem; color: var(--text-muted);">Cumulative Hourly Inflow</span>
    </div>

    <div style="width: 100%; height: 200px;">
      <svg viewBox="0 0 500 180" width="100%" height="100%" preserveAspectRatio="none">
        <line x1="40" y1="20" x2="480" y2="20" stroke="#f1f5f9" stroke-width="1"/>
        <line x1="40" y1="60" x2="480" y2="60" stroke="#f1f5f9" stroke-width="1"/>
        <line x1="40" y1="100" x2="480" y2="100" stroke="#f1f5f9" stroke-width="1"/>
        <line x1="40" y1="140" x2="480" y2="140" stroke="#f1f5f9" stroke-width="1"/>
        
        <text x="10" y="24" font-size="10" fill="#94a3b8">400</text>
        <text x="10" y="64" font-size="10" fill="#94a3b8">300</text>
        <text x="10" y="104" font-size="10" fill="#94a3b8">200</text>
        <text x="10" y="144" font-size="10" fill="#94a3b8">100</text>
        <text x="24" y="175" font-size="10" fill="#94a3b8">0</text>

        <polygon points="50,160 50,150 110,130 170,120 230,110 290,85 350,75 410,65 470,55 470,160" fill="rgba(37, 99, 235, 0.08)"/>

        <polyline points="50,150 110,130 170,120 230,110 290,85 350,75 410,65 470,55" fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round"/>

        <circle cx="50" cy="150" r="4" fill="#2563eb" stroke="#ffffff" stroke-width="2"/>
        <circle cx="110" cy="130" r="4" fill="#2563eb" stroke="#ffffff" stroke-width="2"/>
        <circle cx="170" cy="120" r="4" fill="#2563eb" stroke="#ffffff" stroke-width="2"/>
        <circle cx="230" cy="110" r="4" fill="#2563eb" stroke="#ffffff" stroke-width="2"/>
        <circle cx="290" cy="85" r="4" fill="#2563eb" stroke="#ffffff" stroke-width="2"/>
        <circle cx="350" cy="75" r="4" fill="#2563eb" stroke="#ffffff" stroke-width="2"/>
        <circle cx="410" cy="65" r="4" fill="#2563eb" stroke="#ffffff" stroke-width="2"/>
        <circle cx="470" cy="55" r="4" fill="#2563eb" stroke="#ffffff" stroke-width="2"/>

        <text x="40" y="175" font-size="10" fill="#94a3b8">Sep 1</text>
        <text x="100" y="175" font-size="10" fill="#94a3b8">Sep 3</text>
        <text x="160" y="175" font-size="10" fill="#94a3b8">Sep 5</text>
        <text x="220" y="175" font-size="10" fill="#94a3b8">Sep 7</text>
        <text x="280" y="175" font-size="10" fill="#94a3b8">Sep 9</text>
        <text x="340" y="175" font-size="10" fill="#94a3b8">Sep 10</text>
        <text x="400" y="175" font-size="10" fill="#94a3b8">Sep 15</text>
        <text x="455" y="175" font-size="10" fill="#94a3b8">Today</text>
      </svg>
    </div>
  </div>

  <!-- RECENT SYSTEM ACTIVITIES -->
  <div class="table-card" style="padding: 1.5rem;">
    <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">
      Recent System Events
    </h3>

    <div style="display: flex; flex-direction: column; gap: 14px;">
      <div style="display: flex; align-items: flex-start; gap: 12px;">
        <span style="width: 28px; height: 28px; border-radius: 50%; background: #16a34a; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
          <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </span>
        <div>
          <strong style="font-size: 0.88rem; color: #0f172a; display: block;">New ballot cast</strong>
          <span style="font-size: 0.76rem; color: var(--text-muted);">Verified voter submitted ballot &bull; 5 mins ago</span>
        </div>
      </div>

      <div style="display: flex; align-items: flex-start; gap: 12px;">
        <span style="width: 28px; height: 28px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
          <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        </span>
        <div>
          <strong style="font-size: 0.88rem; color: #0f172a; display: block;">Candidate profile validated</strong>
          <span style="font-size: 0.76rem; color: var(--text-muted);">Sita Bhattarai (Nepali Congress) &bull; 2 hours ago</span>
        </div>
      </div>

      <div style="display: flex; align-items: flex-start; gap: 12px;">
        <span style="width: 28px; height: 28px; border-radius: 50%; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
          <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </span>
        <div>
          <strong style="font-size: 0.88rem; color: #0f172a; display: block;">Election status: Active</strong>
          <span style="font-size: 0.76rem; color: var(--text-muted);">Federal Parliamentary Election 2026 &bull; 5 hours ago</span>
        </div>
      </div>

      <div style="display: flex; align-items: flex-start; gap: 12px;">
        <span style="width: 28px; height: 28px; border-radius: 50%; background: #f3e8ff; color: #9333ea; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
          <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </span>
        <div>
          <strong style="font-size: 0.88rem; color: #0f172a; display: block;">New voter registered</strong>
          <span style="font-size: 0.76rem; color: var(--text-muted);">Bagmati Province, Ward 10 &bull; 6 hours ago</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- QUICK ACTION MANAGEMENT PANELS -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem;">
  <a href="<?= url_for('admin_elections') ?>" class="table-card" style="padding: 1.5rem; text-decoration: none; color: inherit; transition: transform 0.2s;">
    <h4 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 4px;">Elections</h4>
    <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 12px;">Create, activate &amp; close electoral contests</p>
    <span style="color: var(--primary-blue); font-size: 0.85rem; font-weight: 600;">Manage</span>
  </a>

  <a href="<?= url_for('admin_candidates') ?>" class="table-card" style="padding: 1.5rem; text-decoration: none; color: inherit;">
    <h4 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 4px;">Candidates</h4>
    <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 12px;">Add candidate profiles and party symbols</p>
    <span style="color: var(--primary-blue); font-size: 0.85rem; font-weight: 600;">Manage</span>
  </a>

  <a href="<?= url_for('admin_voters') ?>" class="table-card" style="padding: 1.5rem; text-decoration: none; color: inherit;">
    <h4 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 4px;">Voters</h4>
    <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 12px;">Verify voter IDs and constituency statuses</p>
    <span style="color: var(--primary-blue); font-size: 0.85rem; font-weight: 600;">Manage</span>
  </a>

  <a href="<?= url_for('admin_results') ?>" class="table-card" style="padding: 1.5rem; text-decoration: none; color: inherit;">
    <h4 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 4px;">Results Audit</h4>
    <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 12px;">Audit certified results and vote tabulations</p>
    <span style="color: var(--primary-blue); font-size: 0.85rem; font-weight: 600;">Audit</span>
  </a>
</div>
