<?php
require __DIR__ . "/../backend/includes/db.php";

echo "Current categories table structure:\n";

$stmt = $pdo->prepare("DESCRIBE categories");
$stmt->execute();
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($columns as $column) {
    echo "- {$column['Field']} ({$column['Type']})\n";
}

echo "\nCurrent categories:\n";
$stmt = $pdo->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($categories as $category) {
    echo "- ID: {$category['id']}, Name: {$category['name']}, Type: " . ($category['type'] ?? 'NULL') . "\n";
}
?>
