<?php

// Quick script to add guest_token column to shop_messages

require 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    $table = 'shop_messages';
    
    echo "Checking shop_messages table...\n";
    
    // Add guest_token column if it doesn't exist
    if (!Schema::hasColumn($table, 'guest_token')) {
        DB::statement("ALTER TABLE `{$table}` ADD COLUMN `guest_token` VARCHAR(100) NULL AFTER `motorist_id`");
        echo "✓ Added guest_token column\n";
    } else {
        echo "✓ guest_token column already exists\n";
    }
    
    // Make motorist_id nullable if not already
    $columns = DB::select("SHOW COLUMNS FROM `{$table}` WHERE Field = 'motorist_id'");
    if ($columns && $columns[0]->Null === 'NO') {
        DB::statement("ALTER TABLE `{$table}` MODIFY `motorist_id` BIGINT UNSIGNED NULL");
        echo "✓ Made motorist_id nullable\n";
    } else {
        echo "✓ motorist_id is already nullable\n";
    }
    
    echo "\n✓ Shop messages table is ready for guest motorists!\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
