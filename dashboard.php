<?php
session_start();
require_once 'config/database.php';  
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php"); 
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$_SESSION['customer_id']]);
    $customer = $stmt->fetch();

    $stmt = $pdo->prepare("
    SELECT DISTINCT o.*, 
           t.status as tracking_status,
           t.location as tracking_location,
           t.timestamp as tracking_timestamp
    FROM orders o
    LEFT JOIN (
        SELECT order_id, status, location, timestamp
        FROM order_tracking
        WHERE (order_id, timestamp) IN (
            SELECT order_id, MAX(timestamp)
            FROM order_tracking
            GROUP BY order_id
        )
    ) t ON o.id = t.order_id
    WHERE o.user_id = ?
    GROUP BY o.id, o.created_at, o.total_amount, o.delivery_status, o.user_id
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['customer_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate totals and stats
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_orders, 
            SUM(total_amount) as total_spent,
            COUNT(CASE WHEN delivery_status = 'delivered' THEN 1 END) as completed_orders,
            COUNT(CASE WHEN delivery_status = 'processing' THEN 1 END) as active_orders
        FROM orders 
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['customer_id']]);
    $stats = $stmt->fetch();
} catch(PDOException $e) {
    $error_message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TechLaptops</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #059669;
            --warning: #d97706;
            --danger: #dc2626;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.5;
            background-color: var(--gray-50);
            color: var(--gray-800);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .dashboard-header {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 2rem;
            margin-bottom: 2rem;
            align-items: center;
        }

        .welcome-text h1 {
            font-size: 1.875rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }

        .welcome-text p {
            color: var(--gray-500);
        }

        .date-display {
            text-align: right;
        }

        .date-display p {
            color: var(--gray-500);
            margin-bottom: 0.25rem;
        }

        .date-display h5 {
            font-size: 1rem;
            color: var(--gray-800);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            color: white;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--gray-500);
            font-size: 0.875rem;
        }

        .quick-actions {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            padding: 1.5rem;
        }

        .action-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem;
            border-radius: 0.75rem;
            background: var(--gray-50);
            text-decoration: none;
            color: var(--gray-800);
            transition: all 0.2s;
        }

        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .action-icon {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            background: var(--primary);
            color: white;
            margin-bottom: 1rem;
        }

        .orders-table {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .view-all {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: background 0.2s;
        }

        .view-all:hover {
            background: var(--primary-dark);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid var(--gray-200);
        }

        th {
            font-weight: 500;
            color: var(--gray-600);
            background: var(--gray-50);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-processing { background: var(--warning); color: white; }
        .status-delivered { background: var(--success); color: white; }
        .status-cancelled { background: var(--danger); color: white; }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            background: var(--gray-100);
            color: var(--gray-600);
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background: var(--gray-200);
            color: var(--gray-800);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--gray-400);
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--gray-500);
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .dashboard-header {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .date-display {
                text-align: center;
            }

            .actions-grid {
                grid-template-columns: 1fr;
            }

            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Welcome Section -->
        <header class="dashboard-header">
            <div class="welcome-text">
                <h1>Welcome back, <?php echo htmlspecialchars($customer['name']); ?>!</h1>
                <p>Here's what's happening with your orders today.</p>
            </div>
            <div class="date-display">
                <p>Today's Date</p>
                <h5><?php echo date('F d, Y'); ?></h5>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #4361ee;">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h3 class="stat-number"><?php echo $stats['total_orders'] ?? 0; ?></h3>
                <p class="stat-label">Total Orders</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #059669;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="stat-number"><?php echo $stats['completed_orders'] ?? 0; ?></h3>
                <p class="stat-label">Completed Orders</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #d97706;">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="stat-number"><?php echo $stats['active_orders'] ?? 0; ?></h3>
                <p class="stat-label">Active Orders</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #2563eb;">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3 class="stat-number">Rs. <?php echo number_format($stats['total_spent'] ?? 0); ?></h3>
                <p class="stat-label">Total Spent</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="card-header">
                <h2>Quick Actions</h2>
            </div>
            <div class="actions-grid">
                <a href="products.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3>Browse Products</h3>
                    <p>Explore our collection</p>
                </a>
                <a href="cart.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3>My Cart</h3>
                    <p>View your items</p>
                </a>
                <a href="order.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Track Orders</h3>
                    <p>Monitor shipments</p>
                </a>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="orders-table">
            <div class="table-header">
                <h2>Recent Orders</h2>
                <a href="order.php" class="view-all">View All</a>
            </div>
            
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>No orders yet</h3>
                    <p>Start shopping to see your orders here!</p>
                    <a href="products.php" class="view-all">Browse Products</a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                    <br>
                                    <small style="color: var(--gray-500)">
                                        <?php echo date('h:i A', strtotime($order['created_at'])); ?>
                                    </small>
                                </td>
                                <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <span class="status-pill status-<?php echo strtolower($order['delivery_status']); ?>">
                                        <?php echo ucfirst($order['delivery_status']); ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="order-detail.php?id=<?php echo $order['id']; ?>" 
                                       class="action-btn" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="track-order.php?order_id=<?php echo $order['id']; ?>" 
                                       class="action-btn" 
                                       title="Track Order">
                                        <i class="fas fa-truck"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Add hover effect to action cards
        document.querySelectorAll('.action-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Add hover effect to action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>