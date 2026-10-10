<?php
/**
 * Nepal Online Voting System
 * PHP Front Controller (XAMPP / Apache)
 *
 * Port of the original Flask application (app.py). All routing, request
 * handling and view rendering for the public site, voter portal and the
 * election-commission admin panel lives here; data access lives in
 * includes/models.php and templates in views/.
 *
 * Served behind Apache mod_rewrite via .htaccess.
 */

declare(strict_types=1);

// -----------------------------------------------------------------------------
// BASE PATH DETECTION
// Supports both a virtual-host install (http://voting.local/) and a XAMPP
// sub-folder install (http://localhost/voting/).
// -----------------------------------------------------------------------------
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$scriptDir = in_array($scriptDir, ['/', '.', ''], true) ? '' : rtrim($scriptDir, '/');
define('BASE_URL', $scriptDir);

require_once __DIR__ . '/includes/view.php';
require_once __DIR__ . '/includes/seed.php';

// Populate demo data on a brand new (empty) database; a copied voting.db is
// left untouched.
ensure_demo_data();

// -----------------------------------------------------------------------------
// REQUEST RESOLUTION
// -----------------------------------------------------------------------------
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if (BASE_URL !== '' && str_starts_with($requestPath, BASE_URL)) {
    $requestPath = substr($requestPath, strlen(BASE_URL));
}
$path   = '/' . trim($requestPath, '/');
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// -----------------------------------------------------------------------------
// SHARED HELPERS
// -----------------------------------------------------------------------------
function render_page(string $view, array $data = [], ?string $layout = null): void {
    // Pages reachable by both anonymous visitors and signed-in voters switch
    // between the public layout and the voter dashboard layout.
    if ($layout === null) {
        $layout = is_logged_in() ? 'voter' : 'base';
    }
    render_view($view, $data, $layout);
}

function not_found(): void {
    http_response_code(404);
    $home = htmlspecialchars(url_for('index'));
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 - Page Not Found | Nepal Online Voting System</title>
  <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
  <div class="container" style="padding: 5rem 1rem; text-align: center;">
    <h1 style="font-size: 3rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">404</h1>
    <p style="color: #64748b; margin-bottom: 1.5rem;">The page you requested could not be found.</p>
    <a href="{$home}" class="btn btn-primary">Return to Home</a>
  </div>
</body>
</html>
HTML;
    exit;
}

function redirect_to(string $endpoint, array $params = []): void {
    redirect(url_for($endpoint, $params));
}

function redirect_to_supabase(string $path): void {
    header('Location: ' . BASE_URL . '/pages/' . ltrim($path, '/'), true, 302);
    exit;
}

// =============================================================================
// 1. PUBLIC ROUTES
// =============================================================================
function handle_index(): void {
    render_page('index', [
        'elections'                 => election_all(false),
        'active_elections_count'    => election_count_by_status('Active'),
        'upcoming_elections_count'  => election_count_by_status('Upcoming'),
        'completed_elections_count' => election_count_by_status('Completed'),
        'total_voters'              => number_format(user_count_voters()),
        'total_candidates'          => candidate_count(),
        'featured_candidates'       => candidate_all(8),
    ], 'base');
}

function handle_about(): void {
    render_page('about', [], 'base');
}

// =============================================================================
// 2. AUTHENTICATION (LOGIN / REGISTER / LOGOUT)
// =============================================================================
function handle_login(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $next = trim((string)($_GET['next'] ?? ''));
        $query = $next !== '' ? '?next=' . rawurlencode($next) : '';
        header('Location: ' . BASE_URL . '/pages/auth/login.html' . $query, true, 302);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $loginId  = trim($_POST['username'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        $user = user_find_by_login($loginId);
        if ($user && verify_user_password($password, $user['password_hash'], $user)) {
            if (!$user['is_active']) {
                flash('Your account has been deactivated. Please contact the election administrator.', 'error');
                redirect_to('login');
            }

            user_login($user);
            flash('Welcome back, ' . $user['full_name'] . '!', 'success');
            redirect_to($user['role'] === 'admin' ? 'admin_dashboard' : 'voter_dashboard');
        }

        flash('Invalid username/email or password. Please try again.', 'error');
        redirect_to('login');
    }

    render_page('login', [], 'base');
}

function handle_register(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        header('Location: ' . BASE_URL . '/pages/auth/register.html', true, 302);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullName     = trim($_POST['full_name'] ?? '');
        $dob          = trim($_POST['dob'] ?? '');
        $email        = trim($_POST['email'] ?? '');
        $mobile       = trim($_POST['mobile'] ?? '');
        $province     = trim($_POST['province'] ?? '');
        $district     = trim($_POST['district'] ?? '');
        $municipality = trim($_POST['municipality'] ?? '');
        $ward         = trim($_POST['ward'] ?? '');
        $voterId      = trim($_POST['voter_id'] ?? '');
        $citizenship  = trim($_POST['citizenship_no'] ?? '');
        $username     = trim($_POST['username'] ?? '');
        $password     = (string)($_POST['password'] ?? '');
        $confirm      = (string)($_POST['confirm_password'] ?? '');

        $fail = function (string $message): void {
            flash($message, 'error');
            render_page('register', [], 'base');
        };

        if ($password !== $confirm) {
            $fail('Passwords do not match.');
            return;
        }
        if (strlen($password) < 6) {
            $fail('Password must be at least 6 characters long.');
            return;
        }
        if ($dob === '') {
            $fail('Date of Birth is required to verify legal voting age.');
            return;
        }

        // Age requirement: at least 18 years old (Article 84, Constitution of Nepal)
        try {
            $birthDate = new DateTime($dob);
            $today     = new DateTime();
            $calcAge   = $today->diff($birthDate)->y;
        } catch (Exception $e) {
            $fail('Invalid Date of Birth format. Please enter a valid date.');
            return;
        }
        if ($calcAge < 18) {
            $fail("Age Ineligibility: You are currently {$calcAge} years old. Under Article 84 of the Constitution of Nepal, individuals under 18 years of age are not legally eligible to register or vote.");
            return;
        }

        if ($citizenship === '') {
            $fail('Citizenship Identity is required. Please enter your Nepali Citizenship Certificate Number.');
            return;
        }
        if (user_find_by_citizenship($citizenship)) {
            $fail('This Citizenship Certificate Number is already registered in the system.');
            return;
        }
        if (user_find_by_login($username)) {
            $fail('Username is already taken. Please choose another.');
            return;
        }
        if (user_find_by_login($email)) {
            $fail('Email address is already registered.');
            return;
        }
        if (user_find_by_voter_id($voterId)) {
            $fail('Voter ID is already registered in the system.');
            return;
        }

        $newId = user_create([
            'full_name'                => $fullName,
            'username'                 => $username,
            'email'                    => $email,
            'password_hash'            => hash_password($password),
            'role'                     => 'voter',
            'dob'                      => $dob,
            'mobile'                   => $mobile,
            'province'                 => $province,
            'district'                 => $district,
            'municipality'             => $municipality,
            'ward'                     => $ward,
            'voter_id'                 => $voterId,
            'citizenship_no'           => $citizenship,
            'has_citizenship_verified' => 0,
            'is_verified'              => 0,
            'is_active'                => 1,
        ]);

        // Generate the matching official citizenship certificate for the new citizen
        $newUser = user_find_by_id($newId);
        if ($newUser) {
            user_update_fields($newId, ['citizenship_photo' => generate_voter_card_svg($newUser)]);
        }

        flash('Registration Successful! Please log in with your credentials.', 'success');
        redirect_to('login');
    }

    render_page('register', [], 'base');
}

function handle_logout(): void {
    user_logout();
    flash('You have been logged out safely.', 'info');
    redirect_to('index');
}

// =============================================================================
// 3. VOTER DASHBOARD & CONSTITUENCY VIEWS
// =============================================================================
function handle_voter_dashboard(): void {
    login_required();
    $user = current_user();

    $participations = user_participations($user['id']);
    $votedIds       = array_map(static fn($p) => (int)$p['election_id'], $participations);
    $priorVote      = get_prior_vote_for_user($user);

    render_page('voter/dashboard', [
        'active_elections_count'    => election_count_by_status('Active'),
        'upcoming_elections_count'  => election_count_by_status('Upcoming'),
        'completed_elections_count' => election_count_by_status('Completed'),
        'participation_count'       => count($participations),
        'upcoming_elections'        => election_all_by_status('Upcoming'),
        'all_elections'             => election_all(false),
        'user_voted_election_ids'   => $votedIds,
        'has_already_voted'         => $priorVote !== null,
        'prior_vote'                => $priorVote,
    ], 'voter');
}

function handle_my_elections(): void {
    login_required();
    $user = current_user();

    $participations = user_participations($user['id']);
    $priorVote      = get_prior_vote_for_user($user);

    render_page('voter/my_elections', [
        'elections'               => election_all_any(true),
        'user_voted_election_ids' => array_map(static fn($p) => (int)$p['election_id'], $participations),
        'active_elections_count'  => election_count_by_status('Active'),
        'has_already_voted'       => $priorVote !== null,
        'prior_vote'              => $priorVote,
    ], 'voter');
}

function handle_elections_list(): void {
    $user      = current_user();
    $votedIds  = [];
    $priorVote = null;

    if ($user) {
        $votedIds  = array_map(static fn($p) => (int)$p['election_id'], user_participations($user['id']));
        $priorVote = get_prior_vote_for_user($user);
    }

    render_page('elections/index', [
        'elections'               => election_all(false),
        'user_voted_election_ids' => $votedIds,
        'active_elections_count'  => election_count_by_status('Active'),
        'has_already_voted'       => $priorVote !== null,
        'prior_vote'              => $priorVote,
    ]);
}

function handle_election_detail(int $electionId): void {
    $election = election_find_by_id($electionId);
    if (!$election) {
        not_found();
    }

    $hasVoted        = false;
    $hasAlreadyVoted = false;
    if (is_logged_in()) {
        $user            = current_user();
        $participation   = user_has_voted_in_election($user['id'], $electionId);
        $hasVoted        = $participation !== null;
        $hasAlreadyVoted = get_prior_vote_for_user($user) !== null;
    }

    render_page('voter/election_detail', [
        'election'          => $election,
        'has_voted'         => $hasVoted,
        'has_already_voted' => $hasAlreadyVoted,
    ]);
}

// =============================================================================
// 4. VOTING WORKFLOW & AUDIT
// =============================================================================
function handle_election_vote(int $electionId): void {
    login_required();
    $election = election_find_by_id($electionId);
    if (!$election) {
        not_found();
    }

    if ($election['status'] !== 'Active') {
        flash('Voting is not currently active for this election.', 'warning');
        redirect_to('election_detail', ['election_id' => $election['id']]);
    }

    $user = current_user();

    if (empty($user['is_verified'])) {
        flash('Your voter account is awaiting Election Commission verification.', 'warning');
        redirect_to('voter_dashboard');
    }

    // Rule 1: One citizenship holder can vote only once across all elections
    $priorVote = get_prior_vote_for_user($user);
    if ($priorVote) {
        flash('One Verified Voter, One Vote Per Election: you have already voted in this election.', 'warning');
        redirect_to('voting_status');
    }

    // Rule 2: Minimum voting age (18+)
    $userAge    = (int)$user['age'];
    $isUnderage = $userAge < 18;
    if ($isUnderage) {
        flash("Voting Age Restriction: You are currently {$userAge} years old. Under Article 84 of the Constitution of Nepal, individuals below 18 years of age are not legally eligible to vote.", 'error');
    }

    render_page('voter/vote', [
        'election'          => $election,
        'has_citizenship'   => !empty($user['citizenship_no']) && !empty($user['has_citizenship_verified']),
        'voter'             => $user,
        'is_underage'       => $isUnderage,
        'user_age'          => $userAge,
        'has_already_voted' => false,
    ], 'voter');
}

function handle_election_vote_confirm(int $electionId): void {
    login_required();
    $election = election_find_by_id($electionId);
    if (!$election || $election['status'] !== 'Active') {
        flash('Invalid election contest.', 'error');
        redirect_to('my_elections');
    }

    $user = current_user();

    if (empty($user['is_verified'])) {
        flash('Your voter account is awaiting Election Commission verification.', 'warning');
        redirect_to('voter_dashboard');
    }

    // Rule 1: One citizenship, one vote
    if (get_prior_vote_for_user($user)) {
        flash('One Verified Voter, One Vote Per Election: you have already voted in this election.', 'error');
        redirect_to('voting_status');
    }

    // Rule 2: Minimum voting age (18+)
    $userAge = (int)$user['age'];
    if ($userAge < 18) {
        flash("Voting Blocked: You must be at least 18 years old to cast an official ballot (Current age: {$userAge} years old). Individuals under 18 are not eligible to vote.", 'error');
        redirect_to('election_vote', ['election_id' => $election['id']]);
    }

    // Rule 3: Citizenship identity requirement
    if (empty($user['citizenship_no']) || empty($user['has_citizenship_verified'])) {
        flash('Nepali Citizenship Identity Required: You cannot vote without a verified Nepali Citizenship Certificate. Please register your citizenship certificate in your profile.', 'error');
        redirect_to('voter_profile');
    }

    $candidateId = isset($_GET['candidate_id']) ? (int)$_GET['candidate_id'] : 0;
    if (!$candidateId) {
        flash('Please select a candidate first.', 'warning');
        redirect_to('election_vote', ['election_id' => $election['id']]);
    }

    $candidate = candidate_find_by_id($candidateId);
    if (!$candidate || (int)$candidate['election_id'] !== (int)$election['id']) {
        flash('Invalid candidate selected for this election.', 'error');
        redirect_to('election_vote', ['election_id' => $election['id']]);
    }

    render_page('voter/vote_confirm', [
        'election'  => $election,
        'candidate' => $candidate,
        'voter'     => $user,
    ], 'voter');
}

function handle_election_cast_vote(int $electionId): void {
    login_required();

    $user        = current_user();
    $candidateId = isset($_POST['candidate_id']) ? (int)$_POST['candidate_id'] : 0;

    if (empty($user['is_verified'])) {
        flash('Your voter account is awaiting Election Commission verification. Your vote was not submitted.', 'error');
        redirect_to('voter_dashboard');
    }

    $election = election_find_by_id($electionId);
    if (!$election || $election['status'] !== 'Active') {
        flash('This election is not active for voting.', 'error');
        redirect_to('my_elections');
    }

    $candidate = candidate_find_by_id($candidateId);
    if (!$candidate || (int)$candidate['election_id'] !== (int)$election['id']) {
        flash('Invalid candidate selection.', 'error');
        redirect_to('election_vote', ['election_id' => $election['id']]);
    }

    // Guard 0: one verified voter, one vote per election
    if (get_prior_vote_for_user($user)) {
        flash('One Verified Voter, One Vote Per Election: you have already voted in this election.', 'error');
        redirect_to('voting_status');
    }

    // Guard 1: legal voting age (18+)
    if (empty($user['is_eligible_age'])) {
        flash("Voting Prohibited: Underage individuals (under 18 years old) are not legally eligible to vote under Nepali law. Your current recorded age is {$user['age']}. Your vote was NOT submitted.", 'error');
        redirect_to('election_vote', ['election_id' => $election['id']]);
    }

    // Guard 2: registered Nepali citizenship identity
    if (empty($user['citizenship_no']) || empty($user['has_citizenship_verified'])) {
        flash('Nepali Citizenship Identity Required: You cannot vote without a registered Nepali Citizenship Certificate Number.', 'error');
        redirect_to('voter_profile');
    }

    // Guard 3: citizenship number confirmation from the ballot form
    $entered = trim($_POST['citizenship_no'] ?? '');
    if ($entered === '') {
        flash('Citizenship Identity Verification Required: Please enter your Nepali Citizenship Certificate Number to authorize and cast your vote.', 'error');
        redirect_to('election_vote_confirm', ['election_id' => $election['id'], 'candidate_id' => $candidate['id']]);
    }

    $cleanEntered = strtolower(str_replace(['-', ' '], '', $entered));
    $cleanStored  = strtolower(str_replace(['-', ' '], '', (string)$user['citizenship_no']));
    if ($cleanEntered !== $cleanStored) {
        flash('Citizenship Identity Verification Failed: The entered Citizenship Certificate Number does not match your official government record. Your vote was NOT submitted.', 'error');
        redirect_to('election_vote_confirm', ['election_id' => $election['id'], 'candidate_id' => $candidate['id']]);
    }

    // Guard 4: strong citizenship photo verification engine
    $uploadedPhoto = $_FILES['citizenship_photo'] ?? null;
    [$isValid, $message, $citPhotoPath] = verify_citizenship_photo($uploadedPhoto, $user);
    if (!$isValid) {
        flash($message, 'error');
        redirect_to('election_vote_confirm', ['election_id' => $election['id'], 'candidate_id' => $candidate['id']]);
    }

    // Guard 5: atomic re-check against double voting before commit
    if (get_prior_vote_for_user($user)) {
        flash('One Verified Voter, One Vote Per Election: you have already voted in this election.', 'error');
        redirect_to('voting_status');
    }

    // Tamper-evident confirmation code, e.g. NV20260901084523
    $confCode = 'NV' . date('Ymd') . random_int(100000, 999999);
    $now      = date('Y-m-d H:i:s');

    $db = get_db();
    $db->beginTransaction();
    try {
        // 1. Voter participation — ZERO candidate choice stored
        participation_create([
            'user_id'           => $user['id'],
            'election_id'       => $election['id'],
            'citizenship_no'    => $user['citizenship_no'],
            'confirmation_code' => $confCode,
            'citizenship_photo' => $citPhotoPath,
            'voted_at'          => $now,
        ]);

        // 2. Anonymous ballot — NO user id or citizenship stored
        ballot_create((int)$election['id'], (int)$candidate['id']);

        // 3. Increment candidate tally
        candidate_increment_votes((int)$candidate['id']);

        $db->commit();
    } catch (Throwable $e) {
        $db->rollBack();
        flash('Your ballot could not be recorded due to a system error. Please try again.', 'error');
        redirect_to('election_vote_confirm', ['election_id' => $election['id'], 'candidate_id' => $candidate['id']]);
    }

    $_SESSION['last_vote_receipt'] = [
        'confirmation_code' => $confCode,
        'election_title'    => $election['title'],
        'election_id'       => $election['id'],
        'vote_time'         => date('F d, Y \a\t h:i A'),
    ];

    redirect_to('vote_success');
}

function handle_vote_success(): void {
    login_required();
    $receipt = $_SESSION['last_vote_receipt'] ?? [];

    render_page('voter/vote_success', [
        'confirmation_code' => $receipt['confirmation_code'] ?? 'NV20260901084523',
        'election_title'    => $receipt['election_title'] ?? 'Federal Parliamentary Election 2026',
        'election_id'       => $receipt['election_id'] ?? null,
        'vote_time'         => $receipt['vote_time'] ?? date('F d, Y \a\t h:i A'),
    ], 'voter');
}

function handle_voting_status(): void {
    login_required();
    $user = current_user();

    $elections   = election_all(false);
    $votedByEl   = [];
    foreach (user_participations($user['id']) as $p) {
        $votedByEl[(int)$p['election_id']] = $p;
    }

    $participationData = [];
    foreach ($elections as $el) {
        $part = $votedByEl[(int)$el['id']] ?? null;
        $participationData[] = [
            'election'          => $el,
            'has_voted'         => $part !== null,
            'confirmation_code' => $part['confirmation_code'] ?? null,
            'voted_at'          => $part ? date('F d, Y', strtotime($part['voted_at'])) : null,
        ];
    }

    $priorVote = get_prior_vote_for_user($user);

    render_page('voter/voting_status', [
        'participation_data' => $participationData,
        'has_already_voted'  => $priorVote !== null,
        'prior_vote'         => $priorVote,
    ], 'voter');
}

function handle_voter_profile(): void {
    login_required();
    $user = current_user();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullName    = trim($_POST['full_name'] ?? '');
        $mobile      = trim($_POST['mobile'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $dob         = trim($_POST['dob'] ?? '');
        $province    = trim($_POST['province'] ?? '');
        $district    = trim($_POST['district'] ?? '');
        $municipality = trim($_POST['municipality'] ?? '');
        $ward        = trim($_POST['ward'] ?? '');
        $citizenship = trim($_POST['citizenship_no'] ?? '');

        if ($citizenship === '') {
            flash('Nepali Citizenship Certificate Number cannot be empty.', 'error');
            render_page('voter/profile', ['current_user' => $user], 'voter');
            return;
        }

        // Duplicate citizenship check
        $existing = user_find_by_citizenship($citizenship);
        if ($existing && (int)$existing['id'] !== (int)$user['id']) {
            flash('This Citizenship Certificate Number is already registered to another citizen account.', 'error');
            render_page('voter/profile', ['current_user' => $user], 'voter');
            return;
        }

        $fields = [
            'citizenship_no'           => $citizenship,
            'has_citizenship_verified' => 1,
        ];
        if ($fullName !== '')     $fields['full_name'] = $fullName;
        if ($mobile !== '')       $fields['mobile'] = $mobile;
        if ($email !== '')        $fields['email'] = $email;
        if ($dob !== '')          $fields['dob'] = $dob;
        if ($province !== '')     $fields['province'] = $province;
        if ($district !== '')     $fields['district'] = $district;
        if ($municipality !== '') $fields['municipality'] = $municipality;
        if ($ward !== '')         $fields['ward'] = $ward;

        user_update_fields((int)$user['id'], $fields);
        if ($fullName !== '') {
            $_SESSION['full_name'] = $fullName;
        }

        $user = user_find_by_id($user['id']);

        // Optional citizenship photo upload with strong verification
        $uploadedPhoto = $_FILES['citizenship_photo'] ?? null;
        if ($uploadedPhoto && !empty($uploadedPhoto['name'])) {
            [$isValid, $message, ] = verify_citizenship_photo($uploadedPhoto, $user);
            if (!$isValid) {
                flash($message, 'error');
                render_page('voter/profile', ['current_user' => $user], 'voter');
                return;
            }
        }

        flash('Citizen Profile and Citizenship Identity verified and updated successfully!', 'success');
        redirect_to('voter_profile');
    }

    render_page('voter/profile', ['current_user' => $user], 'voter');
}

// =============================================================================
// 5. CANDIDATES DIRECTORY & PROFILE
// =============================================================================
function handle_candidates_list(): void {
    render_page('candidates/index', [
        'candidates' => candidate_all(),
    ]);
}

function handle_candidate_profile(int $candidateId): void {
    $candidate = candidate_find_by_id($candidateId);
    if (!$candidate) {
        not_found();
    }
    render_page('candidates/detail', ['candidate' => $candidate]);
}

// =============================================================================
// 6. RESULTS & HISTORICAL ELECTIONS
// =============================================================================
function handle_results_list(): void {
    render_page('results/index', [
        'elections' => election_all_by_status('Completed', true),
    ]);
}

function handle_results_detail(int $electionId): void {
    $election = election_find_by_id($electionId);
    if (!$election) {
        not_found();
    }

    $totalRegistered = (int)($election['registered_voters_count'] ?: 1200);
    $candidates      = candidate_by_election($electionId);
    $totalVotes      = array_sum(array_map(static fn($c) => (int)$c['vote_count'], $candidates));
    $turnoutPercent  = $totalRegistered > 0 ? round($totalVotes / $totalRegistered * 100, 2) : 0.0;

    $candidateResults = [];
    foreach ($candidates as $c) {
        $pct = $totalVotes > 0 ? round((int)$c['vote_count'] / $totalVotes * 100, 2) : 0.0;
        $candidateResults[] = [
            'candidate'  => $c,
            'votes'      => (int)$c['vote_count'],
            'percentage' => $pct,
        ];
    }

    render_page('results/detail', [
        'election'          => $election,
        'total_registered'  => $totalRegistered,
        'total_votes'       => $totalVotes,
        'turnout_percent'   => $turnoutPercent,
        'candidate_results' => $candidateResults,
    ]);
}

function handle_historical_elections(): void {
    redirect_to('elections_list');
}

// =============================================================================
// 7. ADMIN CONTROL SYSTEM
// =============================================================================
function handle_admin_login(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        $admin = user_find_by_login($username);
        if ($admin && $admin['role'] === 'admin' && verify_user_password($password, $admin['password_hash'], $admin)) {
            user_login($admin);
            $_SESSION['role'] = 'admin';
            flash('Administrative authentication successful.', 'success');
            redirect_to('admin_dashboard');
        }

        flash('Invalid administrator credentials.', 'error');
        redirect_to('admin_login');
    }

    render_page('admin/login', [], 'base');
}

function handle_admin_dashboard(): void {
    admin_required();

    render_page('admin/dashboard', [
        'total_elections_count'  => election_total_count(),
        'total_candidates_count' => candidate_count(),
        'total_voters_count'     => user_count_voters(),
        'total_votes_count'      => candidate_total_votes(),
    ], 'admin');
}

function handle_admin_elections(): void {
    admin_required();
    render_page('admin/elections', ['elections' => election_all_any(true, 'DESC')], 'admin');
}

function handle_admin_create_election(): void {
    admin_required();

    $title = trim($_POST['title'] ?? '');
    election_create([
        'title'          => $title,
        'description'    => trim($_POST['description'] ?? ''),
        'election_type'  => trim($_POST['election_type'] ?? 'General Election'),
        'location'       => trim($_POST['location'] ?? ''),
        'start_date'     => trim($_POST['start_date'] ?? ''),
        'end_date'       => trim($_POST['end_date'] ?? ''),
        'status'         => trim($_POST['status'] ?? 'Upcoming'),
        'is_historical'  => 0,
    ]);

    flash('Election "' . $title . '" created successfully.', 'success');
    redirect_to('admin_elections');
}

function handle_admin_update_election_status(int $electionId): void {
    admin_required();

    $election = election_find_by_id($electionId);
    $status   = trim($_POST['status'] ?? '');
    if ($election && empty($election['is_historical']) && in_array($status, ['Active', 'Upcoming', 'Completed'], true)) {
        election_update_status($electionId, $status);
        flash('Election "' . $election['title'] . '" status changed to ' . $status . '.', 'success');
    }
    redirect_to('admin_elections');
}

function handle_admin_candidates(): void {
    admin_required();
    render_page('admin/candidates', [
        'candidates' => candidate_all(null, true),
        'elections'  => election_all(false),
    ], 'admin');
}

function handle_admin_create_candidate(): void {
    admin_required();

    $name       = trim($_POST['name'] ?? '');
    $symbolRaw  = $_POST['symbol'] ?? 'Tree|tree.svg';
    $symbolArgs = explode('|', $symbolRaw);
    $avatarOptions = ['candidate-1.svg', 'candidate-2.svg', 'candidate-3.svg', 'candidate-4.svg', 'candidate-5.svg'];

    candidate_create([
        'election_id'  => (int)($_POST['election_id'] ?? 0),
        'name'         => $name,
        'party'        => trim($_POST['party'] ?? ''),
        'position'     => trim($_POST['position'] ?? ''),
        'symbol_name'  => $symbolArgs[0] ?? 'Tree',
        'symbol_image' => $symbolArgs[1] ?? 'tree.svg',
        'photo_url'    => $avatarOptions[array_rand($avatarOptions)],
        'biography'    => trim($_POST['biography'] ?? ''),
        'vote_count'   => 0,
    ]);

    flash('Candidate "' . $name . '" added successfully.', 'success');
    redirect_to('admin_candidates');
}

function handle_admin_delete_candidate(int $candidateId): void {
    admin_required();

    $candidate = candidate_find_by_id($candidateId);
    if ($candidate) {
        candidate_delete($candidateId);
        flash('Candidate "' . $candidate['name'] . '" removed.', 'info');
    }
    redirect_to('admin_candidates');
}

function handle_admin_voters(): void {
    admin_required();

    $search = trim($_GET['q'] ?? '');
    render_page('admin/voters', [
        'voters'       => user_all_voters($search),
        'search_query' => $search,
    ], 'admin');
}

function handle_admin_toggle_voter(int $voterId): void {
    admin_required();

    $voter = user_find_by_id($voterId);
    if ($voter && $voter['role'] === 'voter') {
        user_toggle_active($voterId);
        $state = empty($voter['is_active']) ? 'activated' : 'deactivated';
        flash('Voter ' . $voter['full_name'] . ' has been ' . $state . '.', 'info');
    }
    redirect_to('admin_voters');
}

function handle_admin_verify_voter(int $voterId): void {
    admin_required();

    $voter = user_find_by_id($voterId);
    if ($voter && $voter['role'] === 'voter') {
        user_set_verification($voterId, true);
        flash('Voter "' . $voter['full_name'] . '" has been verified and may vote once per election.', 'success');
    }
    redirect_to('admin_voters');
}

function handle_admin_update_voter_details(int $voterId): void {
    admin_required();

    $voter = user_find_by_id($voterId);
    if ($voter && $voter['role'] === 'voter') {
        $fields = ['has_citizenship_verified' => 1];
        foreach (['full_name', 'citizenship_no', 'district', 'ward'] as $key) {
            $value = trim($_POST[$key] ?? '');
            if ($value !== '') {
                $fields[$key] = $value;
            }
        }
        user_update_fields($voterId, $fields);

        // Re-generate the matching certificate when using a generated SVG card
        $updated = user_find_by_id($voterId);
        if (empty($updated['citizenship_photo']) || str_contains((string)$updated['citizenship_photo'], 'citizenship_cards/')) {
            user_update_fields($voterId, ['citizenship_photo' => generate_voter_card_svg($updated)]);
            $updated = user_find_by_id($voterId);
        }

        flash('Voter details for "' . $updated['full_name'] . '" updated and citizenship identity re-verified successfully.', 'success');
    }
    redirect_to('admin_voters');
}

function handle_admin_reupload_voter_certificate(int $voterId): void {
    admin_required();

    $voter = user_find_by_id($voterId);
    if (!$voter || $voter['role'] !== 'voter') {
        redirect_to('admin_voters');
    }

    $uploaded = $_FILES['citizenship_photo'] ?? null;
    if ($uploaded && !empty($uploaded['name']) && ($uploaded['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $name = basename($uploaded['name']);
        $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'pdf', 'svg'], true)) {
            $uploadDir = __DIR__ . '/static/uploads/citizenship';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $safeName  = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $name);
            $unique    = "cit_{$voterId}_" . time() . "_{$safeName}";
            if (move_uploaded_file($uploaded['tmp_name'], $uploadDir . '/' . $unique)) {
                user_update_fields($voterId, [
                    'citizenship_photo'        => "uploads/citizenship/{$unique}",
                    'has_citizenship_verified' => 1,
                ]);
                flash('New citizenship certificate photo uploaded and matched for "' . $voter['full_name'] . '".', 'success');
            } else {
                flash('Failed to store the uploaded certificate. Please check write permissions.', 'error');
            }
        } else {
            flash('Unsupported file type. Please upload a PNG, JPG, WEBP, PDF, or SVG.', 'error');
        }
    } else {
        // Regenerate the official matching certificate
        user_update_fields($voterId, [
            'citizenship_photo'        => generate_voter_card_svg($voter),
            'has_citizenship_verified' => 1,
        ]);
        flash('Official matching citizenship certificate generated for "' . $voter['full_name'] . '".', 'success');
    }

    redirect_to('admin_voters');
}

function handle_admin_results(): void {
    admin_required();

    $summaries = [];
    foreach (election_all_any(true) as $el) {
        $candidates = candidate_by_election((int)$el['id']);
        $totalVotes = array_sum(array_map(static fn($c) => (int)$c['vote_count'], $candidates));
        $registered = (int)($el['registered_voters_count'] ?: 1200);
        $turnout    = $registered > 0 ? round($totalVotes / $registered * 100, 2) : 0.0;

        $candidateResults = [];
        foreach ($candidates as $c) {
            $pct = $totalVotes > 0 ? round((int)$c['vote_count'] / $totalVotes * 100, 2) : 0.0;
            $candidateResults[] = [
                'candidate'  => $c,
                'votes'      => (int)$c['vote_count'],
                'percentage' => $pct,
            ];
        }

        $summaries[] = [
            'election'          => $el,
            'total_registered'  => $registered,
            'total_votes'       => $totalVotes,
            'turnout_percent'   => $turnout,
            'candidate_results' => $candidateResults,
        ];
    }

    render_page('admin/results', ['election_summaries' => $summaries], 'admin');
}

function handle_admin_reports(): void {
    admin_required();
    render_page('admin/reports', ['participation_logs' => all_participation_logs()], 'admin');
}

// =============================================================================
// ROUTE TABLE
// =============================================================================
$routes = [
    ['GET',  '#^/$#',                                   'index',                    static fn() => redirect_to_supabase('public/index.html')],
    ['GET',  '#^/about$#',                              'about',                    static fn() => redirect_to_supabase('public/index.html')],
    ['GET',  '#^/login$#',                              'login',                    static fn() => redirect_to_supabase('auth/login.html')],
    ['GET',  '#^/register$#',                           'register',                 static fn() => redirect_to_supabase('auth/register.html')],
    ['GET',  '#^/logout$#',                             'logout',                   static fn() => redirect_to_supabase('auth/logout.html')],

    ['GET',  '#^/dashboard$#',                          'voter_dashboard',          static fn() => redirect_to_supabase('voter/dashboard.html')],
    ['GET',  '#^/my-elections$#',                       'my_elections',             static fn() => redirect_to_supabase('voter/elections.html')],
    ['GET',  '#^/elections$#',                          'elections_list',           static fn() => redirect_to_supabase('public/elections.html')],
    ['GET',  '#^/elections/(\d+)$#',                    'election_detail',          static fn() => redirect_to_supabase('public/elections.html')],
    ['GET',  '#^/elections/(\d+)/vote$#',               'election_vote',            static fn() => redirect_to_supabase('voter/elections.html')],
    ['GET',  '#^/elections/(\d+)/confirm$#',            'election_vote_confirm',    static fn() => redirect_to_supabase('voter/elections.html')],
    ['GET',  '#^/vote-success$#',                       'vote_success',             static fn() => redirect_to_supabase('voter/status.html')],
    ['GET',  '#^/voting-status$#',                      'voting_status',            static fn() => redirect_to_supabase('voter/status.html')],
    ['GET',  '#^/profile$#',                            'voter_profile',             static fn() => redirect_to_supabase('voter/dashboard.html')],

    ['GET',  '#^/candidates$#',                         'candidates_list',          static fn() => redirect_to_supabase('public/candidates.html')],
    ['GET',  '#^/candidates/(\d+)$#',                   'candidate_profile',        static fn() => redirect_to_supabase('public/candidates.html')],
    ['GET',  '#^/results$#',                            'results_list',             static fn() => redirect_to_supabase('public/results.html')],
    ['GET',  '#^/results/(\d+)$#',                      'results_detail',           static fn() => redirect_to_supabase('public/results.html')],
    ['GET',  '#^/historical-elections$#',               'historical_elections',     static fn() => redirect_to_supabase('public/elections.html')],

    ['GET',  '#^/admin/login$#',                        'admin_login',              static fn() => redirect_to_supabase('auth/login.html')],
    ['GET',  '#^/admin/dashboard$#',                    'admin_dashboard',          static fn() => redirect_to_supabase('admin/dashboard.html')],
    ['GET',  '#^/admin/elections$#',                    'admin_elections',           static fn() => redirect_to_supabase('admin/elections.html')],
    ['GET',  '#^/admin/candidates$#',                   'admin_candidates',         static fn() => redirect_to_supabase('admin/candidates.html')],
    ['GET',  '#^/admin/voters$#',                       'admin_voters',              static fn() => redirect_to_supabase('admin/dashboard.html#voter-applications')],
    ['GET',  '#^/admin/results$#',                      'admin_results',             static fn() => redirect_to_supabase('public/results.html')],
    ['GET',  '#^/admin/reports$#',                      'admin_reports',             static fn() => redirect_to_supabase('admin/audit.html')],
];

// -----------------------------------------------------------------------------
// DISPATCH
// -----------------------------------------------------------------------------
$matched = false;
ob_start();

try {
    foreach ($routes as [$allowedMethods, $pattern, $endpoint, $handler]) {
        if (!preg_match($pattern, $path, $matches)) {
            continue;
        }
        if (!in_array($method, explode('|', $allowedMethods), true)) {
            continue;
        }

        $GLOBALS['CURRENT_ROUTE'] = $endpoint;
        $matched = true;
        $handler($matches);
        break;
    }

    if (!$matched) {
        not_found();
    }
} catch (Throwable $e) {
    // Surface a readable error page during local development
    http_response_code(500);
    echo '<pre style="padding:2rem;font-family:monospace;color:#b91c1c;">'
        . htmlspecialchars(get_class($e) . ': ' . $e->getMessage())
        . "\n" . htmlspecialchars($e->getFile() . ':' . $e->getLine())
        . '</pre>';
}

$html = ob_get_clean();

// Rewrite root-relative asset URLs when the app is installed in a sub-folder
if (BASE_URL !== '') {
    $html = str_replace(
        ['"/static/', "'/static/"],
        ['"' . BASE_URL . '/static/', "'" . BASE_URL . '/static/'],
        $html
    );
}

echo $html;
