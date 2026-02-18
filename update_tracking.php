<?php
session_start();
require_once '../config/database.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $pdo->prepare("
        INSERT INTO order_tracking (order_id, status, location, description)
        VALUES (?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['order_id'],
        $data['status'],
        $data['location'],
        $data['description']
    ]);

    // Also update the main order status
    if ($success) {
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET delivery_status = ? 
            WHERE id = ?
        ");
        $stmt->execute([$data['status'], $data['order_id']]);
    }

    echo json_encode(['success' => $success]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>