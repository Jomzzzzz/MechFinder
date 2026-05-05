<?php

require 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$table = 'dispatch_requests';
$columns = [
    'motor_name' => 'VARCHAR(255) NULL',
    'motor_brand' => 'VARCHAR(255) NULL',
    'motor_color' => 'VARCHAR(100) NULL'
];

echo "Checking dispatch_requests table...\n";

foreach ($columns as $col => $type) {
    if (!Schema::hasColumn($table, $col)) {
        DB::statement("ALTER TABLE `{$table}` ADD COLUMN `{$col}` {$type}");
        echo "✓ Added column: {$col}\n";
    } else {
        echo "✓ Column already exists: {$col}\n";
    }
}

echo "\nDone! Motor info columns are ready.\n";
