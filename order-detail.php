<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $stmt = $pdo->prepare("
        SELECT o.*, oi.quantity, oi.price as item_price,
               p.name as product_name, p.image as product_image
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $_SESSION['customer_id']]);
    $order_items = $stmt->fetchAll();

    if (empty($order_items)) {
        header("Location: dashboard.php");
        exit();
    }

    $order_summary = [
        'id' => $order_items[0]['id'],
        'created_at' => $order_items[0]['created_at'],
        'total_amount' => $order_items[0]['total_amount'],
        'payment_method' => $order_items[0]['payment_method'],
        'payment_status' => $order_items[0]['payment_status'],
        'delivery_status' => $order_items[0]['delivery_status'],
        'shipping_address' => $order_items[0]['shipping_address'],
        'phone' => $order_items[0]['phone']
    ];

    $tracking_stmt = $pdo->prepare("
        SELECT * FROM order_tracking 
        WHERE order_id = ? 
        ORDER BY timestamp DESC
    ");
    $tracking_stmt->execute([$order_id]);
    $tracking_history = $tracking_stmt->fetchAll();

} catch(PDOException $e) {
    $_SESSION['error'] = "Error retrieving order details: " . $e->getMessage();
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details #<?php echo $order_id; ?> - Pendrive Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --background-color: #f3f4f6;
            --card-background: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --border-radius: 1rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
            line-height: 1.6;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            margin-bottom: 2rem;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: var(--secondary-color);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .order-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .status-badge {
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: white;
        }

        .status-pending { background-color: var(--warning-color); }
        .status-processing { background-color: var(--primary-color); }
        .status-shipped { background-color: var(--secondary-color); }
        .status-delivered { background-color: var(--success-color); }

        .order-card {
            background-color: var(--card-background);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
        }

        .order-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .order-info h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-item {
            margin-bottom: 0.75rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .info-label {
            font-weight: 500;
            color: var(--text-secondary);
            min-width: 120px;
        }

        .info-value {
            color: var(--text-primary);
        }

        .product-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1rem;
        }

        .product-table th {
            background-color: var(--background-color);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-secondary);
            border-bottom: 2px solid var(--border-color);
        }

        .product-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        .product-name {
            font-weight: 500;
            color: var(--text-primary);
        }

        .price {
            font-weight: 600;
            color: var(--text-primary);
        }

        .total-row td {
            padding-top: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            border-bottom: none;
        }

        .progress-tracker {
            margin: 2rem 0 3rem;
            position: relative;
            display: flex;
            justify-content: space-between;
        }

        .progress-line {
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--border-color);
            z-index: 1;
        }

        .progress-line-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .progress-step {
            position: relative;
            z-index: 2;
            flex: 1;
            text-align: center;
        }

        .progress-step-dot {
            width: 30px;
            height: 30px;
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 50%;
            margin: 0 auto 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .progress-step.completed .progress-step-dot {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .progress-step-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .progress-step.completed .progress-step-label {
            color: var(--primary-color);
        }

        .tracking-timeline {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .timeline-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: var(--background-color);
            border-radius: 0.5rem;
            align-items: flex-start;
        }

        .timeline-dot {
            width: 12px;
            height: 12px;
            background: var(--primary-color);
            border-radius: 50%;
            margin-top: 0.25rem;
            flex-shrink: 0;
        }

        .timeline-content {
            flex: 1;
        }

        .tracking-date {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .tracking-status {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .tracking-message {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .tracking-empty {
            text-align: center;
            padding: 2rem;
            background: var(--background-color);
            border-radius: var(--border-radius);
            color: var(--text-secondary);
        }

        .tracking-empty i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--text-secondary);
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .order-grid {
                grid-template-columns: 1fr;
            }

            .product-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .tracking-timeline {
                padding-left: 1rem;
            }

            .tracking-item {
                padding-left: 1rem;
            }

            .tracking-item::before {
                left: -1rem;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-button animate-fade-in">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <div class="order-header animate-fade-in">
            <h1 class="order-title">Order #<?php echo $order_id; ?></h1>
            <span class="status-badge status-<?php echo strtolower($order_summary['delivery_status']); ?>">
                <?php echo ucfirst($order_summary['delivery_status']); ?>
            </span>
        </div>

        <div class="order-card animate-fade-in">
            <div class="order-grid">
                <div class="order-info">
                    <h3><i class="fas fa-info-circle"></i> Order Information</h3>
                    <div class="info-item">
                        <span class="info-label">Order Date:</span>
                        <span class="info-value">
                            <?php echo date('M d, Y H:i', strtotime($order_summary['created_at'])); ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Payment Method:</span>
                        <span class="info-value">
                            <?php echo ucfirst($order_summary['payment_method']); ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Payment Status:</span>
                        <span class="status-badge status-<?php echo $order_summary['payment_status']; ?>">
                            <?php echo ucfirst($order_summary['payment_status']); ?>
                        </span>
                    </div>
                </div>

                <div class="order-info">
                    <h3><i class="fas fa-shipping-fast"></i> Shipping Information</h3>
                    <div class="info-item">
                        <span class="info-label">Address:</span>
                        <span class="info-value">
                            <?php echo nl2br(htmlspecialchars($order_summary['shipping_address'])); ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">
                            <?php echo htmlspecialchars($order_summary['phone']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="order-card animate-fade-in">
            <h3><i class="fas fa-shopping-cart"></i> Order Items</h3>
            <div class="table-responsive">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Image</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($order_items as $item): ?>
                            <tr>
                                <td class="product-name">
                                    <?php echo htmlspecialchars($item['product_name']); ?>
                                </td>
                                <td>
                                    <img src="../assets/uploads/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                         class="product-image" alt="Product">
                                </td>
                                <td class="price">Rs. <?php echo number_format($item['item_price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td class="price">Rs. <?php echo number_format($item['item_price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="4" style="text-align: right;">Total:</td>
                            <td>Rs. <?php echo number_format($order_summary['total_amount'], 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="order-card animate-fade-in">
            <h3><i class="fas fa-truck"></i> Order Tracking</h3>
            <?php if (!empty($tracking_history)): ?>
                <div class="tracking-timeline">
                    <div class="timeline-line"></div>
                    <?php foreach($tracking_history as $track): ?>
                        <div class="tracking-item">
                            <div class="tracking-content">
                                <div class="tracking-date">
                                    <i class="fas fa-clock"></i> 
                                    <?php echo date('M d, Y H:i', strtotime($track['timestamp'])); ?>
                                </div>
                                <div class="tracking-status">
                                    <?php echo htmlspecialchars($track['status']); ?>
                                </div>
                                <?php if (!empty($track['location'])): ?>
                                    <div class="tracking-location">
                                        <i class="fas fa-map-marker-alt"></i> 
                                        <?php echo htmlspecialchars($track['location']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($track['description'])): ?>
                                    <div class="tracking-location">
                                        <i class="fas fa-info-circle"></i>
                                        <?php echo htmlspecialchars($track['description']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="tracking-empty">
                    <i class="fas fa-truck"></i>
                    <p>No tracking information available yet. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Add animation when elements come into view
        document.addEventListener('DOMContentLoaded', function() {
            const animatedElements = document.querySelectorAll('.animate-fade-in');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1
            });

            animatedElements.forEach(element => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(10px)';
                observer.observe(element);
            });
        });
    </script>
</body>
</html>