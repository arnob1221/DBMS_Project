<?php
session_start();

require_once '../config/db.php';
setHeaders(); // sets Content-Type + CORS headers
$input = json_decode(file_get_contents('php://input'), true);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    if (($input['password'] ?? '') === 'admin12') {
        $_SESSION['is_admin'] = true; // 
        echo json_encode(['success' => true]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Wrong password']);
    }
}

elseif ($method === 'DELETE') {
    session_destroy();
    echo json_encode(['success' => true]);
}