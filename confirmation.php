<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: login?redirect=confirmation');
    exit();
}

// Check if order ID exists
if (!isset($_GET['order_id'])) {
    header('Location: index');
    exit();
}

$order_id = intval($_GET['order_id']);

try {
    // Fetch order details with customer info
    $stmt = $pdo->prepare("
        SELECT o.*, c.name as customer_name, c.email 
        FROM orders o
        JOIN customers c ON o.user_id = c.id
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $_SESSION['customer_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header('Location: index');
        exit();
    }

    // Fetch order items
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name as product_name, p.image, p.price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: index');
    exit();
}

// Calculate subtotal (excluding delivery charges)
$subtotal = 0;
foreach ($order_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// Delivery charge is excluded from total cost
$total = $subtotal;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        :root {
            --primary: #2563eb;
            --success: #16a34a;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 20px;
            color: var(--gray-800);
        }

        .confirmation-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .confirmation-header {
            background: var(--success);
            color: white;
            text-align: center;
            padding: 40px 20px;
        }

        .success-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .confirmation-content {
            padding: 30px;
        }

        .order-details {
            margin-bottom: 30px;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            overflow: hidden;
        }

        .details-header {
            background: var(--gray-100);
            padding: 15px 20px;
            font-weight: 600;
        }

        .details-content {
            padding: 20px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .detail-label {
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        .detail-value {
            font-weight: 500;
        }

        .order-summary {
            margin-top: 30px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table th,
        .summary-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--gray-200);
        }

        .summary-table th {
            background: var(--gray-100);
            font-weight: 600;
        }

        .summary-table td {
            vertical-align: middle;
        }

        .total-row {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .free-label {
            color: #d1d5db; /* Gray for strikethrough */
            margin-right: 8px;
        }

        .free-text {
            color: var(--success);
            font-weight: 600;
        }

        .savings-notification {
            margin-top: 15px;
            background: rgba(22, 163, 74, 0.1); /* Light green background */
            color: var(--success);
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 0.9rem;
            text-align: center;
        }

        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--gray-200);
        }

        .btn {
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-outline {
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="confirmation-header">
            <div class="success-icon">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>
            <h1>Order Confirmed!</h1>
            <p>Thank you for your order. Your order has been received successfully.</p>
        </div>

        <div class="confirmation-content">
            <div class="order-details">
                <div class="details-header">Order Information</div>
                <div class="details-content">
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Order Number</span>
                            <span class="detail-value">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Order Date</span>
                            <span class="detail-value"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Payment Method</span>
                            <span class="detail-value"><?php echo ucfirst($order['payment_method']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Shipping Address</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order['shipping_address']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-summary">
                <div class="details-header">Order Summary</div>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                                <td>Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3">Delivery Charge</td>
                            <td>
                                <span class="free-label">
                                    <strike>Rs. 1,000.00</strike>
                                </span>
                                <span class="free-text">FREE</span>
                            </td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3">Total Amount</td>
                            <td>Rs. <?php echo number_format($total, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
                <div class="savings-notification">
                    🎉 You saved Rs. 1,000.00 on delivery!
                </div>
            </div>

            <div class="actions">
                <a href="index" class="btn btn-primary">Continue Shopping</a>
                <a href="order" class="btn btn-outline">View All Orders</a>
            </div>
        </div>
    </div>
</body>
</html>
