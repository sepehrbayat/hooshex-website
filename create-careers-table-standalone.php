<?php

/**
 * Standalone Script to Create Careers Table
 * 
 * This script uses PDO directly (no Composer required) to create the careers table.
 * Run with any PHP version that has PDO SQLite support.
 * 
 * Usage: php create-careers-table-standalone.php
 */

$dbPath = __DIR__ . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    echo "❌ Error: Database file not found at: $dbPath\n";
    echo "Please ensure the database file exists.\n";
    exit(1);
}

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Creating careers table...\n";
    
    // Check if table exists
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='careers'");
    if ($stmt->fetch()) {
        echo "✓ Table 'careers' already exists.\n";
        exit(0);
    }
    
    // Create the careers table with all columns
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS \"careers\" (
            \"id\" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            \"title\" VARCHAR NOT NULL,
            \"slug\" VARCHAR NOT NULL UNIQUE,
            \"location\" VARCHAR,
            \"type\" VARCHAR(20) DEFAULT 'remote',
            \"short_description\" TEXT,
            \"description\" TEXT,
            \"application_link\" VARCHAR(500),
            \"is_active\" INTEGER DEFAULT 1,
            \"published_at\" TIMESTAMP,
            \"expires_at\" TIMESTAMP,
            \"created_at\" TIMESTAMP,
            \"updated_at\" TIMESTAMP,
            \"department\" VARCHAR,
            \"work_type\" VARCHAR(20),
            \"contract_type\" VARCHAR(20),
            \"salary_range\" VARCHAR,
            \"experience_level\" VARCHAR,
            \"responsibilities\" TEXT,
            \"requirements\" TEXT,
            \"benefits\" TEXT
        )
    ");
    
    echo "✓ Created careers table.\n";
    
    // Create indexes
    $indexes = [
        'CREATE INDEX IF NOT EXISTS "careers_published_at_index" ON "careers" ("published_at")',
        'CREATE INDEX IF NOT EXISTS "careers_is_active_index" ON "careers" ("is_active")',
        'CREATE INDEX IF NOT EXISTS "careers_type_index" ON "careers" ("type")',
        'CREATE INDEX IF NOT EXISTS "careers_work_type_index" ON "careers" ("work_type")',
        'CREATE INDEX IF NOT EXISTS "careers_contract_type_index" ON "careers" ("contract_type")',
        'CREATE INDEX IF NOT EXISTS "careers_department_index" ON "careers" ("department")',
    ];
    
    foreach ($indexes as $index) {
        try {
            $pdo->exec($index);
        } catch (PDOException $e) {
            // Index might already exist, continue
        }
    }
    
    echo "✓ Created indexes.\n";
    
    // Mark migrations as run (if migrations table exists)
    try {
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='migrations'");
        if ($stmt->fetch()) {
            // Get max batch
            $stmt = $pdo->query("SELECT MAX(batch) as max_batch FROM migrations");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $batch = ($result && $result['max_batch']) ? (int)$result['max_batch'] + 1 : 1;
            
            $migrations = [
                '2025_12_21_123641_create_careers_table',
                '2025_12_21_165608_refactor_careers_table_for_job_postings',
            ];
            
            foreach ($migrations as $migration) {
                // Check if already exists
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM migrations WHERE migration = ?");
                $stmt->execute([$migration]);
                if ($stmt->fetchColumn() == 0) {
                    $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                    $stmt->execute([$migration, $batch++]);
                }
            }
            
            echo "✓ Marked migrations as run.\n";
        }
    } catch (PDOException $e) {
        echo "Note: Could not mark migrations (migrations table may not exist yet).\n";
    }
    
    echo "\n✅ Success! The careers table has been created.\n";
    echo "You can now refresh http://localhost:6012/admin/careers\n";
    
} catch (PDOException $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

