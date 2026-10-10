<?php
/**
 * Nepal Online Voting System
 * Database Connection & Initialization Layer
 */

function get_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        // Default to the shared SQLite database; VOTING_DB_PATH can point the
        // app at an alternative database (useful for tests / staging).
        $dbPath = getenv('VOTING_DB_PATH') ?: (__DIR__ . '/../instance/voting.db');
        $instanceDir = dirname($dbPath);
        if (!is_dir($instanceDir)) {
            mkdir($instanceDir, 0777, true);
        }

        $dsn = 'sqlite:' . $dbPath;
        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        // Enable SQLite foreign key constraints and WAL mode for reliability
        $pdo->exec('PRAGMA foreign_keys = ON;');
        $pdo->exec('PRAGMA journal_mode = WAL;');

        init_db_schema($pdo);
    }
    return $pdo;
}

function init_db_schema(PDO $pdo): void {
    // 1. Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        full_name VARCHAR(120) NOT NULL,
        username VARCHAR(80) UNIQUE NOT NULL,
        email VARCHAR(120) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role VARCHAR(20) DEFAULT 'voter',
        dob VARCHAR(30),
        mobile VARCHAR(30),
        province VARCHAR(50),
        district VARCHAR(50),
        municipality VARCHAR(100),
        ward VARCHAR(20),
        voter_id VARCHAR(50) UNIQUE NOT NULL,
        citizenship_no VARCHAR(50),
        citizenship_photo VARCHAR(255) DEFAULT 'sample-citizenship-card.svg',
        has_citizenship_verified BOOLEAN DEFAULT 0,
        is_verified BOOLEAN DEFAULT 0,
        is_active BOOLEAN DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    // 2. Elections table
    $pdo->exec("CREATE TABLE IF NOT EXISTS elections (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title VARCHAR(200) NOT NULL,
        description TEXT NOT NULL,
        election_type VARCHAR(50) NOT NULL,
        location VARCHAR(100) NOT NULL,
        start_date VARCHAR(30) NOT NULL,
        end_date VARCHAR(30) NOT NULL,
        status VARCHAR(20) DEFAULT 'Active',
        is_historical BOOLEAN DEFAULT 0,
        registered_voters_count INTEGER DEFAULT 1200,
        image_url VARCHAR(255) DEFAULT 'nepal-hero.jpg'
    );");

    // 3. Candidates table
    $pdo->exec("CREATE TABLE IF NOT EXISTS candidates (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        election_id INTEGER NOT NULL REFERENCES elections(id) ON DELETE CASCADE,
        name VARCHAR(120) NOT NULL,
        party VARCHAR(120) NOT NULL,
        position VARCHAR(100) NOT NULL,
        symbol_name VARCHAR(50) NOT NULL,
        symbol_image VARCHAR(100) NOT NULL,
        photo_url VARCHAR(100) NOT NULL,
        biography TEXT NOT NULL,
        vote_count INTEGER DEFAULT 0,
        is_fictional_demo BOOLEAN DEFAULT 1
    );");

    // 4. Voter Participation table (Ballot Secrecy: NO candidate choice stored here!)
    $pdo->exec("CREATE TABLE IF NOT EXISTS voter_participation (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL REFERENCES users(id),
        election_id INTEGER NOT NULL REFERENCES elections(id),
        citizenship_no VARCHAR(100),
        voted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        confirmation_code VARCHAR(50) UNIQUE NOT NULL,
        citizenship_photo VARCHAR(255),
        CONSTRAINT uq_user_election_vote UNIQUE (user_id, election_id)
    );");

    // 5. Anonymous Ballot table (Ballot Secrecy: NO user_id stored here!)
    $pdo->exec("CREATE TABLE IF NOT EXISTS anonymous_ballots (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        election_id INTEGER NOT NULL REFERENCES elections(id),
        candidate_id INTEGER NOT NULL REFERENCES candidates(id),
        cast_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );");
}
