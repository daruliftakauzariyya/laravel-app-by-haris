<?php
/**
 * DB Query Runner
 * WARNING: Delete this file after use! Allowing public access to DB queries is a huge security risk.
 */

// Load DB credentials from Laravel's .env if possible, or define them manually here
// Using manual definitions based on your current setup for reliability
$host = '162.214.80.164';
$db   = 'ensplpmy_darulifta-lar-db';
$user = 'ensplpmy_darulifta-lar-use';
$pass = 'DaurIfta123@2026';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     die("Connection failed: " . $e->getMessage());
}

$query = $_POST['query'] ?? '';
$results = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($query)) {
    try {
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB Query Runner</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f7f6; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        textarea { width: 100%; height: 150px; padding: 10px; border-radius: 4px; border: 1px solid #ccc; font-family: monospace; }
        button { background: #1a3a5f; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        .error { color: red; background: #fee; padding: 10px; border-radius: 4px; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        tr:nth-child(even) { background: #fafafa; }
        .warning { background: #fff3cd; color: #856404; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ffeeba; }
    </style>
</head>
<body>
    <div class="container">
        <div class="warning">
            <strong>SECURITY WARNING:</strong> This file allows direct database access. 
            <strong>DELETE THIS FILE</strong> from the server immediately after you finish your task.
        </div>
        
        <h2>Run SQL Query</h2>
        <form method="POST">
            <textarea name="query" placeholder="SELECT * FROM users LIMIT 10;"><?php echo htmlspecialchars($query); ?></textarea>
            <button type="submit">Execute Query</button>
        </form>

        <?php if ($error): ?>
            <div class="error"><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($results !== null): ?>
            <h3>Results (<?php echo count($results); ?> rows)</h3>
            <?php if (count($results) > 0): ?>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <?php foreach (array_keys($results[0]) as $column): ?>
                                    <th><?php echo htmlspecialchars($column); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $row): ?>
                                <tr>
                                    <?php foreach ($row as $value): ?>
                                        <td><?php echo htmlspecialchars($value ?? 'NULL'); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Query executed successfully, but returned no rows.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
