<?php
require_once '../config/db.php';
setHeaders();
$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'POST') {
    $db->begin_transaction();
    try {
        $stmt = $db->prepare("INSERT INTO orders (customer_id, total_amount) VALUES (?, ?)");
        $total = 0; 
        $db->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['error' => $e->getMessage()]);
    }
}
$db->close();
?>