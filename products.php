<?php
session_start();
require_once '../config/db.php';
setHeaders();
$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    $res = $db->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c USING(category_id) ORDER BY p.created_at DESC");
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));
} 
elseif ($method === 'POST') {
    if (!isset($_SESSION['is_admin'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized access']);
        exit;
    }
    $stmt = $db->prepare("INSERT INTO products (category_id, name, price, stock, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('isdis', $input['category_id'], $input['name'], $input['price'], $input['stock'], $input['image_url']);
    if ($stmt->execute()) echo json_encode(['success' => true]);
    else echo json_encode(['error' => $stmt->error]);
}
$db->close();
?>