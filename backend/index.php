<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove query string from URI
$request_uri = explode('?', $request_uri)[0];

// Route the request
switch ($request_uri) {
    case '/auth/login':
    case '/auth/login.php':
        require_once 'auth/login.php';
        break;
    case '/auth/signup':
    case '/auth/signup.php':
        require_once 'auth/signup.php';
        break;
    case '/auth/logout':
    case '/auth/logout.php':
        require_once 'auth/logout.php';
        break;
    case '/categories/list':
    case '/categories/list.php':
        require_once 'categories/list.php';
        break;
    case '/categories/add':
    case '/categories/add.php':
        require_once 'categories/add.php';
        break;
    case '/categories/update':
    case '/categories/update.php':
        require_once 'categories/update.php';
        break;
    case '/categories/delete':
    case '/categories/delete.php':
        require_once 'categories/delete.php';
        break;
    case '/expenses/list':
    case '/expenses/list.php':
        require_once 'expenses/list.php';
        break;
    case '/expenses/add':
    case '/expenses/add.php':
        require_once 'expenses/add.php';
        break;
    case '/transactions/list':
    case '/transactions/list.php':
        require_once 'transactions/list.php';
        break;
    case '/transactions/add':
    case '/transactions/add.php':
        require_once 'transactions/add.php';
        break;
    case '/transactions/delete':
    case '/transactions/delete.php':
        require_once 'transactions/delete.php';
        break;
    case '/users/session':
    case '/users/session.php':
        require_once 'users/session.php';
        break;
    default:
        echo json_encode([
            'success' => false,
            'error' => 'Endpoint not found',
            'uri' => $request_uri
        ]);
        break;
}
?>
