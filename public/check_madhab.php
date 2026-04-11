<?php
$host = '162.214.80.164';
$db   = 'ensplpmy_darulifta-lar-db';
$user = 'ensplpmy_darulifta-lar-use';
$pass = 'DaurIfta123@2026';
$pdo  = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

echo "<h2>Question madhab_id values</h2><pre>";
$rows = $pdo->query("SELECT id, question_id, madhab_id FROM questions LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);

echo "\n\nMadhab table:\n";
$madhabs = $pdo->query("SELECT * FROM madhabs")->fetchAll(PDO::FETCH_ASSOC);
print_r($madhabs);
echo "</pre>";
