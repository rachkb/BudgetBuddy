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
$type = $data['type'] ?? 'expense'; // Default to expenses

if (!$startDate || !$endDate) {
    echo json_encode(['success' => false, 'error' => 'Start date and end date are required']);
    exit;
}

try {
    // Get category breakdown for the specified type
    $sql = "SELECT 
                c.id,
                c.name,
                c.color,
                c.icon,
                SUM(t.amount) as total,
                COUNT(t.id) as transaction_count
            FROM transactions t
            LEFT JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = ? 
            AND t.transaction_date >= ? 
            AND t.transaction_date <= ?
            AND t.type = ?
            GROUP BY c.id, c.name, c.color, c.icon
            ORDER BY total DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $startDate, $endDate, $type]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data for pie chart
    $categories = [];
    foreach ($results as $row) {
        $categories[] = [
            'id' => $row['id'],
            'name' => $row['name'] ?? 'Uncategorized',
            'color' => $row['color'] ?? 'primary',
            'icon' => $row['icon'] ?? 'bi-tag',
            'total' => floatval($row['total']),
            'transaction_count' => intval($row['transaction_count'])
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $categories
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
