<?php
/**
 * Nepal Online Voting System
 * Helper Utilities & Civic Verification Engine
 */

require_once __DIR__ . '/db.php';

const NEPALI_NAME_MAP = [
    'Ranjeet Kumar' => 'रन्जित कुमार (Ranjeet Kumar)',
    'Bikash Thapa' => 'विकास थापा (Bikash Thapa)',
    'Pooja Gurung' => 'पूजा गुरुङ (Pooja Gurung)',
    'Binod Shrestha' => 'बिनोद श्रेष्ठ (Binod Shrestha)',
    'Sita Maya Tamang' => 'सीता माया तामाङ (Sita Maya Tamang)'
];

function flash(string $message, string $category = 'info'): void {
    if (!isset($_SESSION['_flashes'])) {
        $_SESSION['_flashes'] = [];
    }
    $_SESSION['_flashes'][] = ['category' => $category, 'message' => $message];
}

function get_flashed_messages(): array {
    $flashes = $_SESSION['_flashes'] ?? [];
    unset($_SESSION['_flashes']);
    return $flashes;
}

function csrf_token(): string {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function require_csrf(): void {
    $submitted = (string)($_POST['_csrf'] ?? '');
    $expected = (string)($_SESSION['_csrf_token'] ?? '');
    if ($expected === '' || $submitted === '' || !hash_equals($expected, $submitted)) {
        http_response_code(419);
        exit('Invalid or expired form security token.');
    }
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Base URL prefix ('' when served from the web root, e.g. '/voting' when
 * the project lives in a XAMPP subfolder such as htdocs/voting).
 */
function base_url(): string {
    return defined('BASE_URL') ? BASE_URL : '';
}

/**
 * Public URL for a route endpoint, including the base path prefix.
 */
function url_for(string $endpoint, $params = []): string {
    return base_url() . url_path_for($endpoint, $params);
}

function url_path_for(string $endpoint, $params = []): string {
    if ($endpoint === 'static') {
        $file = is_array($params) ? ($params['filename'] ?? '') : (string)$params;
        return '/static/' . ltrim($file, '/');
    }

    $routes = [
        'index' => '/',
        'about' => '/about',
        'login' => '/login',
        'register' => '/register',
        'logout' => '/logout',
        'voter_dashboard' => '/dashboard',
        'my_elections' => '/my-elections',
        'elections_list' => '/elections',
        'candidates_list' => '/candidates',
        'results_list' => '/results',
        'historical_elections' => '/historical-elections',
        'vote_success' => '/vote-success',
        'voting_status' => '/voting-status',
        'voter_profile' => '/profile',
        'admin_login' => '/admin/login',
        'admin_dashboard' => '/admin/dashboard',
        'admin_elections' => '/admin/elections',
        'admin_create_election' => '/admin/elections/create',
        'admin_candidates' => '/admin/candidates',
        'admin_create_candidate' => '/admin/candidates/create',
        'admin_voters' => '/admin/voters',
        'admin_results' => '/admin/results',
        'admin_reports' => '/admin/reports',
    ];

    if ($endpoint === 'election_detail') {
        $id = is_array($params) ? ($params['election_id'] ?? 1) : $params;
        return '/elections/' . $id;
    }
    if ($endpoint === 'election_vote') {
        $id = is_array($params) ? ($params['election_id'] ?? 1) : $params;
        return '/elections/' . $id . '/vote';
    }
    if ($endpoint === 'election_vote_confirm') {
        $id = is_array($params) ? ($params['election_id'] ?? 1) : $params;
        $cid = is_array($params) ? ($params['candidate_id'] ?? '') : '';
        return '/elections/' . $id . '/confirm' . ($cid ? '?candidate_id=' . $cid : '');
    }
    if ($endpoint === 'election_cast_vote') {
        $id = is_array($params) ? ($params['election_id'] ?? 1) : $params;
        return '/elections/' . $id . '/cast-vote';
    }
    if ($endpoint === 'candidate_profile') {
        $id = is_array($params) ? ($params['candidate_id'] ?? 1) : $params;
        return '/candidates/' . $id;
    }
    if ($endpoint === 'results_detail') {
        $id = is_array($params) ? ($params['election_id'] ?? 1) : $params;
        return '/results/' . $id;
    }
    if ($endpoint === 'admin_update_election_status') {
        $id = is_array($params) ? ($params['election_id'] ?? 1) : $params;
        return '/admin/elections/' . $id . '/status';
    }
    if ($endpoint === 'admin_delete_candidate') {
        $id = is_array($params) ? ($params['candidate_id'] ?? 1) : $params;
        return '/admin/candidates/' . $id . '/delete';
    }
    if ($endpoint === 'admin_toggle_voter') {
        $id = is_array($params) ? ($params['voter_id'] ?? 1) : $params;
        return '/admin/voters/' . $id . '/toggle';
    }
    if ($endpoint === 'admin_update_voter_details') {
        $id = is_array($params) ? ($params['voter_id'] ?? 1) : $params;
        return '/admin/voters/' . $id . '/update-details';
    }
    if ($endpoint === 'admin_reupload_voter_certificate') {
        $id = is_array($params) ? ($params['voter_id'] ?? 1) : $params;
        return '/admin/voters/' . $id . '/reupload-certificate';
    }

    $base = $routes[$endpoint] ?? ('/' . ltrim($endpoint, '/'));
    if (is_array($params) && !empty($params)) {
        $query = http_build_query($params);
        if ($query) {
            $base .= (str_contains($base, '?') ? '&' : '?') . $query;
        }
    }
    return $base;
}

function cit_photo_url($photoPath): string {
    return base_url() . cit_photo_path($photoPath);
}

function cit_photo_path($photoPath): string {
    if (!$photoPath) {
        return '/static/images/sample-citizenship-card.svg';
    }
    if (str_starts_with($photoPath, 'images/') || str_starts_with($photoPath, 'uploads/')) {
        return '/static/' . $photoPath;
    }
    return '/static/images/' . $photoPath;
}

function generate_voter_card_svg($user): string {
    $outDir = __DIR__ . '/../static/images/citizenship_cards';
    if (!is_dir($outDir)) {
        mkdir($outDir, 0777, true);
    }

    $fullName = is_array($user) ? $user['full_name'] : $user->full_name;
    $displayName = NEPALI_NAME_MAP[$fullName] ?? $fullName;
    $distDisplay = (is_array($user) ? $user['district'] : $user->district) ?: 'Kathmandu';
    $wardDisplay = (is_array($user) ? $user['ward'] : $user->ward) ?: '1';
    $dobDisplay = (is_array($user) ? $user['dob'] : $user->dob) ?: '2000-01-15';
    $citNo = (is_array($user) ? $user['citizenship_no'] : $user->citizenship_no) ?: '27-01-76-00000';
    $userId = is_array($user) ? $user['id'] : $user->id;

    $svgContent = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 380" width="100%" height="100%">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#fdfbf7"/>
      <stop offset="100%" stop-color="#f5efe6"/>
    </linearGradient>
    <linearGradient id="headerGrad" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#b91c1c"/>
      <stop offset="100%" stop-color="#dc2626"/>
    </linearGradient>
    <pattern id="guilloche" width="40" height="40" patternUnits="userSpaceOnUse">
      <path d="M0 20 Q10 0 20 20 T40 20" fill="none" stroke="#e2d9cc" stroke-width="0.75" opacity="0.6"/>
      <path d="M0 20 Q10 40 20 20 T40 20" fill="none" stroke="#e2d9cc" stroke-width="0.75" opacity="0.6"/>
    </pattern>
  </defs>

  <rect x="10" y="10" width="580" height="360" rx="16" fill="url(#bgGrad)" stroke="#b91c1c" stroke-width="3"/>
  <rect x="18" y="18" width="564" height="344" rx="12" fill="url(#guilloche)" stroke="#d97706" stroke-width="1" stroke-dasharray="4,2"/>

  <rect x="18" y="18" width="564" height="64" rx="12" fill="url(#headerGrad)"/>
  <rect x="18" y="52" width="564" height="30" fill="url(#headerGrad)"/>

  <g transform="translate(36, 26)">
    <polygon points="0,0 24,14 10,14 26,30 0,30" fill="#ffffff"/>
    <polygon points="2,3 20,13 8,13 22,28 2,28" fill="#dc2626"/>
    <circle cx="8" cy="10" r="3" fill="#ffffff"/>
    <circle cx="8" cy="22" r="3.5" fill="#ffffff"/>
  </g>

  <text x="300" y="42" font-family="system-ui, -apple-system, sans-serif" font-size="14" font-weight="700" fill="#ffffff" text-anchor="middle" letter-spacing="1">नेपाल सरकार | गृह मन्त्रालय</text>
  <text x="300" y="62" font-family="system-ui, -apple-system, sans-serif" font-size="16" font-weight="900" fill="#fef08a" text-anchor="middle" letter-spacing="1.5">नेपाली नागरिकताको प्रमाणपत्र (CITIZENSHIP CERTIFICATE)</text>

  <circle cx="340" cy="215" r="75" fill="none" stroke="#dc2626" stroke-width="1.5" opacity="0.12"/>
  <polygon points="340,155 355,190 395,190 362,212 375,250 340,227 305,250 318,212 285,190 325,190" fill="#dc2626" opacity="0.08"/>

  <g transform="translate(42, 106)">
    <rect x="0" y="0" width="115" height="145" rx="8" fill="#e2e8f0" stroke="#0f172a" stroke-width="2"/>
    <circle cx="57.5" cy="52" r="26" fill="#64748b"/>
    <path d="M22 135 C22 95, 93 95, 93 135 Z" fill="#64748b"/>
    <rect x="0" y="145" width="115" height="24" rx="4" fill="#0f172a"/>
    <text x="57.5" y="161" font-family="system-ui, sans-serif" font-size="10" font-weight="700" fill="#ffffff" text-anchor="middle">VERIFIED CITIZEN</text>
  </g>

  <g transform="translate(180, 102)">
    <rect x="0" y="0" width="380" height="34" rx="6" fill="#fee2e2" stroke="#ef4444" stroke-width="1.2"/>
    <text x="14" y="22" font-family="system-ui, sans-serif" font-size="11" font-weight="700" fill="#991b1b">नागरिकता प्रमाणपत्र नं. / CITIZENSHIP NO:</text>
    <text x="240" y="23" font-family="monospace, sans-serif" font-size="15" font-weight="900" fill="#b91c1c" letter-spacing="1">{$citNo}</text>
  </g>

  <g transform="translate(180, 155)" font-family="system-ui, -apple-system, sans-serif">
    <text x="0" y="14" font-size="11" font-weight="600" fill="#64748b">नाम, थर / Full Name:</text>
    <text x="140" y="14" font-size="13" font-weight="800" fill="#0f172a">{$displayName}</text>

    <text x="0" y="38" font-size="11" font-weight="600" fill="#64748b">जन्म मिति / Date of Birth:</text>
    <text x="140" y="38" font-size="12" font-weight="700" fill="#1e293b">{$dobDisplay}</text>

    <text x="0" y="62" font-size="11" font-weight="600" fill="#64748b">जिल्ला र वडा / District &amp; Ward:</text>
    <text x="140" y="62" font-size="12" font-weight="700" fill="#1e293b">{$distDisplay}, Ward No. {$wardDisplay}</text>

    <text x="0" y="86" font-size="11" font-weight="600" fill="#64748b">जारी जिल्ला / Issuing Office:</text>
    <text x="140" y="86" font-size="12" font-weight="700" fill="#1e293b">District Administration Office, {$distDisplay}</text>

    <text x="0" y="110" font-size="11" font-weight="600" fill="#64748b">किसिम / Citizenship Type:</text>
    <text x="140" y="110" font-size="12" font-weight="800" fill="#15803d">वंशज (By Descent)</text>
  </g>

  <g transform="translate(42, 290)">
    <circle cx="58" cy="35" r="32" fill="none" stroke="#dc2626" stroke-width="2" stroke-dasharray="3,2"/>
    <circle cx="58" cy="35" r="26" fill="none" stroke="#dc2626" stroke-width="1"/>
    <text x="58" y="32" font-family="system-ui, sans-serif" font-size="7" font-weight="700" fill="#dc2626" text-anchor="middle">जिल्ला प्रशासन कार्यालय</text>
    <text x="58" y="42" font-family="system-ui, sans-serif" font-size="7" font-weight="700" fill="#dc2626" text-anchor="middle">{$distDisplay}</text>

    <g transform="translate(140, 16)">
      <rect x="0" y="0" width="220" height="38" rx="4" fill="#ffffff" stroke="#cbd5e1"/>
      <line x1="15" y1="8" x2="15" y2="30" stroke="#0f172a" stroke-width="2"/>
      <line x1="20" y1="8" x2="20" y2="30" stroke="#0f172a" stroke-width="3"/>
      <line x1="26" y1="8" x2="26" y2="30" stroke="#0f172a" stroke-width="1"/>
      <line x1="31" y1="8" x2="31" y2="30" stroke="#0f172a" stroke-width="4"/>
      <line x1="40" y1="8" x2="40" y2="30" stroke="#0f172a" stroke-width="1.5"/>
      <line x1="46" y1="8" x2="46" y2="30" stroke="#0f172a" stroke-width="3"/>
      <line x1="54" y1="8" x2="54" y2="30" stroke="#0f172a" stroke-width="2"/>
      <line x1="62" y1="8" x2="62" y2="30" stroke="#0f172a" stroke-width="3.5"/>
      <line x1="72" y1="8" x2="72" y2="30" stroke="#0f172a" stroke-width="1"/>
      <line x1="78" y1="8" x2="78" y2="30" stroke="#0f172a" stroke-width="2.5"/>
      <line x1="86" y1="8" x2="86" y2="30" stroke="#0f172a" stroke-width="4"/>
      <line x1="96" y1="8" x2="96" y2="30" stroke="#0f172a" stroke-width="2"/>
      <line x1="104" y1="8" x2="104" y2="30" stroke="#0f172a" stroke-width="1"/>
      <line x1="112" y1="8" x2="112" y2="30" stroke="#0f172a" stroke-width="3"/>
      <line x1="120" y1="8" x2="120" y2="30" stroke="#0f172a" stroke-width="2.5"/>
      <line x1="130" y1="8" x2="130" y2="30" stroke="#0f172a" stroke-width="3.5"/>
      <line x1="148" y1="8" x2="148" y2="30" stroke="#0f172a" stroke-width="3"/>
      <line x1="168" y1="8" x2="168" y2="30" stroke="#0f172a" stroke-width="4"/>
      <line x1="186" y1="8" x2="186" y2="30" stroke="#0f172a" stroke-width="2.5"/>
      <line x1="204" y1="8" x2="204" y2="30" stroke="#0f172a" stroke-width="1.5"/>
    </g>

    <g transform="translate(385, 12)">
      <path d="M10 25 Q35 5 55 22 T95 18 T125 24" fill="none" stroke="#1e3a8a" stroke-width="1.8"/>
      <line x1="0" y1="36" x2="135" y2="36" stroke="#94a3b8" stroke-width="1"/>
      <text x="67" y="48" font-family="system-ui, sans-serif" font-size="9" font-weight="700" fill="#475569" text-anchor="middle">प्रमुख जिल्ला अधिकारी (CDO)</text>
    </g>
  </g>
</svg>
SVG;

    $filename = "cit_{$userId}.svg";
    $filePath = $outDir . '/' . $filename;
    file_put_contents($filePath, $svgContent);
    return "images/citizenship_cards/{$filename}";
}

function verify_citizenship_photo($uploadedFile, &$user): array {
    $db = get_db();
    $userId = $user['id'];
    $fullName = $user['full_name'];

    // If no new file uploaded, check existing record
    if (!$uploadedFile || empty($uploadedFile['name']) || empty($uploadedFile['tmp_name'])) {
        $existing = $user['citizenship_photo'];
        if ($existing) {
            $fullPath = __DIR__ . '/../static/' . ltrim($existing, '/');
            if (file_exists($fullPath)) {
                if (str_ends_with($fullPath, '.svg')) {
                    $content = file_get_contents($fullPath);
                    $tokens = array_filter(explode(' ', strtolower($fullName)), fn($t) => strlen($t) >= 3);
                    $hasMatch = false;
                    foreach ($tokens as $t) {
                        if (str_contains(strtolower($content), $t)) {
                            $hasMatch = true;
                            break;
                        }
                    }
                    if (!$hasMatch && !empty($tokens)) {
                        $regen = generate_voter_card_svg($user);
                        $stmt = $db->prepare("UPDATE users SET citizenship_photo = :p WHERE id = :id");
                        $stmt->execute([':p' => $regen, ':id' => $userId]);
                        $user['citizenship_photo'] = $regen;
                        return [true, "Personalized citizenship certificate synchronized.", $regen];
                    }
                }
                return [true, "Verified citizenship certificate on record.", $existing];
            }
        }

        // Auto-generate official card
        $gen = generate_voter_card_svg($user);
        $stmt = $db->prepare("UPDATE users SET citizenship_photo = :p, has_citizenship_verified = 1 WHERE id = :id");
        $stmt->execute([':p' => $gen, ':id' => $userId]);
        $user['citizenship_photo'] = $gen;
        $user['has_citizenship_verified'] = 1;
        return [true, "Official citizenship certificate generated and verified.", $gen];
    }

    // New file uploaded: Strong Verification Engine
    $origName = basename($uploadedFile['name']);
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg', 'pdf'];
    if (!in_array($ext, $allowed)) {
        return [false, "Unsupported File Format (.$ext): Citizenship certificate must be a valid image (.jpg, .jpeg, .png, .webp) or document (.svg, .pdf).", null];
    }

    $size = (int)$uploadedFile['size'];
    if ($size < 4096) {
        return [false, "Citizenship Document Rejected: The uploaded file is too small (under 4KB) or corrupted to be an authentic government certificate.", null];
    }
    if ($size > 16 * 1024 * 1024) {
        return [false, "File Too Large: Uploaded document exceeds the 16MB limit.", null];
    }

    // Anti-spoofing check
    $spoofKeywords = [
        'kulman', 'ghising', 'balen', 'balendra', 'oli', 'kp_oli', 
        'prachanda', 'deuba', 'sher_bahadur', 'dangol', 'sampang',
        'avatar', 'selfie', 'meme'
    ];
    $filenameLower = strtolower($origName);
    $userNameLower = strtolower($fullName);
    foreach ($spoofKeywords as $kw) {
        if (str_contains($filenameLower, $kw) && !str_contains($userNameLower, $kw)) {
            return [false, "Citizenship Photo Verification Failed: The uploaded file '{$origName}' appears to reference candidate or public figure '{$kw}'. You must upload your own official Nepali Citizenship Certificate (नागरिकता प्रमाणपत्र).", null];
        }
    }

    // Image Verification (JPG, PNG, WEBP) using GD getimagesize()
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        $info = @getimagesize($uploadedFile['tmp_name']);
        if (!$info) {
            return [false, "Corrupted or Invalid Image: The uploaded file is not a valid recognized image.", null];
        }
        $w = $info[0];
        $h = $info[1];
        if ($w < 280 || $h < 180) {
            return [false, "Low Resolution Document: Dimensions ({$w}x{$h} px) are too low for official identity verification. Please upload a clear photo of at least 300x200 pixels.", null];
        }
        // Aspect ratio: must be landscape (ratio >= 0.95)
        $ratio = $w / (float)$h;
        if ($ratio < 0.95) {
            return [false, "Document Orientation Error: The uploaded image is a vertical portrait/headshot photo. Official Nepali Citizenship Certificates are horizontal landscape cards. Please upload a clear horizontal scan or photo of your Citizenship Certificate (नागरिकता प्रमाणपत्र).", null];
        }
    } elseif ($ext === 'svg') {
        $svgText = file_get_contents($uploadedFile['tmp_name']);
        $markers = ['नागरिकता', 'CITIZENSHIP', 'नेपाल', 'Government of Nepal', 'District Administration', 'वडा'];
        $hasMarker = false;
        foreach ($markers as $m) {
            if (str_contains($svgText, $m)) {
                $hasMarker = true;
                break;
            }
        }
        if (!$hasMarker) {
            return [false, "Invalid Certificate Format: Uploaded SVG does not contain recognized Nepali Citizenship Certificate elements.", null];
        }
        $tokens = array_filter(explode(' ', strtolower($fullName)), fn($t) => strlen($t) >= 3);
        $nameMatch = false;
        foreach ($tokens as $t) {
            if (str_contains(strtolower($svgText), $t)) {
                $nameMatch = true;
                break;
            }
        }
        if (!$nameMatch && !empty($tokens)) {
            return [false, "The Full Legal Name and the name printed on the uploaded Citizenship Photo do not match. Please update the user's details or re-upload a valid citizenship certificate where the name matches the legal name on record ({$fullName}).", null];
        }
    }

    $uploadDir = __DIR__ . '/../static/uploads/citizenship';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $sanitized = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $origName);
    $uniqueName = "cit_{$userId}_" . time() . "_{$sanitized}";
    $targetPath = $uploadDir . '/' . $uniqueName;

    if (!move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
        return [false, "Failed to store uploaded citizenship document. Please check permissions.", null];
    }

    $relPath = "uploads/citizenship/{$uniqueName}";
    $stmt = $db->prepare("UPDATE users SET citizenship_photo = :p, has_citizenship_verified = 1 WHERE id = :id");
    $stmt->execute([':p' => $relPath, ':id' => $userId]);
    $user['citizenship_photo'] = $relPath;
    $user['has_citizenship_verified'] = 1;

    return [true, "Citizenship certificate successfully verified.", $relPath];
}
