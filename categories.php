<?php
require_once '../config/db.php';
setHeaders();

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    $result = $db->query("SELECT * FROM categories ORDER BY name");
    $rows = [];

    while ($r = $result->fetch_assoc()) {
        $rows[] = $r;
    }

    echo json_encode($rows);
}

elseif ($method === 'POST') {
    if (empty($input['name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Name required']);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    $stmt->bind_param('ss', $input['name'], $input['description'] ?? '');

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'category_id' => $db->insert_id
        ]);
    } else {
        http_response_code(409);
        echo json_encode(['error' => $stmt->error]);
    }
}

$db->close();