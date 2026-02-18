<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the cart exists
    if (isset($_SESSION['cart'])) {
        // Unset the cart session
        unset($_SESSION['cart']);
        echo json_encode([
            'success' => true,
            'message' => 'Cart has been cleared successfully.',
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Cart is already empty.',
        ]);
    }
    exit;
} else {
    // If accessed via GET or other methods, return an error
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.',
    ]);
    exit;
}
