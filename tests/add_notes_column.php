<?php
require __DIR__ . "/../includes/db.php";

echo "Adding notes column...\n";

try {
    $pdo->exec("ALTER TABLE transactions ADD COLUMN notes TEXT NULL AFTER date");
    echo "Notes column added successfully!\n";
} catch (PDOException $e) {
    echo "Error adding notes column: " . $e->getMessage() . "\n";
}

echo "Notes column setup complete.\n";
?>
