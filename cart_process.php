<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing action parameter']);
    exit;
}

$action = $_POST['action'];
$response = ['success' => false, 'message' => ''];

switch ($action) {
    case 'add':
        if (isset($_POST['product_id'], $_POST['quantity'])) {
            $product_id = (int)$_POST['product_id'];
            $quantity = (int)$_POST['quantity'];

            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND stock >= ?");
            $stmt->execute([$product_id, $quantity]);
            $product = $stmt->fetch();

            if ($product) {
                if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
                $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + $quantity;

                if ($_SESSION['cart'][$product_id] > $product['stock']) {
                    $_SESSION['cart'][$product_id] = $product['stock'];
                }

                $response = ['success' => true, 'message' => 'Product added to cart', 'cart_count' => array_sum($_SESSION['cart'])];
            } else {
                $response['message'] = 'Product not available or insufficient stock.';
            }
        } else {
            $response['message'] = 'Invalid product data.';
        }
        break;

    case 'update':
        if (isset($_POST['product_id'], $_POST['change'])) {
            $product_id = (int)$_POST['product_id'];
            $change = (int)$_POST['change'];

            if (isset($_SESSION['cart'][$product_id])) {
                $new_quantity = $_SESSION['cart'][$product_id] + $change;

                $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch();

                if ($new_quantity > 0 && $new_quantity <= $product['stock']) {
                    $_SESSION['cart'][$product_id] = $new_quantity;
                    $response = ['success' => true, 'quantity' => $new_quantity];
                } elseif ($new_quantity <= 0) {
                    unset($_SESSION['cart'][$product_id]);
                    $response = ['success' => true, 'quantity' => 0];
                } else {
                    $response['message'] = 'Quantity exceeds available stock.';
                }
            } else {
                $response['message'] = 'Product not found in cart.';
            }
        } else {
            $response['message'] = 'Invalid parameters.';
        }
        break;

    case 'remove':
        if (isset($_POST['product_id'])) {
            $product_id = (int)$_POST['product_id'];
            unset($_SESSION['cart'][$product_id]);
            $response = ['success' => true, 'message' => 'Product removed from cart.'];
        } else {
            $response['message'] = 'Invalid product ID.';
        }
        break;

    case 'clear':
        $_SESSION['cart'] = [];
        $response = ['success' => true, 'message' => 'Cart cleared.'];
        break;

    default:
        $response['message'] = 'Invalid action.';
        break;
}

echo json_encode($response);
