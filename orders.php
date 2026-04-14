<?php
require_once '../config/db.php';
setHeaders();

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    $res = $db->query("
        SELECT o.*, c.name AS customer_name 
        FROM orders o 
        JOIN customers c USING(customer_id) 
        ORDER BY o.created_at DESC
    ");
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));
} 

elseif ($method === 'POST') {
    if (empty($input['customer_id']) || empty($input['items'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data: Customer ID or Items missing']);
        exit;
    }

    $db->begin_transaction(); 

    try {
        $customer_id = $input['customer_id'];
        $total_amount = 0;
        $order_items = [];

        foreach ($input['items'] as $item) {
            $pid = $item['product_id'];
            $qty = $item['quantity'];

            $stmt = $db->prepare("SELECT price, stock FROM products WHERE product_id = ?");
            $stmt->bind_param('i', $pid);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();

            if (!$product || $product['stock'] < $qty) {
                throw new Exception("Product ID $pid has insufficient stock!");
            }

            $price = $product['price'];
            $total_amount += ($price * $qty);
            $order_items[] = ['pid' => $pid, 'qty' => $qty, 'price' => $price];
        }

        $stmt = $db->prepare("INSERT INTO orders (customer_id, total_amount, status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param('id', $customer_id, $total_amount);
        $stmt->execute();
        $order_id = $db->insert_id;

        foreach ($order_items as $oi) {
            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('iiid', $order_id, $oi['pid'], $oi['qty'], $oi['price']);
            $stmt->execute();

            $stmt = $db->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
            $stmt->bind_param('ii', $oi['qty'], $oi['pid']);
            $stmt->execute();
        }

        $db->commit(); 
        echo json_encode(['success' => true, 'order_id' => $order_id]);

    } catch (Exception $e) {
        $db->rollback(); 
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

$db->close();
?>
