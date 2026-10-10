<?php
$page_title = "Confirm Your Vote - " . htmlspecialchars($election['title']);
$section_title = "Vote Verification";
$section_subtitle = "Step 2 of 2: Review & Final Confirmation";
?>
<div class="confirm-card">
  <div class="confirm-header">
    <span class="badge-pill-soft" style="margin-bottom: 8px;">Final Review</span>
    <h2 class="confirm-title">Confirm Your Vote</h2>
    <p class="confirm-subtitle">Verify that your chosen candidate accurately reflects your democratic choice</p>
  </div>

  <!-- ELECTION DETAILS ROW -->
  <div style="margin-bottom: 1.5rem; padding-bottom: 1.25rem; border-bottom: 1px solid var(--border-color);">
    <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Election</div>
    <div style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-top: 2px;"><?= htmlspecialchars($election['title']) ?></div>
    <div style="font-size: 0.85rem; color: #475569; margin-top: 2px;"><?= htmlspecialchars($election['election_type']) ?> &bull; <?= htmlspecialchars($election['location']) ?></div>
  </div>

  <!-- SELECTED CANDIDATE RECAP BOX -->
  <div class="selection-recap-box">
    <div class="recap-avatar">
      <img src="/static/images/candidates/<?= htmlspecialchars($candidate['photo_url']) ?>" alt="<?= htmlspecialchars($candidate['name']) ?>">
    </div>

    <div class="recap-details" style="flex-grow: 1;">
      <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Selected Candidate</span>
      <h3><?= htmlspecialchars($candidate['name']) ?></h3>
      <div class="recap-party"><?= htmlspecialchars($candidate['party']) ?></div>
    </div>

    <div style="text-align: center; background: #ffffff; padding: 10px 16px; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
      <img src="/static/images/<?= htmlspecialchars($candidate['symbol_image']) ?>" alt="<?= htmlspecialchars($candidate['symbol_name']) ?>" style="width: 36px; height: 36px; margin: 0 auto 4px;">
      <div style="font-size: 0.78rem; font-weight: 700; color: #334155;"><?= htmlspecialchars($candidate['symbol_name']) ?></div>
    </div>
  </div>

  <!-- WARNING CALLOUT -->
  <div class="warning-callout">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <div>
      <strong>Irreversible Action:</strong>
      <p style="margin-top: 2px; font-size: 0.85rem;">
        Please review your selection carefully. Under the Election Commission's secret ballot protocol, your vote cannot be altered, cancelled, or recast once submitted.
      </p>
    </div>
  </div>

  <!-- CITIZENSHIP IDENTITY VERIFICATION CARD -->
  <div style="background: #f8fafc; border: 2px solid #cbd5e1; border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem;">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1rem;">
      <div style="width: 38px; height: 38px; border-radius: 50%; background: #dbeafe; display: flex; align-items: center; justify-content: center; color: #1d4ed8; flex-shrink: 0;">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
      </div>
      <div>
        <h4 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0;">
          Citizenship Identity Verification (नागरिकता प्रमाणीकरण)
        </h4>
        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">
          Mandatory identity authorization required by the Election Commission of Nepal before casting ballot
        </p>
      </div>
    </div>

    <!-- FORM SUBMISSION -->
    <form action="<?= url_for('election_cast_vote', ['election_id' => $election['id']]) ?>" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="candidate_id" value="<?= $candidate['id'] ?>">

      <!-- 1. CITIZENSHIP NUMBER INPUT -->
      <div class="form-group" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
          <label class="form-label" for="citizenship_no" style="font-weight: 700; margin-bottom: 0;">
            Enter Your Nepali Citizenship Certificate Number <span class="required" style="color: #dc2626;">*</span>
          </label>
          <?php if (!empty($voter['citizenship_no'])): ?>
          <button type="button" id="btn-autofill-cit" class="btn btn-outline btn-sm" style="font-size: 0.75rem; padding: 2px 10px; background: #ffffff;">
            Auto-Fill (<?= htmlspecialchars($voter['citizenship_no']) ?>)
          </button>
          <?php endif; ?>
        </div>
        <div style="position: relative;">
          <input 
            type="text" 
            id="citizenship_no" 
            name="citizenship_no" 
            class="form-control" 
            placeholder="e.g. 27-01-76-08891" 
            style="font-size: 1.05rem; letter-spacing: 0.5px; font-weight: 600; padding-left: 2.5rem;" 
            required 
            autocomplete="off"
          >
          <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
            </svg>
          </div>
        </div>
        <div style="font-size: 0.8rem; color: #64748b; margin-top: 6px;">
          <?php if (!empty($voter['citizenship_no'])): ?>
          Your citizenship certificate number on record is <strong><?= htmlspecialchars($voter['citizenship_no']) ?></strong>. Only citizens with a valid matching certificate can vote.
          <?php else: ?>
          <span style="color: #dc2626;">No citizenship identity found on record. You must add it to your profile to vote.</span>
          <?php endif; ?>
        </div>
      </div>

      <!-- 2. CITIZENSHIP PHOTO SUBMISSION SECTION -->
      <div style="margin-bottom: 1.5rem; padding: 1.25rem; background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
          <label class="form-label" for="citizenship_photo" style="font-weight: 700; margin-bottom: 0; display: flex; align-items: center; gap: 6px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color: #2563eb;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Citizenship Certificate Photo (नागरिकता प्रमाणपत्रको फोटो)
          </label>
          <span class="badge badge-active" style="font-size: 0.72rem;">Optional or Upload New</span>
        </div>
        
        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 12px;">
          Upload a clear image of your Nepali Citizenship Card (front view) for biometric audit, or submit with your verified certificate on record.
        </p>

        <div style="display: grid; grid-template-columns: 1fr 180px; gap: 16px; align-items: center;" class="form-row">
          <!-- File Input Dropzone -->
          <div>
            <div style="border: 2px dashed #cbd5e1; border-radius: var(--radius-md); padding: 1rem; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.2s;" id="dropzone-box" onclick="document.getElementById('citizenship_photo').click()">
              <svg width="28" height="28" fill="none" stroke="#64748b" viewBox="0 0 24 24" stroke-width="1.8" style="margin: 0 auto 6px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
              </svg>
              <div style="font-size: 0.85rem; font-weight: 600; color: #0f172a;">
                Click to browse or drop citizenship photo
              </div>
              <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">
                Supports PNG, JPG, JPEG, WEBP or PDF (Max 16MB)
              </div>
              <input 
                type="file" 
                id="citizenship_photo" 
                name="citizenship_photo" 
                accept="image/*,application/pdf" 
                style="display: none;"
              >
            </div>
            <div id="file-name-display" style="font-size: 0.78rem; color: #16a34a; font-weight: 600; margin-top: 6px; display: none;">
              Selected: <span id="file-name-text"></span>
            </div>
            <div id="cit-precheck-warning" style="display: none; margin-top: 8px; padding: 8px 12px; background: #dc2626; border-radius: 6px; font-size: 0.78rem; color: #ffffff; text-align: left;">
              <span id="cit-precheck-warning-text"></span>
            </div>
          </div>

          <!-- Document Preview Card -->
          <div style="text-align: center;">
            <div style="width: 100%; height: 110px; border-radius: var(--radius-md); border: 1px solid #cbd5e1; overflow: hidden; background: #ffffff; box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: center; position: relative;">
              <img 
                id="cit-preview-img" 
                src="<?= cit_photo_url($voter['citizenship_photo'] ?? '') ?>" 
                alt="Citizenship Certificate Preview" 
                style="width: 100%; height: 100%; object-fit: contain; padding: 4px;"
              >
              <div id="cit-preview-badge" style="position: absolute; bottom: 4px; left: 4px; right: 4px; background: rgba(15, 23, 42, 0.75); color: #ffffff; font-size: 0.68rem; font-weight: 700; padding: 2px 4px; border-radius: 4px; text-transform: uppercase;">
                Verified on file
              </div>
            </div>
            <span id="cit-name-match-status" style="font-size: 0.72rem; color: #166534; font-weight: 700; margin-top: 4px; display: block;">
              Name Match: <?= htmlspecialchars($voter['full_name'] ?? '') ?>
            </span>
          </div>
        </div>

        <!-- Strong Verification Criteria Indicators -->
        <div style="margin-top: 14px; padding-top: 10px; border-top: 1px dashed #e2e8f0; display: flex; flex-wrap: wrap; gap: 12px; font-size: 0.74rem; color: #64748b;">
          <span style="display: inline-flex; align-items: center; gap: 4px;">
            <svg width="14" height="14" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Horizontal Landscape Card
          </span>
          <span style="display: inline-flex; align-items: center; gap: 4px;">
            <svg width="14" height="14" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            High Resolution Scan (&gt;=300x200)
          </span>
          <span style="display: inline-flex; align-items: center; gap: 4px;">
            <svg width="14" height="14" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            1 Citizen = 1 Ballot Policy
          </span>
        </div>
      </div>

      <div class="confirm-actions">
        <a href="<?= url_for('election_vote', ['election_id' => $election['id']]) ?>" class="btn btn-outline btn-lg">
          &larr; Change Selection
        </a>

        <button type="submit" id="btn-submit-cast" class="btn btn-crimson btn-lg" style="padding-left: 2.2rem; padding-right: 2.2rem; box-shadow: 0 4px 14px rgba(225, 29, 72, 0.35);">
          Verify Identity &amp; Cast Vote
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const autofillBtn = document.getElementById('btn-autofill-cit');
    const citInput = document.getElementById('citizenship_no');
    if (autofillBtn && citInput) {
      autofillBtn.addEventListener('click', function() {
        citInput.value = "<?= addslashes($voter['citizenship_no'] ?? '') ?>";
        citInput.focus();
      });
    }

    const fileInput = document.getElementById('citizenship_photo');
    const fileNameDisplay = document.getElementById('file-name-display');
    const fileNameText = document.getElementById('file-name-text');
    const previewImg = document.getElementById('cit-preview-img');
    const previewBadge = document.getElementById('cit-preview-badge');
    const dropzoneBox = document.getElementById('dropzone-box');
    const warningBox = document.getElementById('cit-precheck-warning');
    const warningText = document.getElementById('cit-precheck-warning-text');
    const voterName = "<?= strtolower(addslashes($voter['full_name'] ?? '')) ?>";

    const candidateKeywords = ['kulman', 'ghising', 'balen', 'balendra', 'oli', 'kp_oli', 'prachanda', 'deuba', 'avatar', 'selfie'];

    if (fileInput) {
      fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
          fileNameDisplay.style.display = 'block';
          fileNameText.textContent = file.name;
          dropzoneBox.style.borderColor = '#16a34a';
          dropzoneBox.style.background = '#f8fafc';
          warningBox.style.display = 'none';

          const fnLower = file.name.toLowerCase();
          for (let kw of candidateKeywords) {
            if (fnLower.includes(kw) && !voterName.includes(kw)) {
              warningBox.style.display = 'block';
              warningText.innerHTML = '<strong>Document Notice:</strong> File name appears to reference public figure ("' + kw + '"). Please ensure you upload your own Nepali Citizenship Certificate.';
              break;
            }
          }

          if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(evt) {
              previewImg.src = evt.target.result;
              previewBadge.textContent = 'Uploaded Document';
              previewBadge.style.background = 'rgba(22, 163, 74, 0.85)';

              const tempImg = new Image();
              tempImg.onload = function() {
                if (tempImg.width < tempImg.height * 0.95) {
                  warningBox.style.display = 'block';
                  warningText.innerHTML = '<strong>Portrait/Headshot Detected:</strong> The selected file is vertical (' + tempImg.width + 'x' + tempImg.height + 'px). Official Nepali Citizenship Certificates are horizontal landscape cards. Please upload a clear horizontal scan or photo of your certificate.';
                  dropzoneBox.style.borderColor = '#e11d48';
                  dropzoneBox.style.background = '#fff1f2';
                }
              };
              tempImg.src = evt.target.result;
            };
            reader.readAsDataURL(file);
          } else {
            previewBadge.textContent = 'Document Attached';
          }
        }
      });
    }
  });
</script>
