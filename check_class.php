<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    $exists = class_exists(\App\Http\Controllers\Admin\MailManagementController::class);
    echo $exists ? "EXISTS" : "DOES NOT EXIST";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
