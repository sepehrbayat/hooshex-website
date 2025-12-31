-- SQL script to create the careers table manually
-- Run this if you cannot run migrations due to PHP version issues
-- Usage: sqlite3 database/database.sqlite < create-careers-table.sql

-- Create the careers table
CREATE TABLE IF NOT EXISTS "careers" (
    "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    "title" VARCHAR NOT NULL,
    "slug" VARCHAR NOT NULL UNIQUE,
    "location" VARCHAR,
    "type" VARCHAR(20) DEFAULT 'remote',
    "short_description" TEXT,
    "description" TEXT,
    "application_link" VARCHAR(500),
    "is_active" INTEGER DEFAULT 1,
    "published_at" TIMESTAMP,
    "expires_at" TIMESTAMP,
    "created_at" TIMESTAMP,
    "updated_at" TIMESTAMP,
    "department" VARCHAR,
    "work_type" VARCHAR(20),
    "contract_type" VARCHAR(20),
    "salary_range" VARCHAR,
    "experience_level" VARCHAR,
    "responsibilities" TEXT,  -- JSON stored as TEXT in SQLite
    "requirements" TEXT,      -- JSON stored as TEXT in SQLite
    "benefits" TEXT           -- JSON stored as TEXT in SQLite
);

-- Create indexes
CREATE INDEX IF NOT EXISTS "careers_published_at_index" ON "careers" ("published_at");
CREATE INDEX IF NOT EXISTS "careers_is_active_index" ON "careers" ("is_active");
CREATE INDEX IF NOT EXISTS "careers_type_index" ON "careers" ("type");
CREATE INDEX IF NOT EXISTS "careers_work_type_index" ON "careers" ("work_type");
CREATE INDEX IF NOT EXISTS "careers_contract_type_index" ON "careers" ("contract_type");
CREATE INDEX IF NOT EXISTS "careers_department_index" ON "careers" ("department");

-- Insert migration records (so Laravel knows these migrations have been run)
INSERT OR IGNORE INTO "migrations" ("migration", "batch") VALUES 
    ('2025_12_21_123641_create_careers_table', 1),
    ('2025_12_21_165608_refactor_careers_table_for_job_postings', 2);

