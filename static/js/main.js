/**
 * Nepal Online Voting System - Client Side Interactions
 */

document.addEventListener('DOMContentLoaded', function () {
  // 1. Mobile Menu Toggle for Public Header
  const mobileBtn = document.querySelector('.mobile-menu-btn');
  const navLinks = document.querySelector('.nav-links');
  if (mobileBtn && navLinks) {
    mobileBtn.addEventListener('click', function () {
      navLinks.classList.toggle('active');
    });
  }

  // 2. Voting Selection Logic
  const ballotCards = document.querySelectorAll('.ballot-candidate-card');
  const selectedCandidateInput = document.getElementById('selected-candidate-id');
  const continueVoteBtn = document.getElementById('btn-continue-vote');

  if (ballotCards.length > 0 && selectedCandidateInput) {
    ballotCards.forEach(card => {
      card.addEventListener('click', function () {
        ballotCards.forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        const candId = this.getAttribute('data-candidate-id');
        selectedCandidateInput.value = candId;
        if (continueVoteBtn) {
          continueVoteBtn.disabled = false;
        }
      });
    });
  }

  // 3. Filter Tabs (My Elections / Admin)
  const tabBtns = document.querySelectorAll('.tab-btn');
  const filterableCards = document.querySelectorAll('[data-status]');

  if (tabBtns.length > 0) {
    tabBtns.forEach(btn => {
      btn.addEventListener('click', function () {
        tabBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const filter = this.getAttribute('data-filter');
        filterableCards.forEach(card => {
          const cardStatus = card.getAttribute('data-status');
          if (filter === 'all' || cardStatus.toLowerCase() === filter.toLowerCase()) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  }

  // 4. Candidate Directory Search & Filter
  const candidateSearchInput = document.getElementById('candidateSearch');
  const partyFilterSelect = document.getElementById('partyFilter');
  const candidateCards = document.querySelectorAll('.candidate-dir-card');

  function filterCandidates() {
    if (!candidateCards.length) return;
    const searchVal = (candidateSearchInput ? candidateSearchInput.value : '').toLowerCase().trim();
    const partyVal = (partyFilterSelect ? partyFilterSelect.value : 'all').toLowerCase();

    candidateCards.forEach(card => {
      const name = (card.getAttribute('data-name') || '').toLowerCase();
      const party = (card.getAttribute('data-party') || '').toLowerCase();

      const matchesSearch = !searchVal || name.includes(searchVal) || party.includes(searchVal);
      const matchesParty = partyVal === 'all' || party === partyVal;

      if (matchesSearch && matchesParty) {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });
  }

  if (candidateSearchInput) {
    candidateSearchInput.addEventListener('input', filterCandidates);
  }
  if (partyFilterSelect) {
    partyFilterSelect.addEventListener('change', filterCandidates);
  }

  // 5. Admin Modals
  const openModalBtns = document.querySelectorAll('[data-open-modal]');
  const closeModalBtns = document.querySelectorAll('[data-close-modal]');

  openModalBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      const targetId = this.getAttribute('data-open-modal');
      const targetModal = document.getElementById(targetId);
      if (targetModal) {
        targetModal.classList.add('show');
      }
    });
  });

  closeModalBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      const modal = this.closest('.modal-backdrop');
      if (modal) {
        modal.classList.remove('show');
      }
    });
  });

  // 6. Nepali / English Switcher handled cleanly by static/js/i18n.js

  // 7. Flash toast messages: auto-dismiss + manual close
  const toasts = document.querySelectorAll('.flash-messages .alert');

  function dismissToast(toast) {
    if (toast.dataset.dismissed) return;
    toast.dataset.dismissed = 'true';
    toast.classList.add('toast-hide');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const hideDelay = reduceMotion ? 0 : 250;
    setTimeout(() => toast.remove(), hideDelay);
  }

  toasts.forEach(toast => {
    // Auto-dismiss after 4 seconds
    setTimeout(() => dismissToast(toast), 4000);

    // Manual dismiss via the close glyph (::after is not clickable, so detect clicks on the toast's right edge)
    toast.addEventListener('click', function (e) {
      const rect = toast.getBoundingClientRect();
      if (e.clientX > rect.right - 34) {
        dismissToast(toast);
      }
    });
  });
});
