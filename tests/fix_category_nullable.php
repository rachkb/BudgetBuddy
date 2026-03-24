<?php
require __DIR__ . "/../backend/includes/db.php";

echo "Making category_id nullable...\n";

try {
    $pdo->exec("ALTER TABLE transactions MODIFY COLUMN category_id INT NULL");
    echo "category_id column is now nullable!\n";
} catch (PDOException $e) {
    echo "Error modifying category_id: " . $e->getMessage() . "\n";
}

echo "Category column fix complete.\n";
?>
