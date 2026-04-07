<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;

echo "Main Categories: " . Category::whereNull('parent_id')->count() . "\n";
print_r(Category::whereNull('parent_id')->pluck('name')->toArray());
echo "\nSubcategories: " . Category::whereNotNull('parent_id')->count() . "\n";
print_r(Category::whereNotNull('parent_id')->take(5)->pluck('name')->toArray());
