<?php
require __DIR__ . "/../backend/includes/db.php";

echo "Adding type field to categories table...\n";

try {
    $pdo->exec("ALTER TABLE categories ADD COLUMN type ENUM('income', 'expense', 'both') NOT NULL DEFAULT 'expense' AFTER color");
    echo "Type field added successfully!\n";
    
    // Update existing categories to be expense type by default
    $pdo->exec("UPDATE categories SET type = 'expense' WHERE type IS NULL");
    echo "Existing categories updated to expense type!\n";
    
} catch (PDOException $e) {
    echo "Error adding type field: " . $e->getMessage() . "\n";
}

echo "Category type setup complete.\n";
?>
