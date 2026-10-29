<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QuestionAndAnswer;
use Illuminate\Support\Facades\DB;

$isMysql = DB::getDriverName() === 'mysql';
$yearExpr = $isMysql ? "DATE_FORMAT(created_at, '%Y')" : "strftime('%Y', created_at)";
$monthExpr = $isMysql ? "DATE_FORMAT(created_at, '%Y-%m')" : "strftime('%Y-%m', created_at)";
$monthLabelExpr = $isMysql ? "DATE_FORMAT(created_at, '%b %Y')" : "strftime('%m-%Y', created_at)";

$yearlyData = QuestionAndAnswer::selectRaw("
        {$yearExpr} as yr,
        COUNT(*) as total_count,
        SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published_count
    ")
    ->whereNotNull('created_at')
    ->groupBy('yr')
    ->orderBy('yr')
    ->get();

echo "DB Driver: " . DB::getDriverName() . "\n";
echo "Yearly data:\n";
print_r($yearlyData->toArray());

$monthlyLifelongData = QuestionAndAnswer::selectRaw("
        {$monthExpr} as ym,
        {$monthLabelExpr} as label,
        COUNT(*) as total_count
    ")
    ->whereNotNull('created_at')
    ->groupBy('ym', 'label')
    ->orderBy('ym')
    ->get();

echo "Monthly count: " . $monthlyLifelongData->count() . "\n";
if ($monthlyLifelongData->isNotEmpty()) {
    print_r($monthlyLifelongData->take(3)->toArray());
}

