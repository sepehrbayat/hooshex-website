<?php

/**
 * Migration Runner Script
 * 
 * This script helps run migrations when CLI PHP version doesn't match requirements.
 * Usage: php run-migrations.php
 * 
 * Make sure you're using PHP 8.4+ to run this script.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->call('migrate', [
    '--force' => true,
]);

exit($status);

