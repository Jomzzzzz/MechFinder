<?php

require 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Check if shops table exists
if (Schema::hasTable('shops')) {
    echo "✓ Shops table exists\n\n";
    
    // Get the columns
    $columns = Schema::getColumns('shops');
    echo "Columns in shops table:\n";
    foreach ($columns as $column) {
        $type = $column['type'];
        $nullable = $column['nullable'] ? 'nullable' : 'not null';
        echo "  - {$column['name']}: {$type} ({$nullable})\n";
    }
} else {
    echo "✗ Shops table does not exist\n";
}
