<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

require __DIR__ . "/../includes/db.php";

$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'] ?? ($data['user_id'] ?? null);

if (!$userId) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$startDate = $data['start_date'] ?? '';
$endDate = $data['end_date'] ?? '';
$categoryId = $data['category_id'] ?? null;

if (!$startDate || !$endDate) {
    echo json_encode(['success' => false, 'error' => 'Start date and end date are required']);
    exit;
}

try {
    // Build query to get transactions
    $sql = "SELECT 
                t.transaction_date,
                t.description,
                c.name as category,
                t.amount,
                t.type,
                t.notes
            FROM transactions t
            LEFT JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = ? 
            AND t.transaction_date >= ? 
            AND t.transaction_date <= ?";
    
    $params = [$userId, $startDate, $endDate];
    
    // Add category filter if specified
    if ($categoryId && $categoryId !== 'all') {
        $sql .= " AND t.category_id = ?";
        $params[] = $categoryId;
    }
    
    $sql .= " ORDER BY t.transaction_date DESC, t.id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="transactions_' . date('Y-m-d') . '.csv"');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for Excel UTF-8 support
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Add CSV headers
    fputcsv($output, ['Date', 'Description', 'Category', 'Amount', 'Type', 'Notes']);
    
    // Add data rows
    foreach ($transactions as $transaction) {
        fputcsv($output, [
            $transaction['transaction_date'],
            $transaction['description'],
            $transaction['category'] ?? 'Uncategorized',
            number_format($transaction['amount'], 2),
            ucfirst($transaction['type']),
            $transaction['notes'] ?? ''
        ]);
    }
    
    fclose($output);
    exit;
    
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
