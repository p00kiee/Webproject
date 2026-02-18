<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $payment_method = trim($_POST['payment_method']);

    if (!$name || !$email || !$address || !$phone || !$payment_method) {
        $_SESSION['error'] = 'Please fill in all the fields.';
        header('Location: checkout.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        header('Location: checkout.php');
        exit;
    }

    $cart_items = [];
    $total = 0;

    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute(array_keys($_SESSION['cart']));
        $products = $stmt->fetchAll();

        foreach ($products as $product) {
            $quantity = $_SESSION['cart'][$product['id']];
            if ($quantity > $product['stock']) {
                $_SESSION['error'] = "Insufficient stock for product: " . htmlspecialchars($product['name']);
                header('Location: checkout.php');
                exit;
            }
            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;

            $cart_items[] = [
                'product_id' => $product['id'],
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }
    } else {
        $_SESSION['error'] = 'Your cart is empty.';
        header('Location: cart.php');
        exit;
    }

    $delivery_charge = ($total > 0) ? 100 : 0;
    $grand_total = $total + $delivery_charge;

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, name, email, address, phone, payment_method, total_amount, created_at) 
            VALUES (:user_id, :name, :email, :address, :phone, :payment_method, :total_amount, NOW())
        ");
        $stmt->execute([
            ':user_id' => $_SESSION['customer_id'],
            ':name' => $name,
            ':email' => $email,
            ':address' => $address,
            ':phone' => $phone,
            ':payment_method' => $payment_method,
            ':total_amount' => $grand_total,
        ]);
        $order_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, subtotal) 
            VALUES (:order_id, :product_id, :quantity, :subtotal)
        ");
        foreach ($cart_items as $item) {
            $stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $item['product_id'],
                ':quantity' => $item['quantity'],
                ':subtotal' => $item['subtotal'],
            ]);

            $stmt = $pdo->prepare("UPDATE products SET stock = stock - :quantity WHERE id = :product_id");
            $stmt->execute([
                ':quantity' => $item['quantity'],
                ':product_id' => $item['product_id'],
            ]);
        }

        $pdo->commit();
        unset($_SESSION['cart']);
        header('Location: confirmation.php?order_id=' . $order_id);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log($e->getMessage(), 3, 'logs/error.log');
        $_SESSION['error'] = 'Failed to process your order. Please try again.';
        header('Location: checkout.php');
        exit;
    }
} else {
    $_SESSION['error'] = 'You must be logged in to place an order.';
    header('Location: checkout.php');
    exit;
}
?>
