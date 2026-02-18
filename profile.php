<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Pendrive Shop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
        .back-to-home {
            text-decoration: none;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .col-md-4, .col-md-8 {
            flex: 1;
        }
        .col-md-4 {
            max-width: 30%;
        }
        .col-md-8 {
            max-width: 65%;
        }
        .stats-card {
            padding: 20px;
            border-radius: 8px;
            color: white;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .bg-gradient-primary {
            background: linear-gradient(45deg, #007bff, #00bcd4);
        }
        .bg-gradient-success {
            background: linear-gradient(45deg, #28a745, #84c991);
        }
        .bg-gradient-info {
            background: linear-gradient(45deg, #17a2b8, #89d4e3);
        }
        .profile-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        h4 {
            margin-bottom: 15px;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>My Profile</h2>
            <a href="index.php" class="back-to-home">Back to Home</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="stats-card bg-gradient-primary">
                    <h4><?php echo $stats['total_orders'] ?? 0; ?></h4>
                    <p>Total Orders</p>
                </div>
                <div class="stats-card bg-gradient-success">
                    <h4>Rs. <?php echo number_format($stats['total_spent'] ?? 0, 2); ?></h4>
                    <p>Total Spent</p>
                </div>
                <div class="stats-card bg-gradient-info">
                    <h4><?php echo $stats['completed_orders'] ?? 0; ?></h4>
                    <p>Completed Orders</p>
                </div>
            </div>

            <div class="col-md-8">
                <div class="profile-card">
                    <h4>Profile Information</h4>
                    <form method="POST" action="">
                        <input type="text" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" placeholder="Full Name" required>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" placeholder="Email Address" required>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>" placeholder="Phone Number">
                        <textarea name="address" rows="3" placeholder="Address"><?php echo htmlspecialchars($customer['address'] ?? ''); ?></textarea>
                        <button type="submit" name="update_profile">Update Profile</button>
                    </form>
                </div>

                <div class="profile-card">
                    <h4>Change Password</h4>
                    <form action="update-password.php" method="POST">
                        <input type="password" name="current_password" placeholder="Current Password" required>
                        <input type="password" name="new_password" placeholder="New Password" required>
                        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                        <button type="submit">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
