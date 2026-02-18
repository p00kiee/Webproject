<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $customer_id = $_SESSION['customer_id'];

    // Validate inputs
    if (empty($name) || empty($email)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: dashboard.php#profile");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header("Location: dashboard.php#profile");
        exit();
    }

    try {
        // Check if email exists for other users
        $stmt = $pdo->prepare("SELECT id FROM customers WHERE email = ? AND id != ?");
        $stmt->execute([$email, $customer_id]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Email already exists";
            header("Location: dashboard.php#profile");
            exit();
        }

        // Update profile
        $stmt = $pdo->prepare("UPDATE customers SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $customer_id]);

        $_SESSION['success'] = "Profile updated successfully";
        header("Location: dashboard.php#profile");
        exit();

    } catch(PDOException $e) {
        $_SESSION['error'] = "Update failed: " . $e->getMessage();
        header("Location: dashboard.php#profile");
        exit();
    }
}
?>