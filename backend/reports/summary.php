<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

require __DIR__ . "/../includes/db.php";

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? null;

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
    // Build base query
    $sql = "SELECT 
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expenses,
                COUNT(*) as transaction_count
            FROM transactions 
            WHERE user_id = ? 
            AND transaction_date >= ? 
            AND transaction_date <= ?";
    
    $params = [$userId, $startDate, $endDate];
    
    // Add category filter if specified
    if ($categoryId && $categoryId !== 'all') {
        $sql .= " AND category_id = ?";
        $params[] = $categoryId;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Calculate net savings
    $totalIncome = floatval($result['total_income'] ?? 0);
    $totalExpenses = floatval($result['total_expenses'] ?? 0);
    $netSavings = $totalIncome - $totalExpenses;
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_savings' => $netSavings,
            'transaction_count' => intval($result['transaction_count'] ?? 0),
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
