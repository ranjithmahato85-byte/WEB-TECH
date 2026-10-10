<?php
$page_title = "Nepal Online Voting System - Secure Digital Voting for Nepal";
?>
<!-- HERO: asymmetric split, trust-first civic register -->
<section class="hero-section">
  <div class="hero-ambient-orb orb-left"></div>
  <div class="hero-ambient-orb orb-right"></div>

  <div class="container hero-grid">
    <div class="hero-content">
      <!-- Element 1: live badge (eyebrow) -->
      <div class="hero-live-badge">
        <span class="hero-badge-np">संघीय लोकतान्त्रिक गणतन्त्र नेपाल</span>
        <span class="hero-badge-sep">&bull;</span>
        <span class="hero-badge-en">Official Digital Ballot Portal</span>
      </div>

      <!-- Element 2: headline (2 lines) -->
      <h1 class="hero-title">
        Secure Digital<br>
        <span class="hero-gradient-text">Voting for Nepal</span>
      </h1>

      <!-- Element 3: subtext -->
      <p class="hero-description">
        A tamper-evident digital balloting platform for transparent, secret, and audited federal, provincial, and local elections.
      </p>

      <!-- Element 4: CTAs -->
      <div class="hero-actions">
        <?php if (!empty($current_user)): ?>
          <a href="<?= url_for('my_elections') ?>" class="btn btn-crimson btn-lg hero-btn-glow">
            <span>Vote Now</span>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </a>
          <a href="<?= url_for('voter_dashboard') ?>" class="btn btn-hero-secondary btn-lg">
            <span>My Dashboard</span>
          </a>
        <?php else: ?>
          <a href="<?= url_for('login') ?>" class="btn btn-crimson btn-lg hero-btn-glow">
            <span>Vote Now</span>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </a>
          <a href="<?= url_for('elections_list') ?>" class="btn btn-hero-secondary btn-lg">
            <span>View Elections</span>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Hero visual with glass overlays -->
    <div class="hero-visual">
      <div class="hero-glow-backdrop"></div>
      <div class="hero-image-card">
        <img src="/static/images/parliament.jpg" alt="Himalayas and a traditional Nepali pagoda">
      </div>
    </div>
  </div>
</section>

<!-- TRUST STRIP: real statistics, directly under the hero -->
<section class="trust-strip">
  <div class="container trust-strip-grid">
    <div class="trust-stat">
      <div class="trust-stat-value">17,988,570</div>
      <div class="trust-stat-label">Registered Electorate (2079)</div>
    </div>
    <div class="trust-stat">
      <div class="trust-stat-value">7</div>
      <div class="trust-stat-label">Provinces Covered</div>
    </div>
    <div class="trust-stat">
      <div class="trust-stat-value">753</div>
      <div class="trust-stat-label">Local Units Modeled</div>
    </div>
    <div class="trust-stat">
      <div class="trust-stat-value">61.4%</div>
      <div class="trust-stat-label">2079 National Turnout</div>
    </div>
  </div>
</section>

<!-- KEY CAPABILITIES: asymmetric 2-column split -->
<section class="section" style="background-color: #ffffff; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
  <div class="container">
    <div class="section-header-centered">
      <h2 class="section-title">Key System Features</h2>
      <p class="section-subtitle">Built to uphold democratic integrity, voter privacy, and administrative transparency</p>
    </div>

    <div class="capability-split">
      <!-- Left: lead feature, image-anchored -->
      <div class="capability-lead">
        <div class="capability-lead-text">
          <h3 class="capability-title">Ballot Secrecy Protocol</h3>
          <p class="capability-desc">
            Individual selections are decoupled from voter identity in the database. Even system administrators cannot trace which candidate a voter chose.
          </p>
        </div>
        <div class="capability-lead-media">
          <img src="/static/images/nepal-flag.svg" alt="Flag of Nepal" style="width: 64px; height: 80px; margin: 0 auto; filter: drop-shadow(0 6px 14px rgba(15, 23, 42, 0.18));">
        </div>
      </div>

      <!-- Right: supporting features stacked with hairlines -->
      <div class="capability-list">
        <div class="capability-row">
          <div class="feature-icon-wrapper" style="width: 44px; height: 44px; margin-bottom: 0; flex-shrink: 0;">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          </div>
          <div>
            <h3 class="capability-title" style="font-size: 1.02rem;">Voter ID &amp; Ward Verification</h3>
            <p class="capability-desc" style="font-size: 0.88rem;">
              Eligibility is verified against Nepal's 7 provinces, 77 districts, and municipal ward allocations before a ballot can be cast.
            </p>
          </div>
        </div>

        <div class="capability-row">
          <div class="feature-icon-wrapper" style="width: 44px; height: 44px; margin-bottom: 0; flex-shrink: 0;">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          </div>
          <div>
            <h3 class="capability-title" style="font-size: 1.02rem;">Real-time Transparent Tally</h3>
            <p class="capability-desc" style="font-size: 0.88rem;">
              Automated tabulation removes human counting errors, with clear turnout metrics and candidate percentage distributions.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="section">
  <div class="container">
    <div class="section-header-centered">
      <h2 class="section-title">How The System Works</h2>
      <p class="section-subtitle">A four-stage process designed for every citizen</p>
    </div>

    <div class="steps-grid">
      <div class="step-card">
        <div class="step-number">1</div>
        <h3 class="step-title">Register</h3>
        <p class="step-desc">Sign up with your citizenship or voter ID, provincial address, and local ward.</p>
      </div>

      <div class="step-card">
        <div class="step-number">2</div>
        <h3 class="step-title">Browse</h3>
        <p class="step-desc">Review active elections, candidate biographies, and certified party symbols.</p>
      </div>

      <div class="step-card">
        <div class="step-number">3</div>
        <h3 class="step-title">Vote</h3>
        <p class="step-desc">Select a candidate, confirm your choice on screen, and cast a secret ballot.</p>
      </div>

      <div class="step-card">
        <div class="step-number">4</div>
        <h3 class="step-title">Verify</h3>
        <p class="step-desc">Receive a tamper-evident confirmation number while your choice stays secret.</p>
      </div>
    </div>
  </div>
</section>
