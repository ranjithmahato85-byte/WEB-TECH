<?php
/**
 * Nepal Online Voting System
 * Database Query Models & Data Access Layer
 */

require_once __DIR__ . '/db.php';

// -----------------------------------------------------------------------------
// USER MODEL
// -----------------------------------------------------------------------------

function user_find_by_id($id) {
    if (!$id) return null;
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch();
    return $user ? enrich_user($user) : null;
}

function user_find_by_login($login) {
    $login = trim($login);
    if (!$login) return null;
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM users WHERE (username = :login OR email = :login) LIMIT 1");
    $stmt->execute([':login' => $login]);
    $user = $stmt->fetch();
    return $user ? enrich_user($user) : null;
}

function user_find_by_citizenship($citNo) {
    $citNo = trim($citNo);
    if (!$citNo) return null;
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM users WHERE citizenship_no = :cit LIMIT 1");
    $stmt->execute([':cit' => $citNo]);
    $user = $stmt->fetch();
    return $user ? enrich_user($user) : null;
}

function user_find_by_voter_id($voterId) {
    $voterId = trim($voterId);
    if (!$voterId) return null;
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM users WHERE voter_id = :vid LIMIT 1");
    $stmt->execute([':vid' => $voterId]);
    $user = $stmt->fetch();
    return $user ? enrich_user($user) : null;
}

function user_all_voters($search = '') {
    $db = get_db();
    if (!empty($search)) {
        $term = '%' . trim($search) . '%';
        $stmt = $db->prepare("SELECT * FROM users WHERE role = 'voter' AND (
            full_name LIKE :t1 OR voter_id LIKE :t2 OR citizenship_no LIKE :t3 OR district LIKE :t4 OR username LIKE :t5
        ) ORDER BY id DESC");
        $stmt->execute([':t1' => $term, ':t2' => $term, ':t3' => $term, ':t4' => $term, ':t5' => $term]);
    } else {
        $stmt = $db->query("SELECT * FROM users WHERE role = 'voter' ORDER BY id DESC");
    }
    $users = $stmt->fetchAll();
    return array_map('enrich_user', $users);
}

function user_count_voters() {
    $db = get_db();
    return (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'voter'")->fetchColumn();
}

function enrich_user(array $user): array {
    // Add computed age and is_eligible_age
    $age = 0;
    if (!empty($user['dob'])) {
        try {
            $dob = new DateTime(trim($user['dob']));
            $now = new DateTime();
            $age = $now->diff($dob)->y;
        } catch (Exception $e) {
            $age = 0;
        }
    }
    $user['age'] = $age;
    $user['is_eligible_age'] = ($age >= 18);
    return $user;
}

// -----------------------------------------------------------------------------
// ELECTION MODEL
// -----------------------------------------------------------------------------

function election_find_by_id($id) {
    if (!$id) return null;
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM elections WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $election = $stmt->fetch();
    if ($election) {
        $election['candidates'] = candidate_by_election($election['id']);
    }
    return $election ?: null;
}

function election_all($includeHistorical = false) {
    return election_all_any((bool)$includeHistorical, 'ASC');
}

/**
 * Every election (optionally including historical archives) with candidates
 * attached, ordered by id.
 */
function election_all_any(bool $includeHistorical = true, string $order = 'ASC') {
    $db  = get_db();
    $dir = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
    $sql = $includeHistorical
        ? "SELECT * FROM elections ORDER BY id $dir"
        : "SELECT * FROM elections WHERE is_historical = 0 ORDER BY id $dir";

    $elections = $db->query($sql)->fetchAll();
    foreach ($elections as &$el) {
        $el['candidates'] = candidate_by_election($el['id']);
    }
    unset($el);
    return $elections;
}

/**
 * Elections filtered by status, with candidates attached.
 */
function election_all_by_status(string $status, bool $includeHistorical = false) {
    $db  = get_db();
    $sql = $includeHistorical
        ? "SELECT * FROM elections WHERE status = :st ORDER BY id ASC"
        : "SELECT * FROM elections WHERE status = :st AND is_historical = 0 ORDER BY id ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute([':st' => $status]);

    $elections = $stmt->fetchAll();
    foreach ($elections as &$el) {
        $el['candidates'] = candidate_by_election($el['id']);
    }
    unset($el);
    return $elections;
}

function election_count_by_status($status, $includeHistorical = false) {
    $db = get_db();
    if ($includeHistorical) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM elections WHERE status = :st");
        $stmt->execute([':st' => $status]);
    } else {
        $stmt = $db->prepare("SELECT COUNT(*) FROM elections WHERE status = :st AND is_historical = 0");
        $stmt->execute([':st' => $status]);
    }
    return (int)$stmt->fetchColumn();
}

function election_total_count() {
    $db = get_db();
    return (int)$db->query("SELECT COUNT(*) FROM elections")->fetchColumn();
}

// -----------------------------------------------------------------------------
// CANDIDATE MODEL
// -----------------------------------------------------------------------------

function candidate_find_by_id($id) {
    if (!$id) return null;
    $db = get_db();
    $stmt = $db->prepare("SELECT c.*, e.title as election_title FROM candidates c JOIN elections e ON c.election_id = e.id WHERE c.id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $candidate = $stmt->fetch();
    return $candidate ?: null;
}

function candidate_by_election($electionId) {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM candidates WHERE election_id = :eid ORDER BY vote_count DESC, id ASC");
    $stmt->execute([':eid' => $electionId]);
    return $stmt->fetchAll();
}

function candidate_all($limit = null, bool $includeHistorical = false) {
    $db  = get_db();
    $sql = "SELECT c.*, e.title as election_title FROM candidates c JOIN elections e ON c.election_id = e.id";
    if (!$includeHistorical) {
        $sql .= " WHERE e.is_historical = 0";
    }
    $sql .= " ORDER BY c.id ASC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    return $db->query($sql)->fetchAll();
}

function candidate_count() {
    $db = get_db();
    return (int)$db->query("SELECT COUNT(*) FROM candidates")->fetchColumn();
}

function candidate_total_votes() {
    $db = get_db();
    return (int)$db->query("SELECT COALESCE(SUM(vote_count), 0) FROM candidates")->fetchColumn();
}

// -----------------------------------------------------------------------------
// VOTER PARTICIPATION & CONSTITUTIONAL BALLOT SECRECY
// -----------------------------------------------------------------------------

function get_prior_vote_for_user($user, ?int $electionId = null) {
    if (!$user) return null;
    $db = get_db();

    // 1. Direct user_id check
    $userId = is_array($user) ? $user['id'] : $user->id;
    $sql = "SELECT * FROM voter_participation WHERE user_id = :uid";
    $params = [':uid' => $userId];
    if ($electionId !== null) {
        $sql .= " AND election_id = :eid";
        $params[':eid'] = $electionId;
    }
    $sql .= " ORDER BY id DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $p = $stmt->fetch();
    if ($p) return $p;

    // 2. Match the normalized citizenship identity only within this election.
    $citNo = is_array($user) ? ($user['citizenship_no'] ?? '') : ($user->citizenship_no ?? '');
    if (!empty($citNo)) {
        $cleanCit = strtolower(str_replace(['-', ' '], '', $citNo));
        if (!empty($cleanCit)) {
            $sql = "SELECT * FROM voter_participation WHERE REPLACE(REPLACE(LOWER(citizenship_no), '-', ''), ' ', '') = :cit";
            $params = [':cit' => $cleanCit];
            if ($electionId !== null) {
                $sql .= " AND election_id = :eid";
                $params[':eid'] = $electionId;
            }
            $sql .= " ORDER BY id DESC LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch() ?: null;
        }
    }

    return null;
}

function user_has_voted_in_election($userId, $electionId) {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM voter_participation WHERE user_id = :uid AND election_id = :eid LIMIT 1");
    $stmt->execute([':uid' => $userId, ':eid' => $electionId]);
    return $stmt->fetch() ?: null;
}

function user_participations($userId) {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM voter_participation WHERE user_id = :uid ORDER BY id DESC");
    $stmt->execute([':uid' => $userId]);
    return $stmt->fetchAll();
}

function all_participation_logs() {
    $db = get_db();
    return $db->query("SELECT p.*, u.full_name as voter_name, u.voter_id, e.title as election_title FROM voter_participation p JOIN users u ON p.user_id = u.id JOIN elections e ON p.election_id = e.id ORDER BY p.id DESC")->fetchAll();
}

// -----------------------------------------------------------------------------
// WRITE / MUTATION OPERATIONS
// -----------------------------------------------------------------------------

function user_create(array $data): int {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO users
        (full_name, username, email, password_hash, role, dob, mobile, province, district, municipality, ward,
         voter_id, citizenship_no, citizenship_photo, has_citizenship_verified, is_verified, is_active)
        VALUES
        (:full_name, :username, :email, :password_hash, :role, :dob, :mobile, :province, :district, :municipality, :ward,
         :voter_id, :citizenship_no, :citizenship_photo, :has_citizenship_verified, :is_verified, :is_active)");
    $stmt->execute([
        ':full_name' => $data['full_name'],
        ':username' => $data['username'],
        ':email' => $data['email'],
        ':password_hash' => $data['password_hash'],
        ':role' => $data['role'] ?? 'voter',
        ':dob' => $data['dob'] ?? null,
        ':mobile' => $data['mobile'] ?? null,
        ':province' => $data['province'] ?? null,
        ':district' => $data['district'] ?? null,
        ':municipality' => $data['municipality'] ?? null,
        ':ward' => $data['ward'] ?? null,
        ':voter_id' => $data['voter_id'],
        ':citizenship_no' => $data['citizenship_no'] ?? null,
        ':citizenship_photo' => $data['citizenship_photo'] ?? 'sample-citizenship-card.svg',
        ':has_citizenship_verified' => isset($data['has_citizenship_verified']) ? (int)$data['has_citizenship_verified'] : 0,
        ':is_verified' => isset($data['is_verified']) ? (int)$data['is_verified'] : 0,
        ':is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
    ]);
    return (int)$db->lastInsertId();
}

function user_update_fields(int $id, array $fields): void {
    if (empty($fields)) return;
    $db = get_db();
    $sets = [];
    $params = [':id' => $id];
    foreach ($fields as $col => $val) {
        $sets[] = "$col = :$col";
        $params[':' . $col] = $val;
    }
    $stmt = $db->prepare("UPDATE users SET " . implode(', ', $sets) . " WHERE id = :id");
    $stmt->execute($params);
}

function user_toggle_active(int $id): void {
    $db = get_db();
    $stmt = $db->prepare("UPDATE users SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END WHERE id = :id AND role = 'voter'");
    $stmt->execute([':id' => $id]);
}

function user_set_verification(int $id, bool $verified): void {
    $db = get_db();
    $stmt = $db->prepare("
        UPDATE users
        SET is_verified = :verified,
            has_citizenship_verified = CASE WHEN :citizenship_verified = 1 THEN 1 ELSE has_citizenship_verified END
        WHERE id = :id AND role = 'voter'
    ");
    $stmt->execute([
        ':verified' => $verified ? 1 : 0,
        ':citizenship_verified' => $verified ? 1 : 0,
        ':id' => $id,
    ]);
}

function election_create(array $data): int {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO elections
        (title, description, election_type, location, start_date, end_date, status, is_historical, registered_voters_count, image_url)
        VALUES
        (:title, :description, :election_type, :location, :start_date, :end_date, :status, :is_historical, :registered_voters_count, :image_url)");
    $stmt->execute([
        ':title' => $data['title'],
        ':description' => $data['description'],
        ':election_type' => $data['election_type'],
        ':location' => $data['location'],
        ':start_date' => $data['start_date'],
        ':end_date' => $data['end_date'],
        ':status' => $data['status'] ?? 'Upcoming',
        ':is_historical' => isset($data['is_historical']) ? (int)$data['is_historical'] : 0,
        ':registered_voters_count' => $data['registered_voters_count'] ?? 1200,
        ':image_url' => $data['image_url'] ?? 'nepal-hero.jpg',
    ]);
    return (int)$db->lastInsertId();
}

function election_update_status(int $id, string $status): void {
    $db = get_db();
    $stmt = $db->prepare("UPDATE elections SET status = :status WHERE id = :id AND is_historical = 0");
    $stmt->execute([':status' => $status, ':id' => $id]);
}

function election_count_all(): int {
    $db = get_db();
    return (int)$db->query("SELECT COUNT(*) FROM elections")->fetchColumn();
}

function candidate_create(array $data): int {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO candidates
        (election_id, name, party, position, symbol_name, symbol_image, photo_url, biography, vote_count, is_fictional_demo)
        VALUES
        (:election_id, :name, :party, :position, :symbol_name, :symbol_image, :photo_url, :biography, :vote_count, :is_fictional_demo)");
    $stmt->execute([
        ':election_id' => $data['election_id'],
        ':name' => $data['name'],
        ':party' => $data['party'],
        ':position' => $data['position'],
        ':symbol_name' => $data['symbol_name'],
        ':symbol_image' => $data['symbol_image'],
        ':photo_url' => $data['photo_url'],
        ':biography' => $data['biography'],
        ':vote_count' => $data['vote_count'] ?? 0,
        ':is_fictional_demo' => isset($data['is_fictional_demo']) ? (int)$data['is_fictional_demo'] : 1,
    ]);
    return (int)$db->lastInsertId();
}

function candidate_delete(int $id): void {
    $db = get_db();
    $stmt = $db->prepare("DELETE FROM candidates WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

function candidate_increment_votes(int $id): void {
    $db = get_db();
    $stmt = $db->prepare("UPDATE candidates SET vote_count = COALESCE(vote_count, 0) + 1 WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

function participation_create(array $data): int {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO voter_participation
        (user_id, election_id, citizenship_no, voted_at, confirmation_code, citizenship_photo)
        VALUES (:user_id, :election_id, :citizenship_no, :voted_at, :confirmation_code, :citizenship_photo)");
    $stmt->execute([
        ':user_id' => $data['user_id'],
        ':election_id' => $data['election_id'],
        ':citizenship_no' => $data['citizenship_no'] ?? null,
        ':voted_at' => $data['voted_at'] ?? date('Y-m-d H:i:s'),
        ':confirmation_code' => $data['confirmation_code'],
        ':citizenship_photo' => $data['citizenship_photo'] ?? null,
    ]);
    return (int)$db->lastInsertId();
}

function ballot_create(int $electionId, int $candidateId): int {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO anonymous_ballots (election_id, candidate_id, cast_at) VALUES (:eid, :cid, :cast_at)");
    $stmt->execute([':eid' => $electionId, ':cid' => $candidateId, ':cast_at' => date('Y-m-d H:i:s')]);
    return (int)$db->lastInsertId();
}
