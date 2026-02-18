<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// Check if order_id is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header("Location: order.php");
    exit();
}

try {
    // First verify if the order belongs to the logged-in user
    $verify_stmt = $pdo->prepare("
        SELECT id FROM orders 
        WHERE id = ? AND user_id = ?
    ");
    $verify_stmt->execute([$_GET['order_id'], $_SESSION['customer_id']]);
    
    if (!$verify_stmt->fetch()) {
        header("Location: order.php");
        exit();
    }

    // Now fetch the order details
    $stmt = $pdo->prepare("
        SELECT o.*, 
               GROUP_CONCAT(oi.quantity) as quantities,
               GROUP_CONCAT(p.name) as product_names,
               t.status as tracking_status,
               t.location,
               t.timestamp as tracking_timestamp
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        LEFT JOIN (
            SELECT order_id, status, location, timestamp
            FROM order_tracking
            WHERE (order_id, timestamp) IN (
                SELECT order_id, MAX(timestamp)
                FROM order_tracking
                GROUP BY order_id
            )
        ) t ON o.id = t.order_id
        WHERE o.id = ? AND o.user_id = ?
        GROUP BY o.id
    ");
    
    $stmt->execute([$_GET['order_id'], $_SESSION['customer_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // Modified query to prevent duplicates
        $tracking_stmt = $pdo->prepare("
            SELECT DISTINCT ot.status, ot.location, ot.description, ot.timestamp
            FROM order_tracking ot
            WHERE ot.order_id = ?
            GROUP BY ot.timestamp, ot.status, ot.location, ot.description
            ORDER BY ot.timestamp DESC
        ");
        $tracking_stmt->execute([$_GET['order_id']]);
        $tracking_details = $tracking_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch(PDOException $e) {
    $error_message = "Error retrieving order details: " . $e->getMessage();
}

// Include header after all potential redirects
require_once 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order #<?php echo htmlspecialchars($_GET['order_id']); ?> - TechLaptops</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.5;
            color: var(--gray-800);
            background-color: var(--gray-50);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 1rem;
            background: white;
            border-radius: 0.75rem;
            box-shadow: var(--shadow-md);
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-600);
            background: var(--gray-100);
            border: 1px solid var(--gray-200);
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .back-button:hover {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .tracking-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
        }

        .order-info {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: var(--shadow-md);
        }

        .order-info h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 1.5rem;
        }

        .info-group {
            margin-bottom: 1.5rem;
        }

        .info-group:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-500);
            margin-bottom: 0.5rem;
        }

        .info-value {
            color: var(--gray-800);
            font-weight: 500;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 9999px;
            color: white;
            background: var(--primary);
        }

        .status-badge.processing { background: var(--primary); }
        .status-badge.shipped { background: var(--warning); }
        .status-badge.delivered { background: var(--success); }
        .status-badge.cancelled { background: var(--danger); }

        .tracking-timeline {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: var(--shadow-md);
        }

        .tracking-timeline h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 1.5rem;
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 2rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -1.5rem;
            top: 0.25rem;
            width: 1rem;
            height: 1rem;
            background: var(--primary);
            border: 2px solid white;
            border-radius: 50%;
            box-shadow: var(--shadow-sm);
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: -1rem;
            top: 1.5rem;
            bottom: 0;
            width: 2px;
            background: var(--gray-200);
        }

        .timeline-item:last-child::after {
            display: none;
        }

        .timeline-content {
            background: var(--gray-50);
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid var(--gray-200);
        }

        .timeline-date {
            font-size: 0.875rem;
            color: var(--gray-500);
            margin-bottom: 0.5rem;
        }

        .timeline-status {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }

        .timeline-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-600);
        }

        @media (max-width: 768px) {
            .tracking-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Track Order #<?php echo htmlspecialchars($_GET['order_id']); ?></h1>
            <a href="order.php" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Back to Orders
            </a>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php elseif (!$order): ?>
            <div class="alert-error">
                Order not found or you don't have permission to view it.
            </div>
        <?php else: ?>
            <div class="tracking-grid">
                <div class="order-info">
                    <h2>Order Information</h2>
                    <div class="info-group">
                        <div class="info-label">Order Date</div>
                        <div class="info-value">
                            <?php echo date('F d, Y', strtotime($order['created_at'])); ?>
                        </div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Total Amount</div>
                        <div class="info-value">
                            Rs. <?php echo number_format($order['total_amount'], 2); ?>
                        </div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="status-badge <?php echo strtolower($order['delivery_status']); ?>">
                                <?php echo ucfirst($order['delivery_status']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Shipping Address</div>
                        <div class="info-value">
                            <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                        </div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Contact Number</div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($order['phone']); ?>
                        </div>
                    </div>
                </div>

                <div class="tracking-timeline">
                    <h2>Tracking History</h2>
                    <div class="timeline">
                        <?php if (!empty($tracking_details)): ?>
                            <?php foreach ($tracking_details as $track): ?>
                                <div class="timeline-item">
                                    <div class="timeline-content">
                                        <div class="timeline-date">
                                            <?php echo date('F d, Y h:i A', strtotime($track['timestamp'])); ?>
                                        </div>
                                        <div class="timeline-status">
                                            <?php echo htmlspecialchars($track['status']); ?>
                                        </div>
                                        <?php if (!empty($track['location'])): ?>
                                            <div class="timeline-location">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <?php echo htmlspecialchars($track['location']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($track['description'])): ?>
                                            <div class="timeline-description">
                                                <?php echo htmlspecialchars($track['description']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <p>No tracking information available yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>