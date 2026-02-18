<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables with default values to prevent undefined errors
$page_title = "Product Details - TechEquipments";
$current_page = 'product-details';

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// If no product ID provided, redirect to products page
if ($product_id === 0) {
    header('Location: products.php');
    exit;
}

require_once 'includes/header.php';
require_once 'config/database.php';

// Initialize variables
$product = null;
$specifications = [];
$features = [];
$related_products = [];
$categories = [];
$db_error = null;

// Check database connection
if (!$pdo) {
    // Display a user-friendly error instead of stopping execution
    $db_error = 'Database connection failed. Please try again later.';
} else {
    try {
        // Fetch product details
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                              FROM products p 
                              LEFT JOIN categories c ON p.category_id = c.id 
                              WHERE p.id = :id");
        $stmt->execute([':id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // If product not found, set error message
        if (!$product) {
            $db_error = 'Product not found or has been removed.';
        } else {
            // Set page title with product name
            $page_title = htmlspecialchars($product['name']) . " - TechEquipments";
            
            // Fetch product specifications
            $spec_stmt = $pdo->prepare("SELECT * FROM product_specifications WHERE product_id = :id");
            $spec_stmt->execute([':id' => $product_id]);
            $specifications = $spec_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Fetch product features
            $feature_stmt = $pdo->prepare("SELECT * FROM product_features WHERE product_id = :id");
            $feature_stmt->execute([':id' => $product_id]);
            $features = $feature_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Fetch related products from the same category (excluding current product)
            if ($product['category_id']) {
                $related_stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                                              FROM products p 
                                              LEFT JOIN categories c ON p.category_id = c.id 
                                              WHERE p.category_id = :category_id AND p.id != :product_id 
                                              ORDER BY p.created_at DESC LIMIT 4");
                $related_stmt->execute([
                    ':category_id' => $product['category_id'],
                    ':product_id' => $product_id
                ]);
                $related_products = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            // Fetch all categories for the breadcrumb
            $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch(PDOException $e) {
        error_log("Error fetching product details: " . $e->getMessage());
        $db_error = 'An error occurred while retrieving product information.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #003366;
            --primary-dark: #003366;
            --primary-light: #dbeafe;
            --primary-extra-light: #f0f7ff;
            --secondary: #64748b;
            --text: #1e293b;
            --text-light: #64748b;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --border-radius-lg: 16px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.1);
            --shadow: 0 4px 6px rgba(0,0,0,0.05), 0 5px 15px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.1), 0 5px 10px rgba(0,0,0,0.05);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--gray-50);
            color: var(--text);
            line-height: 1.6;
        }

        .product-details-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .breadcrumb-item {
            display: inline-flex;
            align-items: center;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .breadcrumb-item a {
            color: var(--text-light);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb-item a:hover {
            color: var(--primary);
        }

        .breadcrumb-separator {
            margin: 0 0.5rem;
            color: var(--gray-300);
        }

        .breadcrumb-item.active {
            color: var(--text);
            font-weight: 500;
        }

        /* Error Message */
        .error-container {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 3rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .error-icon {
            font-size: 3rem;
            color: var(--error);
            margin-bottom: 1.5rem;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text);
        }

        .error-message {
            color: var(--text-light);
            margin-bottom: 1.5rem;
            font-size: 1.05rem;
        }

        .error-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius-sm);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .error-button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Product Details */
        .product-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        @media (max-width: 992px) {
            .product-details {
                grid-template-columns: 1fr;
            }
        }

        /* Product Gallery */
        .product-gallery {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--gray-100);
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            max-height: 400px;
            object-fit: contain;
            transition: transform 0.5s ease;
        }

        .product-image:hover {
            transform: scale(1.05);
        }

        /* Product Info */
        .product-info {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            border: 1px solid var(--gray-100);
        }

        .product-category {
            display: inline-block;
            background: var(--primary-extra-light);
            color: var(--primary);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 1rem;
            letter-spacing: 0.5px;
        }

        .product-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--text);
            line-height: 1.2;
        }

        .product-price-container {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .product-price {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
        }

        .product-price::before {
            content: 'Rs.';
            font-size: 1.2rem;
            font-weight: 600;
            margin-right: 0.3rem;
            opacity: 0.7;
        }

        .product-price-inquiry {
            font-size: 1.25rem;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .product-price-inquiry i {
            color: var(--warning);
        }

        .product-description {
            margin-bottom: 1.5rem;
            color: var(--text);
            line-height: 1.8;
        }

        .stock-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--success);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .stock-status.out-of-stock {
            background: var(--error);
        }

        .stock-status.low-stock {
            background: var(--warning);
        }

        .product-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius-sm);
            overflow: hidden;
            max-width: 150px;
        }

        .quantity-button {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gray-100);
            border: none;
            cursor: pointer;
            font-weight: 600;
            color: var(--text);
            transition: all 0.3s ease;
        }

        .quantity-button:hover {
            background: var(--gray-200);
        }

        .quantity-input {
            width: 50px;
            height: 40px;
            border: none;
            border-left: 1px solid var(--gray-200);
            border-right: 1px solid var(--gray-200);
            text-align: center;
            font-weight: 600;
        }

        .btn {
            flex: 1;
            padding: 0.85rem 1.5rem;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            font-size: 0.95rem;
            text-align: center;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
            min-width: 180px;
        }

        .btn i {
            font-size: 0.9rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 2px 5px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
        }

        .btn-outline {
            border: 1px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }

        .btn-outline:hover {
            background: var(--primary-extra-light);
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(37, 99, 235, 0.1);
        }

        /* Tabs */
        .product-tabs {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 3rem;
            border: 1px solid var(--gray-100);
            overflow: hidden;
        }

        .tabs-header {
            display: flex;
            border-bottom: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        .tab-button {
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--text-light);
            cursor: pointer;
            background: transparent;
            border: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            position: relative;
        }

        .tab-button::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .tab-button.active {
            color: var(--primary);
        }

        .tab-button.active::after {
            transform: scaleX(1);
        }

        .tab-button:hover {
            color: var(--primary);
        }

        .tab-content {
            display: none;
            padding: 2rem;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease forwards;
        }

        /* Specifications */
        .specifications-table {
            width: 100%;
            border-collapse: collapse;
        }

        .specifications-table tr {
            border-bottom: 1px solid var(--gray-200);
        }

        .specifications-table tr:last-child {
            border-bottom: none;
        }

        .specifications-table th,
        .specifications-table td {
            padding: 1rem;
            text-align: left;
        }

        .specifications-table th {
            width: 40%;
            font-weight: 600;
            color: var(--text);
            background: var(--gray-50);
        }

        .specifications-table td {
            color: var(--text-light);
        }

        /* Features List */
        .features-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .feature-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .feature-icon {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-extra-light);
            color: var(--primary);
            border-radius: 50%;
            font-size: 0.9rem;
        }

        .feature-text {
            flex: 1;
            color: var(--text);
        }

        /* Related Products */
        .related-products {
            margin-bottom: 3rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--text);
            position: relative;
            padding-bottom: 0.5rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background: var(--primary);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .product-card {
            background: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
            border: 1px solid var(--gray-100);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--gray-200);
        }

        .card-image {
            position: relative;
            height: 200px;
            background: var(--gray-50);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--gray-100);
        }

        .card-image img {
            max-width: 85%;
            max-height: 85%;
            object-fit: contain;
            transition: transform 0.5s ease;
        }

        .product-card:hover .card-image img {
            transform: scale(1.08);
        }

        .card-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--error);
            color: white;
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 1;
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3);
        }

        .card-badge.get-now {
            background: var(--success);
            box-shadow: 0 2px 5px rgba(16, 185, 129, 0.3);
        }

        .card-badge.inquiry {
            background: var(--warning);
            box-shadow: 0 2px 5px rgba(245, 158, 11, 0.3);
        }

        .card-content {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .card-category {
            display: inline-block;
            background: var(--primary-extra-light);
            color: var(--primary);
            padding: 0.3rem 0.7rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            letter-spacing: 0.5px;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.8rem;
        }

        .card-price {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 1rem;
            margin-top: auto;
            display: flex;
            align-items: center;
        }

        .card-price::before {
            content: 'Rs.';
            font-size: 0.8rem;
            font-weight: 600;
            margin-right: 0.3rem;
            opacity: 0.7;
        }

        .card-price-inquiry {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 1rem;
            margin-top: auto;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .card-price-inquiry i {
            color: var(--warning);
        }

        .card-action {
            display: block;
            padding: 0.75rem;
            background: var(--primary);
            color: white;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: var(--border-radius-sm);
            transition: all 0.3s ease;
        }

        .card-action:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .product-details-container {
                padding: 1rem;
            }

            .product-details {
                gap: 1.5rem;
            }

            .product-title {
                font-size: 1.5rem;
            }

            .product-price {
                font-size: 1.5rem;
            }

            .tab-button {
                padding: 1rem;
                font-size: 0.9rem;
            }

            .product-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .tabs-header {
                flex-direction: column;
            }

            .tab-button {
                width: 100%;
                text-align: left;
                border-bottom: 1px solid var(--gray-200);
            }

            .tab-button::after {
                display: none;
            }

            .tab-button.active {
                background: var(--primary-extra-light);
            }

            .product-gallery {
                padding: 1rem;
            }

            .product-info {
                padding: 1.5rem;
            }
        }

        /* Animation */
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
    </style>
</head>
<body>
    <div class="product-details-container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <div class="breadcrumb-item">
                <a href="index.php">Home</a>
            </div>
            <div class="breadcrumb-separator">
                <i class="fas fa-chevron-right"></i>
            </div>
            <div class="breadcrumb-item">
                <a href="products.php">Products</a>
            </div>
            <?php if ($product && isset($product['category_name'])): ?>
            <div class="breadcrumb-separator">
                <i class="fas fa-chevron-right"></i>
            </div>
            <div class="breadcrumb-item">
                <a href="products.php?category=<?php echo $product['category_id']; ?>">
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </a>
            </div>
            <?php endif; ?>
            <div class="breadcrumb-separator">
                <i class="fas fa-chevron-right"></i>
            </div>
            <div class="breadcrumb-item active">
                <?php echo $product ? htmlspecialchars($product['name']) : 'Product Details'; ?>
            </div>
        </nav>

        <?php if (isset($db_error)): ?>
            <!-- Error Message Display -->
            <div class="error-container">
                <div class="error-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <h2 class="error-title">Oops! Something went wrong</h2>
                <p class="error-message"><?php echo $db_error; ?></p>
                <a href="products.php" class="error-button">
                    <i class="fas fa-arrow-left"></i>
                    Go back to Products
                </a>
            </div>
        <?php elseif ($product): ?>
            <!-- Product Details Section -->
            <div class="product-details">
                <!-- Product Gallery -->
                <div class="product-gallery">
                    <img src="assets/uploads/<?php echo htmlspecialchars($product['image'] ?? 'default.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="product-image"
                         onerror="this.src='assets/images/default-placeholder.png'">
                </div>

                <!-- Product Info -->
                <div class="product-info">
                    <span class="product-category">
                        <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                    </span>
                    <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                    
                    <div class="product-price-container">
                        <?php if (!isset($product['show_price']) || $product['show_price'] == 1): ?>
                            <div class="product-price">
                                <?php echo number_format($product['price'], 2); ?>
                            </div>
                        <?php else: ?>
                            <div class="product-price-inquiry">
                                <i class="fas fa-info-circle"></i> Contact for Price Information
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-description">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </div>
                    
                    <?php if (isset($product['stock'])): ?>
                        <?php if ($product['stock'] > 10): ?>
                            <div class="stock-status">
                                <i class="fas fa-check-circle"></i> In Stock (<?php echo $product['stock']; ?> available)
                            </div>
                        <?php elseif ($product['stock'] > 0): ?>
                            <div class="stock-status low-stock">
                                <i class="fas fa-exclamation-circle"></i> Low Stock (Only <?php echo $product['stock']; ?> left)
                            </div>
                        <?php else: ?>
                            <div class="stock-status out-of-stock">
                                <i class="fas fa-times-circle"></i> Out of Stock
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if ((!isset($product['show_price']) || $product['show_price'] == 1) && isset($product['stock']) && $product['stock'] > 0): ?>
                        <div class="product-actions">
                            <div class="quantity-selector">
                                <button type="button" class="quantity-button" id="decrease-qty">-</button>
                                <input type="number" value="1" min="1" max="<?php echo $product['stock']; ?>" class="quantity-input" id="product-quantity">
                                <button type="button" class="quantity-button" id="increase-qty">+</button>
                            </div>
                            
                            <button type="button" class="btn btn-primary add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    <?php elseif (!isset($product['show_price']) || $product['show_price'] == 1): ?>
                        <!-- Either out of stock but price is shown -->
                        <div class="product-actions">
                            <button type="button" class="btn btn-outline" disabled>
                                <i class="fas fa-shopping-cart"></i> Out of Stock
                            </button>
                            <a href="contact_us.php?product=<?php echo $product['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-envelope"></i> Notify Me
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Price is hidden, inquiry option -->
                        <div class="product-actions">
                            <a href="contact_us.php?product=<?php echo $product['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-envelope"></i> Request Quote
                            </a>
                        </div>
                    <?php endif; ?>
                    </div>
            </div>

            <!-- Product Tabs -->
            <div class="product-tabs">
                <div class="tabs-header">
                    <button class="tab-button active" data-tab="specifications">Specifications</button>
                    <button class="tab-button" data-tab="features">Features & Benefits</button>
                </div>
                
                <!-- Specifications Tab -->
                <div class="tab-content active" id="specifications-tab">
                    <?php if (!empty($specifications)): ?>
                        <table class="specifications-table">
                            <?php foreach ($specifications as $spec): ?>
                                <tr>
                                    <th><?php echo htmlspecialchars($spec['spec_name']); ?></th>
                                    <td><?php echo htmlspecialchars($spec['spec_value']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <p>No specifications available for this product.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Features Tab -->
                <div class="tab-content" id="features-tab">
                    <?php if (!empty($features)): ?>
                        <ul class="features-list">
                            <?php foreach ($features as $feature): ?>
                                <li class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="feature-text">
                                        <?php echo htmlspecialchars($feature['feature_text']); ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No features listed for this product.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Related Products -->
            <?php if (!empty($related_products)): ?>
            <div class="related-products">
                <h2 class="section-title">Related Products</h2>
                
                <div class="products-grid">
                    <?php foreach ($related_products as $related): ?>
                        <div class="product-card">
                            <div class="card-image">
                                <?php if (!isset($related['show_price']) || $related['show_price'] == 1): ?>
                                    <div class="card-badge get-now">Get It Now</div>
                                <?php else: ?>
                                    <div class="card-badge inquiry">By Inquiry</div>
                                <?php endif; ?>
                                
                                <img src="assets/uploads/<?php echo htmlspecialchars($related['image'] ?? 'default.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($related['name']); ?>"
                                     onerror="this.src='assets/images/default-placeholder.png'">
                            </div>
                            
                            <div class="card-content">
                                <span class="card-category">
                                    <?php echo htmlspecialchars($related['category_name'] ?? 'Uncategorized'); ?>
                                </span>
                                
                                <h3 class="card-title">
                                    <?php echo htmlspecialchars($related['name']); ?>
                                </h3>
                                
                                <?php if (!isset($related['show_price']) || $related['show_price'] == 1): ?>
                                    <div class="card-price">
                                        <?php echo number_format($related['price'], 2); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="card-price-inquiry">
                                        <i class="fas fa-info-circle"></i> Contact for Price
                                    </div>
                                <?php endif; ?>
                                
                                <a href="product-details.php?id=<?php echo $related['id']; ?>" class="card-action">
                                    View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
        <?php endif; ?>
    </div>

    <?php require_once 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Tab functionality
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tabId = button.getAttribute('data-tab');
                    
                    // Remove active class from all tabs
                    tabButtons.forEach(tab => tab.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to selected tab
                    button.classList.add('active');
                    document.getElementById(`${tabId}-tab`).classList.add('active');
                });
            });
            
            // Quantity selector
            const decreaseBtn = document.getElementById('decrease-qty');
            const increaseBtn = document.getElementById('increase-qty');
            const quantityInput = document.getElementById('product-quantity');
            
            if (decreaseBtn && increaseBtn && quantityInput) {
                decreaseBtn.addEventListener('click', () => {
                    const currentValue = parseInt(quantityInput.value);
                    if (currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                    }
                });
                
                increaseBtn.addEventListener('click', () => {
                    const currentValue = parseInt(quantityInput.value);
                    const maxValue = parseInt(quantityInput.getAttribute('max'));
                    if (currentValue < maxValue) {
                        quantityInput.value = currentValue + 1;
                    }
                });
                
                // Validate manual input
                quantityInput.addEventListener('change', () => {
                    const currentValue = parseInt(quantityInput.value);
                    const maxValue = parseInt(quantityInput.getAttribute('max'));
                    
                    if (isNaN(currentValue) || currentValue < 1) {
                        quantityInput.value = 1;
                    } else if (currentValue > maxValue) {
                        quantityInput.value = maxValue;
                    }
                });
            }
            
            // Add to cart functionality
            const addToCartBtn = document.querySelector('.add-to-cart-btn');
            
            if (addToCartBtn) {
                addToCartBtn.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const quantity = quantityInput ? quantityInput.value : 1;
                    
                    addToCart(productId, quantity);
                });
            }
            
            // Function to handle Add to Cart
            const addToCart = (productId, quantity) => {
                fetch('ajax/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=${quantity}`,
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Added to Cart',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                            
                            // Update cart count if you have a cart counter
                            const cartCounter = document.querySelector('.cart-count');
                            if (cartCounter && data.cart_count) {
                                cartCounter.textContent = data.cart_count;
                                cartCounter.classList.add('animate-pulse');
                                setTimeout(() => {
                                    cartCounter.classList.remove('animate-pulse');
                                }, 1000);
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An unexpected error occurred. Please try again.',
                        });
                    });
            };
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
</body>
</html> 