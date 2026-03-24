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

$months = intval($data['months'] ?? 6); // Default to 6 months

try {
    // Get monthly data for the last N months
    $sql = "SELECT 
                DATE_FORMAT(transaction_date, '%Y-%m') as month,
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expenses
            FROM transactions 
            WHERE user_id = ? 
            AND transaction_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
            GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
            ORDER BY month ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $months]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the data for Chart.js
    $chartData = [
        'labels' => [],
        'income' => [],
        'expenses' => []
    ];
    
    foreach ($results as $row) {
        // Format month as "Jan 2026"
        $date = DateTime::createFromFormat('Y-m', $row['month']);
        $chartData['labels'][] = $date->format('M Y');
        $chartData['income'][] = floatval($row['income']);
        $chartData['expenses'][] = floatval($row['expenses']);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $chartData
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
