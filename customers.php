<?php
require_once '../config/db.php';
setHeaders();

$db = getDB();

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// ── GET ─────────────────────
if ($method === 'GET') {
    $res = $db->query("SELECT * FROM customers ORDER BY created_at DESC");
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));
}

// ── POST ────────────────────
elseif ($method === 'POST') {

    if (empty($input['name']) || empty($input['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Name and Email are required']);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)");

    $phone = $input['phone'] ?? '';
    $address = $input['address'] ?? '';

    $stmt->bind_param('ssss', $input['name'], $input['email'], $phone, $address);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'customer_id' => $db->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'error' => $stmt->error 
        ]);
    }
}

$db->close();