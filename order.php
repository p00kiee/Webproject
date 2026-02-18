<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Modified query to prevent duplicates
    $stmt = $pdo->prepare("
        SELECT DISTINCT o.*,
            FIRST_VALUE(t.status) OVER (PARTITION BY o.id ORDER BY t.timestamp DESC) as tracking_status,
            FIRST_VALUE(t.location) OVER (PARTITION BY o.id ORDER BY t.timestamp DESC) as tracking_location,
            FIRST_VALUE(t.description) OVER (PARTITION BY o.id ORDER BY t.timestamp DESC) as tracking_description,
            FIRST_VALUE(t.timestamp) OVER (PARTITION BY o.id ORDER BY t.timestamp DESC) as tracking_timestamp
        FROM orders o
        LEFT JOIN order_tracking t ON o.id = t.order_id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
    ");
    
    $stmt->execute([$_SESSION['customer_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Error retrieving orders: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Pendrive Shop</title>
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
            line-height: 1.5;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: var(--card-background);
            border-radius: 1rem;
            box-shadow: var(--shadow-md);
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .page-title img {
            width: 2rem;
            height: 2rem;
            object-fit: contain;
        }

        .order-card {
            background: var(--card-background);
            border-radius: 1rem;
            box-shadow: var(--shadow-md);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .order-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .order-header {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            padding: 1.5rem;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .order-header-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .order-header-item h3 {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .order-content {
            padding: 1.5rem;
        }

        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-box {
            background: var(--background-color);
            padding: 1.25rem;
            border-radius: 0.75rem;
            border: 1px solid var(--border-color);
        }

        .info-box h4 {
            font-size: 1.1rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 600;
            background: var(--primary-color);
            color: white;
        }

        .status-badge.completed {
            background: var(--success-color);
        }

        .status-badge.pending {
            background: var(--warning-color);
        }

        .status-badge.cancelled {
            background: var(--danger-color);
        }

        .tracking-info {
            background: #e8f4fd;
            padding: 1.25rem;
            border-radius: 0.75rem;
            margin-top: 1.5rem;
        }

        .tracking-info h4 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            justify-content: flex-end;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--secondary-color);
        }

        .btn-secondary {
            background: var(--background-color);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--border-color);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: var(--card-background);
            border-radius: 1rem;
            box-shadow: var(--shadow-md);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }
        .tracking-details {
            display: none;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            margin-top: 1rem;
            padding: 1.5rem;
            animation: slideDown 0.3s ease-out;
        }

        .tracking-details.active {
            display: block;
        }

        .tracking-timeline {
            position: relative;
            padding-left: 2rem;
            margin-top: 1rem;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
            margin-left: 1rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -1.5rem;
            top: 0.5rem;
            width: 1rem;
            height: 1rem;
            background: var(--primary-color);
            border: 2px solid white;
            border-radius: 50%;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: -1rem;
            top: 1.5rem;
            width: 2px;
            height: calc(100% - 1rem);
            background: var(--border-color);
        }

        .timeline-item:last-child::after {
            display: none;
        }

        .timeline-date {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .timeline-status {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .timeline-location {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .btn-track {
            background: var(--background-color);
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .btn-track:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-track.active {
            background: var(--primary-color);
            color: white;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .order-header {
                grid-template-columns: 1fr;
            }

            .order-info-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Animation classes */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-in {
            animation: slideIn 0.3s ease forwards;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-shopping-bag"></i>
                My Orders
            </h1>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-home"></i>
                Back to Home
            </a>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger animate-slide-in">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="empty-state animate-slide-in">
                <i class="fas fa-shopping-cart"></i>
                <p>You haven't placed any orders yet.</p>
                <a href="products.php" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card animate-slide-in">
                    <div class="order-header">
                        <div class="order-header-item">
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <span><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="order-header-item">
                            <h3>Total Amount</h3>
                            <span>Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
                        </div>
                        <div class="order-header-item">
                            <h3>Status</h3>
                            <span class="status-badge <?php echo strtolower($order['delivery_status']); ?>">
                                <?php echo ucfirst($order['delivery_status']); ?>
                            </span>
                        </div>
                    </div>

                    <div class="order-content">
                        <div class="order-info-grid">
                            <div class="info-box">
                                <h4><i class="fas fa-shipping-fast"></i> Shipping Details</h4>
                                <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                            </div>

                            <div class="info-box">
                                <h4><i class="fas fa-credit-card"></i> Payment Information</h4>
                                <p><strong>Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                                <p>
                                    <strong>Status:</strong>
                                    <span class="status-badge <?php echo $order['payment_status']; ?>">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-eye"></i>
                                View Details
                            </a>
                            <?php if ($order['delivery_status'] != 'delivered'): ?>
                                <button onclick="toggleTracking('<?php echo $order['id']; ?>')" class="btn btn-track" id="track-btn-<?php echo $order['id']; ?>">
                                    <i class="fas fa-truck"></i>
                                    Track Order
                                </button>
                            <?php endif; ?>
                        </div>

                        <div id="tracking-details-<?php echo $order['id']; ?>" class="tracking-details">
                            <h4><i class="fas fa-map-marker-alt"></i> Tracking History</h4>
                            <div class="tracking-timeline">
                                <?php 
                                try {
                                    $tracking_stmt = $pdo->prepare("
                                        SELECT * FROM order_tracking 
                                        WHERE order_id = ? 
                                        ORDER BY timestamp DESC
                                    ");
                                    $tracking_stmt->execute([$order['id']]);
                                    $tracking_history = $tracking_stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if (!empty($tracking_history)): 
                                        foreach ($tracking_history as $tracking): 
                                ?>
                                            <div class="timeline-item">
                                                <div class="timeline-date">
                                                    <?php echo date('M d, Y H:i', strtotime($tracking['timestamp'])); ?>
                                                </div>
                                                <div class="timeline-status">
                                                    <strong><?php echo htmlspecialchars($tracking['status']); ?></strong>
                                                </div>
                                                <?php if (!empty($tracking['description'])): ?>
                                                    <div class="timeline-description">
                                                        <?php echo htmlspecialchars($tracking['description']); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($tracking['location'])): ?>
                                                    <div class="timeline-location">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        <?php echo htmlspecialchars($tracking['location']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                <?php 
                                        endforeach;
                                    else: 
                                ?>
                                        <div class="empty-tracking">
                                            <p><i class="fas fa-info-circle"></i> No tracking information available yet.</p>
                                        </div>
                                <?php 
                                    endif; 
                                } catch(PDOException $e) {
                                    echo '<div class="error-message">Error retrieving tracking information.</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
        function toggleTracking(orderId) {
            const trackingDetails = document.getElementById(`tracking-details-${orderId}`);
            const trackButton = document.getElementById(`track-btn-${orderId}`);
            
            if (trackingDetails) {
                trackingDetails.classList.toggle('active');
                trackButton.classList.toggle('active');
                
                if (trackingDetails.classList.contains('active')) {
                    trackButton.innerHTML = '<i class="fas fa-chevron-up"></i> Hide Tracking';
                } else {
                    trackButton.innerHTML = '<i class="fas fa-truck"></i> Track Order';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.order-card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-slide-in');
                    }
                });
            }, {
                threshold: 0.1
            });

            cards.forEach(card => observer.observe(card));
        });
    </script>
</body>
</html>