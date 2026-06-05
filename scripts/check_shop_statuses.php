<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$db = $app->make('db');
$rows = $db->table('shop_statuses')->get();
foreach ($rows as $r) {
    echo sprintf("ID: %s, slug: %s, toggles_to_id: %s\n", $r->id, $r->slug, $r->toggles_to_id ?? 'NULL');
}
$shop = $db->table('shops')->first();
if ($shop) {
    echo sprintf("\nSample shop: id=%s, shop_name=%s, status_id=%s\n", $shop->id, $shop->shop_name ?? 'N/A', $shop->status_id ?? 'NULL');
}
