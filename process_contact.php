<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $email = $message = "";
    $errors = [];

    // Validate name
    if (empty($_POST["name"])) {
        $errors['name'] = "Name is required";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate email
    if (empty($_POST["email"])) {
        $errors['email'] = "Email is required";
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format";
        }
    }

    // Validate message
    if (empty($_POST["message"])) {
        $errors['message'] = "Message is required";
    } else {
        $message = trim($_POST["message"]);
    }

    // If no errors, save to database
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_submissions (name, email, message) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $message])) {
                $_SESSION['successMessage'] = "Thank you for your message! We will get back to you soon.";
            } else {
                $_SESSION['errorMessage'] = "Error saving your message. Please try again.";
            }
        } catch(PDOException $e) {
            error_log("Error saving contact submission: " . $e->getMessage());
            $_SESSION['errorMessage'] = "Sorry, there was an error sending your message. Please try again later.";
        }
    } else {
        $_SESSION['formErrors'] = $errors;
        $_SESSION['formData'] = $_POST;
    }
}

header("Location: contact_us.php");
exit();
?>