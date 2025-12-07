<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;

$categories = Category::all();
if ($categories->isEmpty()) {
    echo "No categories found.\n";
    exit(0);
}

echo "Categories:\n";
foreach ($categories as $c) {
    printf("- id=%d name=%s\n", $c->id, $c->name);
}
