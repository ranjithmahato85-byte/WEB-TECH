<?php
/**
 * Nepal Online Voting System
 * Demo Data Seeder
 *
 * PHP port of the Flask application's init_db() seed routine. Only runs on a
 * completely empty database, so an existing/copied voting.db is never touched.
 */

require_once __DIR__ . '/models.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

function ensure_demo_data(): void {
    $db = get_db();

    $userCount     = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $electionCount = (int)$db->query("SELECT COUNT(*) FROM elections")->fetchColumn();

    if ($userCount > 0 || $electionCount > 0) {
        return; // Database already populated — preserve existing data
    }

    seed_users($db);
    seed_elections($db);
    seed_candidates($db);
    seed_certificates($db);
}

function seed_users(PDO $db): void {
    // 1. Election Commission administrator
    $adminId = user_create([
        'full_name'                => 'Election Commission Administrator',
        'username'                 => 'admin',
        'email'                    => 'admin@election.gov.np',
        'password_hash'            => hash_password('admin123'),
        'role'                     => 'admin',
        'voter_id'                 => 'ECN-ADMIN-01',
        'citizenship_no'           => '27-01-70-00001',
        'has_citizenship_verified' => 1,
        'province'                 => 'Bagmati',
        'district'                 => 'Kathmandu',
        'municipality'             => 'KMC',
        'ward'                     => '1',
        'is_verified'              => 1,
        'is_active'                => 1,
    ]);

    // 2. Primary demo voter
    $ranjeetId = user_create([
        'full_name'                => 'Ranjeet Kumar',
        'username'                 => 'ranjeet',
        'email'                    => 'ranjeet@example.com',
        'password_hash'            => hash_password('password123'),
        'role'                     => 'voter',
        'dob'                      => '1999-05-14',
        'mobile'                   => '9841234567',
        'province'                 => 'Bagmati',
        'district'                 => 'Kathmandu',
        'municipality'             => 'Kathmandu Metropolitan City',
        'ward'                     => '10',
        'voter_id'                 => 'NV-2026-8891',
        'citizenship_no'           => '27-01-76-08891',
        'has_citizenship_verified' => 1,
        'is_verified'              => 1,
        'is_active'                => 1,
    ]);

    // 3. Additional verified citizens for registry demonstration
    $sampleVoters = [
        ['Bikash Thapa', 'bikash', 'bikash@example.com', '9811223344', 'NV-2026-1011', '27-02-77-01011', 'Bagmati', 'Lalitpur', '12'],
        ['Pooja Gurung', 'pooja', 'pooja@example.com', '9822334455', 'NV-2026-1012', '44-01-78-01012', 'Gandaki', 'Kaski', '4'],
        ['Binod Shrestha', 'binod', 'binod@example.com', '9833445566', 'NV-2026-1013', '12-03-75-01013', 'Koshi', 'Morang', '7'],
        ['Sita Maya Tamang', 'sitamaya', 'sitamaya@example.com', '9844556677', 'NV-2026-1014', '28-01-76-01014', 'Bagmati', 'Bhaktapur', '3'],
    ];

    foreach ($sampleVoters as [$name, $username, $email, $mobile, $voterId, $citNo, $province, $district, $ward]) {
        user_create([
            'full_name'                => $name,
            'username'                 => $username,
            'email'                    => $email,
            'password_hash'            => hash_password('password123'),
            'role'                     => 'voter',
            'dob'                      => '1998-01-15',
            'mobile'                   => $mobile,
            'voter_id'                 => $voterId,
            'citizenship_no'           => $citNo,
            'has_citizenship_verified' => 1,
            'province'                 => $province,
            'district'                 => $district,
            'municipality'             => $district . ' Municipality',
            'ward'                     => $ward,
            'is_verified'              => 1,
            'is_active'                => 1,
        ]);
    }

    unset($adminId, $ranjeetId);
}

function seed_elections(PDO $db): void {
    $elections = [
        [
            'title' => 'Federal Parliamentary Election',
            'description' => 'General election for the House of Representatives of the Federal Parliament of Nepal.',
            'election_type' => 'General Election',
            'location' => 'Nepal',
            'start_date' => '2025-09-01',
            'end_date' => '2025-09-10',
            'status' => 'Active',
            'is_historical' => 0,
            'registered_voters_count' => 1200,
            'image_url' => 'nepal-hero.jpg',
        ],
        [
            'title' => 'Provincial Assembly Election',
            'description' => 'Provincial Assembly representative balloting across all 7 provinces.',
            'election_type' => 'Provincial Election',
            'location' => 'Bagmati',
            'start_date' => '2025-09-15',
            'end_date' => '2025-09-25',
            'status' => 'Upcoming',
            'is_historical' => 0,
            'registered_voters_count' => 850,
            'image_url' => 'nepal-patan.jpg',
        ],
        [
            'title' => 'Local Election',
            'description' => 'Municipal mayoral and ward chairperson democratic balloting.',
            'election_type' => 'Local Government',
            'location' => 'Lalitpur',
            'start_date' => '2025-10-01',
            'end_date' => '2025-10-10',
            'status' => 'Upcoming',
            'is_historical' => 0,
            'registered_voters_count' => 600,
            'image_url' => 'nepal-boudhanath.jpg',
        ],
        [
            'title' => 'Kathmandu Mayoral Election',
            'description' => 'Local civic election for Mayor and Deputy Mayor of Kathmandu Metropolitan City.',
            'election_type' => 'Local Government',
            'location' => 'Kathmandu',
            'start_date' => '2026-09-20',
            'end_date' => '2026-10-05',
            'status' => 'Active',
            'is_historical' => 0,
            'registered_voters_count' => 1500,
            'image_url' => 'nepal-patan.jpg',
        ],
        [
            'title' => 'House of Representatives Election 2079',
            'description' => 'Official historical results from the 2022 general election of Nepal.',
            'election_type' => 'General Election',
            'location' => 'Nepal',
            'start_date' => '2022-11-20',
            'end_date' => '2022-11-20',
            'status' => 'Completed',
            'is_historical' => 1,
            'registered_voters_count' => 17988570,
            'image_url' => 'nepal-boudhanath.jpg',
        ],
    ];

    foreach ($elections as $el) {
        election_create($el);
    }
}

function seed_candidates(PDO $db): void {
    $byTitle = [];
    foreach ($db->query("SELECT id, title FROM elections")->fetchAll() as $row) {
        $byTitle[$row['title']] = (int)$row['id'];
    }

    $federalId   = $byTitle['Federal Parliamentary Election'] ?? null;
    $mayoralId   = $byTitle['Kathmandu Mayoral Election'] ?? null;
    $historicalId = $byTitle['House of Representatives Election 2079'] ?? null;

    // Federal Parliamentary Election
    $federalCandidates = [
        ['KP Sharma Oli', 'CPN (UML)', 'Member of Parliament', 'Sun', 'sun.svg', 'kp_sharma_oli.jpg',
         'Senior political leader and Chairman of CPN (UML). Former Prime Minister campaigning on infrastructure expansion, national connectivity, and economic stability.', 380],
        ['Sher Bahadur Deuba', 'Nepali Congress', 'Member of Parliament', 'Tree', 'tree.svg', 'sher_bahadur_deuba.jpg',
         'President of Nepali Congress and multi-term Prime Minister. Platform prioritizing democratic constitutionalism, federal strengthening, and diplomatic leadership.', 340],
        ['Pushpa Kamal Dahal (Prachanda)', 'Maoist Center', 'Member of Parliament', 'Hammer & Sickle', 'hammer-sickle.svg', 'prachanda.jpg',
         'Chairman of CPN (Maoist Centre) and former Prime Minister. Championing inclusive governance, transitional justice, and social safety nets.', 210],
        ['Kulman Ghising', 'Independent', 'Member of Parliament', 'Torch', 'torch.svg', 'kulman_ghising.webp',
         'Distinguished electrical engineer and former NEA Director celebrated for eliminating nationwide load shedding. Focused on energy self-sufficiency, clean tech, and public accountability.', 150],
    ];
    foreach ($federalCandidates as [$name, $party, $position, $sym, $symImg, $photo, $bio, $votes]) {
        candidate_create([
            'election_id' => $federalId, 'name' => $name, 'party' => $party, 'position' => $position,
            'symbol_name' => $sym, 'symbol_image' => $symImg, 'photo_url' => $photo,
            'biography' => $bio, 'vote_count' => $votes, 'is_fictional_demo' => 0,
        ]);
    }

    // Kathmandu Mayoral Election
    $mayoralCandidates = [
        ['Balendra Shah', 'Independent', 'Metropolitan Mayor', 'Bell (घण्टी)', 'bell.svg', 'balen_shah.jpg',
         'Structural engineer, artist, and Mayor of Kathmandu Metropolitan City. Championing urban planning, municipal education reform, heritage restoration, and transparent civic governance.', 620],
        ['Sunita Dangol', 'CPN (UML)', 'Deputy Mayor', 'Sun', 'sun.svg', 'sunita_dangol.png',
         'Youth leader, cultural indigenous advocate, and Deputy Mayor of Kathmandu. Focus on community development, women entrepreneurship, and Newa heritage conservation.', 480],
        ['Harka Sampang', 'Independent', 'Sub-Metropolitan Mayor', 'Soil (माटो)', 'soil.svg', 'harka_sampang.png',
         'Grassroots leader and Mayor of Dharan. Renowned for the voluntary civic labor movement (Shram Sanskriti), reforestation, and independent citizen water supply initiatives.', 410],
    ];
    foreach ($mayoralCandidates as [$name, $party, $position, $sym, $symImg, $photo, $bio, $votes]) {
        candidate_create([
            'election_id' => $mayoralId, 'name' => $name, 'party' => $party, 'position' => $position,
            'symbol_name' => $sym, 'symbol_image' => $symImg, 'photo_url' => $photo,
            'biography' => $bio, 'vote_count' => $votes, 'is_fictional_demo' => 0,
        ]);
    }

    // Historical House of Representatives 2079 (official certified tally)
    $historicalCandidates = [
        ['KP Sharma Oli', 'CPN (UML)', 'Member of Parliament (Jhapa-5)', 'Sun (सूर्य)', 'sun.svg', 'kp_sharma_oli.jpg',
         'Senior leader and Chairman of CPN (UML). Secured largest party popular vote share nationwide with 78 total seats.', 2845641],
        ['Sher Bahadur Deuba', 'Nepali Congress', 'Member of Parliament (Dadeldhura-1)', 'Tree (रुख)', 'tree.svg', 'sher_bahadur_deuba.jpg',
         'President of Nepali Congress. Secured largest plurality of 89 parliamentary seats and 2,715,845 popular votes.', 2715845],
        ['Pushpa Kamal Dahal (Prachanda)', 'CPN (Maoist Centre)', 'Member of Parliament (Gorkha-2)', 'Hammer & Sickle', 'hammer-sickle.svg', 'prachanda.jpg',
         'Chairman of CPN (Maoist Centre). Secured 32 total parliamentary seats and pivotal coalition leadership.', 1175684],
        ['Rabi Lamichhane', 'Rastriya Swatantra Party', 'Member of Parliament (Chitwan-2)', 'Bell (घण्टी)', 'bell.svg', 'candidate-4.svg',
         'President of Rastriya Swatantra Party. Historic debut securing 20 seats and 1,130,344 nationwide popular votes.', 1130344],
        ['Rajendra Lingden', 'Rastriya Prajatantra Party', 'Member of Parliament (Jhapa-3)', 'Plough (हलो)', 'torch.svg', 'candidate-1.svg',
         'Chairman of Rastriya Prajatantra Party. Captured 14 seats and 588,849 popular votes across Nepal.', 588849],
        ['Upendra Yadav', 'Janata Samajbadi Party', 'Member of Parliament (Saptari-2)', 'Umbrella (छाता)', 'nepal-flag.svg', 'candidate-3.svg',
         'Chairman of Janata Samajbadi Party Nepal. Secured 12 seats and 421,314 popular votes in Madhesh Province.', 421314],
        ['Dr. CK Raut', 'Janamat Party', 'Member of Parliament (Saptari-2)', 'Horn (लाउडस्पिकर)', 'soil.svg', 'candidate-2.svg',
         'President of Janamat Party. Captured 6 seats and 394,655 popular votes, achieving national party status.', 394655],
        ['Madhav Kumar Nepal', 'CPN (Unified Socialist)', 'Member of Parliament (Rautahat-1)', 'Pen (कलम)', 'tree.svg', 'candidate-5.svg',
         'Chairman of CPN (Unified Socialist). Secured 10 parliamentary seats and 298,391 popular votes.', 298391],
        ['Other Parties & Independents', 'Independent Alliance', 'Members of Parliament (Various)', 'Torch (मसाल)', 'torch.svg', 'kulman_ghising.jpg',
         'Combined votes of LSP, Nagarik Unmukti, NWPP, and independent parliamentarians.', 1476617],
    ];
    foreach ($historicalCandidates as [$name, $party, $position, $sym, $symImg, $photo, $bio, $votes]) {
        candidate_create([
            'election_id' => $historicalId, 'name' => $name, 'party' => $party, 'position' => $position,
            'symbol_name' => $sym, 'symbol_image' => $symImg, 'photo_url' => $photo,
            'biography' => $bio, 'vote_count' => $votes, 'is_fictional_demo' => 0,
        ]);
    }
}

function seed_certificates(PDO $db): void {
    foreach ($db->query("SELECT * FROM users WHERE role = 'voter'")->fetchAll() as $voter) {
        if (empty($voter['citizenship_photo']) || $voter['citizenship_photo'] === 'sample-citizenship-card.svg') {
            $voter['citizenship_photo'] = generate_voter_card_svg($voter);
            $stmt = $db->prepare("UPDATE users SET citizenship_photo = :p WHERE id = :id");
            $stmt->execute([':p' => $voter['citizenship_photo'], ':id' => $voter['id']]);
        }
    }
}
