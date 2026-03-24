<?php
require __DIR__ . "/../backend/includes/db.php";

echo "Current transactions table structure:\n";

$stmt = $pdo->prepare("DESCRIBE transactions");
$stmt->execute();
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($columns as $column) {
    echo "- {$column['Field']} ({$column['Type']})\n";
}
?>
