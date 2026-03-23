-- Add recurring transaction fields to the transactions table
ALTER TABLE transactions 
ADD COLUMN is_recurring BOOLEAN DEFAULT FALSE AFTER notes,
ADD COLUMN recurring_frequency ENUM('daily', 'weekly', 'biweekly', 'monthly', 'quarterly', 'yearly') NULL AFTER is_recurring,
ADD COLUMN recurring_interval INT DEFAULT 1 AFTER recurring_frequency,
ADD COLUMN recurring_end_date DATE NULL AFTER recurring_interval,
ADD COLUMN parent_transaction_id INT NULL AFTER recurring_end_date,
ADD INDEX idx_recurring (parent_transaction_id),
ADD FOREIGN KEY (parent_transaction_id) REFERENCES transactions(id) ON DELETE SET NULL;
