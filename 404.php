<?php
// File: LaptopEcom/404.php
$page_title = "Page Not Found";
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        .error-container {
            text-align: center;
            padding: 50px 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .error-code {
            font-size: 120px;
            color: #4f46e5;
            margin: 0;
            line-height: 1;
            font-weight: bold;
        }

        .error-title {
            font-size: 32px;
            color: #1f2937;
            margin: 20px 0;
        }

        .error-message {
            font-size: 18px;
            color: #6b7280;
            margin-bottom: 30px;
        }

        .back-button {
            display: inline-block;
            padding: 12px 24px;
            background: #4f46e5;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s ease;
        }

        .back-button:hover {
            background: #4338ca;
        }

        .suggestions {
            margin-top: 40px;
            padding: 20px;
            background: #f3f4f6;
            border-radius: 8px;
            text-align: left;
        }

        .suggestions h3 {
            color: #1f2937;
            margin-bottom: 15px;
        }

        .suggestions ul {
            list-style-type: none;
            padding: 0;
        }

        .suggestions li {
            margin: 10px 0;
            color: #4b5563;
        }

        .suggestions li:before {
            content: "•";
            color: #4f46e5;
            margin-right: 10px;
        }

        .error-image {
            max-width: 300px;
            margin: 30px auto;
        }
    </style>
</head>
<body>

<div class="error-container">
    <h1 class="error-code">404</h1>
    <h2 class="error-title">Oops! Page Not Found</h2>
    <p class="error-message">The page you're looking for doesn't exist or has been moved.</p>
    
    <a href="/LaptopEcom/index.php" class="back-button">Return to Homepage</a>
    
    <div class="suggestions">
        <h3>Here's what you can do:</h3>
        <ul>
            <li>Double-check the URL for any typing errors</li>
            <li>Go back to the previous page</li>
            <li>Browse our latest laptops on the homepage</li>
            <li>Contact our support team if you need assistance</li>
        </ul>
    </div>
</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>