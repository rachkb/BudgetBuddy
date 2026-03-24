<?php
require __DIR__ . "/../includes/db.php";

echo "Adding recurring transaction fields...\n";

$sql = "ALTER TABLE transactions 
ADD COLUMN notes TEXT NULL AFTER transaction_date,
ADD COLUMN is_recurring BOOLEAN DEFAULT FALSE AFTER notes,
ADD COLUMN recurring_frequency ENUM('daily', 'weekly', 'biweekly', 'monthly', 'quarterly', 'yearly') NULL AFTER is_recurring,
ADD COLUMN recurring_interval INT DEFAULT 1 AFTER recurring_frequency,
ADD COLUMN recurring_end_date DATE NULL AFTER recurring_interval,
ADD COLUMN parent_transaction_id INT NULL AFTER recurring_end_date,
ADD INDEX idx_recurring (parent_transaction_id)";

try {
    $pdo->exec($sql);
    echo "Recurring fields added successfully!\n";
    
    // Add foreign key separately in case there are issues
    $fkSql = "ALTER TABLE transactions ADD FOREIGN KEY (parent_transaction_id) REFERENCES transactions(id) ON DELETE SET NULL";
    $pdo->exec($fkSql);
    echo "Foreign key added successfully!\n";
    
} catch (PDOException $e) {
    echo "Error adding recurring fields: " . $e->getMessage() . "\n";
}

echo "Recurring transaction setup complete.\n";
?>
