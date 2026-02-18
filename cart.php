<?php
session_start();
$page_title = 'Shopping Cart';
$current_page = 'cart';

require_once 'config/database.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch cart items with product details
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    try {
        $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute(array_keys($_SESSION['cart']));
        $products = $stmt->fetchAll();

        foreach ($products as $product) {
            $quantity = $_SESSION['cart'][$product['id']];
            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;
            $cart_items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
    }
}

// Calculate charges
$delivery_charge = ($total > 0) ? 1000 : 0;
$grand_total = $total ;

require_once 'includes/header.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <style>
        /* Base Styles */
        :root {
            --primary: #2563eb;
            --secondary: #1e40af;
            --accent: #dbeafe;
            --danger: #dc2626;
            --success: #16a34a;
            --warning: #f59e0b;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
        }

        /* Cart Container */
        .cart-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-title {
            font-size: 2rem;
            color: var(--gray-800);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Cart Items */
        .cart-items {
            margin-bottom: 30px;
        }

        .cart-item {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        .cart-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .cart-item-image {
            width: 120px;
            height: 120px;
            object-fit: contain;
            background: var(--gray-100);
            padding: 10px;
            border-radius: 8px;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-info h3 {
            font-size: 1.1rem;
            color: var(--gray-800);
            margin-bottom: 8px;
        }

        .item-price {
            color: var(--primary);
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Quantity Controls */
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--gray-100);
            padding: 8px 12px;
            border-radius: 8px;
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: white;
            border-radius: 6px;
            color: var(--gray-800);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover {
            background: var(--primary);
            color: white;
        }

        .quantity-value {
            font-weight: 600;
            color: var(--gray-800);
            min-width: 20px;
            text-align: center;
        }

        /* Subtotal */
        .subtotal {
            font-weight: 600;
            color: var(--gray-800);
            min-width: 100px;
            text-align: right;
        }

        /* Remove Button */
        .remove-btn {
            background: var(--danger);
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .remove-btn:hover {
            background: #b91c1c;
        }

        
.cart-summary {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.summary-header {
    background: #f8fafc;
    padding: 20px 25px;
    border-bottom: 1px solid #e2e8f0;
}

.summary-header h3 {
    color: #1e293b;
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
}

.summary-content {
    padding: 25px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    color: #475569;
    font-size: 1.1rem;
}

.delivery-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.free-tag {
    background: #ecfdf5;
    color: #059669;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.strikethrough {
    text-decoration: line-through;
    color: #94a3b8;
}

.savings-row {
    background: #f0fdf4;
    padding: 12px;
    border-radius: 8px;
    color: #166534;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
    margin-bottom: 20px;
}

.savings-icon {
    font-size: 1.2rem;
}

.summary-divider {
    height: 1px;
    background: #e2e8f0;
    margin: 20px 0;
}

.total-row {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0;
}

.checkout-btn, .login-btn {
    width: 100%;
    padding: 16px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.checkout-btn {
    background: #2563eb;
    color: white;
}

.checkout-btn:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}

.login-btn {
    background: #f59e0b;
    color: white;
}

.login-btn:hover {
    background: #d97706;
    transform: translateY(-1px);
}

/* Animation for savings message */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.savings-row {
    animation: fadeIn 0.5s ease;
}

/* Hover effects */
.summary-row:hover {
    background: #f8fafc;
    border-radius: 8px;
    padding: 8px;
    margin: -8px -8px 7px -8px;
}

.total-row:hover {
    background: transparent;
    padding: 0;
    margin: 0;
}

@media (max-width: 768px) {
    .cart-summary {
        border-radius: 12px;
    }
    
    .summary-content {
        padding: 20px;
    }
    
    .summary-row {
        font-size: 1rem;
    }
    
    .total-row {
        font-size: 1.2rem;
    }
}

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-success {
            background: var(--success);
            color: white;
            width: 100%;
        }

        .btn-success:hover {
            background: #15803d;
        }

        .btn-warning {
            background: var(--warning);
            color: white;
            width: 100%;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .empty-cart p {
            color: var(--gray-600);
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        /* Recommended Products Section */
        .recommended-section {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid var(--gray-200);
        }

        .section-title {
            font-size: 1.5rem;
            color: var(--gray-800);
            margin-bottom: 25px;
            text-align: center;
        }

        .product-slider {
            position: relative;
            padding: 0 40px;
            margin: 0 -20px;
        }

        .slider-container {
            overflow: hidden;
        }

        .slider-track {
            display: flex;
            gap: 20px;
            padding: 20px 0;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .slider-track::-webkit-scrollbar {
            display: none;
        }

        .product-card {
            flex: 0 0 250px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .product-image {
            width: 100%;
            height: 180px;
            object-fit: contain;
            background: var(--gray-100);
            padding: 15px;
        }

        .product-content {
            padding: 15px;
        }

        .product-title {
            font-size: 1rem;
            color: var(--gray-800);
            margin-bottom: 8px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .product-price {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 12px;
        }

        .card-buttons {
            display: flex;
            gap: 8px;
            
        }

        .card-btn {
            flex: 1;
            padding: 8px;
            border-radius: 6px;
            font-size: 0.9rem;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            
        }

        .btn-view {
            background: var(--primary);
            color: white;
        }

        .btn-view:hover {
            background: var(--gray-200);
            color:var(--primary);
        }

        .btn-add {
            background: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-add:hover {
            background: var(--secondary);
        }

        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: var(--gray-800);
            transition: all 0.3s ease;
            z-index: 1;
        }

        .slider-btn:hover {
            background: var(--gray-100);
        }

        .prev-btn { left: 0; }
        .next-btn { right: 0; }

        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
                text-align: center;
                padding: 15px;
            }

            .cart-item-image {
                width: 100px;
                height: 100px;
            }

            .quantity-controls {
                margin: 15px 0;
            }

            .subtotal {
                text-align: center;
            }

            .product-card {
                flex: 0 0 200px;
            }
        }
    </style>
</head>
<body>
    <main class="cart-container">
        <div class="page-title">
            <h1>Shopping Cart</h1>
            <?php if (!empty($cart_items)): ?>
                <button class="btn btn-danger" onclick="clearCart()">
                    Clear Cart
                </button>
            <?php endif; ?>
        </div>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <p>Your cart is empty</p>
                <a href="products.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item" id="item-<?php echo htmlspecialchars($item['product']['id']); ?>">
                        <img 
                            src="assets/uploads/<?php echo htmlspecialchars($item['product']['image']); ?>" 
                            alt="<?php echo htmlspecialchars($item['product']['name']); ?>" 
                            class="cart-item-image"
                            onerror="this.src='assets/images/default-placeholder.png';"
                        >
                        
                        <div class="cart-item-info">
                            <h3><?php echo htmlspecialchars($item['product']['name']); ?></h3>
                            <p class="item-price">Rs. <?php echo number_format($item['product']['price'], 2); ?></p>
                        </div>
            <div class="quantity-controls">
                            <button class="quantity-btn" data-product-id="<?php echo htmlspecialchars($item['product']['id']); ?>" data-change="-1">−</button>
                            <span id="quantity-<?php echo htmlspecialchars($item['product']['id']); ?>">
                                <?php echo htmlspecialchars($item['quantity']); ?>
                            </span>
                            <button class="quantity-btn" data-product-id="<?php echo htmlspecialchars($item['product']['id']); ?>" data-change="1">+</button>
                        </div>

                        <div class="subtotal" id="subtotal-<?php echo htmlspecialchars($item['product']['id']); ?>">
                            Rs. <?php echo number_format($item['subtotal'], 2); ?>
                        </div>


                        <button class="remove-btn" onclick="removeItem(<?php echo $item['product']['id']; ?>)">×</button>
                    </div>
                <?php endforeach; ?>
            </div>

                        <div class="cart-summary">  
                <div class="summary-header">
                    <h3>Order Summary</h3>
                </div>
                
                <div class="summary-content">
                    <div class="summary-row">
                        <span>Items (<?php echo count($cart_items); ?>)</span>
                        <span id="subtotal">Rs. <?php echo number_format($total, 2); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <div class="delivery-info">
                            <span>Delivery Charges</span>
                            <span class="free-tag">FREE</span>
                        </div>
                        <span id="delivery" class="strikethrough">Rs. <?php echo number_format($delivery_charge, 2); ?></span>
                    </div>
                    
                    <div class="savings-row">
                        <span class="savings-icon">🎉</span>
                        <span>You saved Rs. <?php echo number_format($delivery_charge, 2); ?> on delivery!</span>
                    </div>
                    
                    <div class="summary-divider"></div>
                    
                    <div class="summary-row total-row">
                        <span>Total Amount</span>
                        <span id="total">Rs. <?php echo number_format($grand_total, 2); ?></span>
                    </div>
                </div>
                
                <?php if (isset($_SESSION['customer_id'])): ?>
                    <button onclick="window.location.href='checkout.php'" class="checkout-btn">
                        <span>Proceed to Checkout</span>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                <?php else: ?>
                    <button onclick="window.location.href='login.php?redirect=checkout.php'" class="login-btn">
                        <span>Login to Checkout</span>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/>
                        </svg>
                    </button>
                <?php endif; ?>
            </div>

        <?php endif; ?>

        <?php if (!empty($recommended_products)): ?>
            <div class="recommended-section">
                <h2 class="section-title">You May Also Like</h2>
                <div class="product-slider">
                    <button class="slider-btn prev-btn" onclick="slideProducts(-1)">❮</button>
                    <div class="slider-container">
                        <div class="slider-track">
                            <?php foreach($recommended_products as $product): ?>
                                <div class="product-card">
                                    <img src="assets/uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         class="product-image">
                                    <div class="product-content">
                                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <p class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></p>
                                        <div class="card-buttons">
                                            <a href="product-details.php?id=<?php echo $product['id']; ?>" 
                                               class="card-btn btn-view">View</a>
                                            
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button class="slider-btn next-btn" onclick="slideProducts(1)">❯</button>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function () {
    $('.quantity-btn').click(function () {
        const productId = $(this).data('product-id');
        const change = parseInt($(this).data('change'));
        const quantityElement = $(`#quantity-${productId}`);
        const currentQuantity = parseInt(quantityElement.text());

        if (currentQuantity + change < 1) return;

        $.ajax({
            url: 'ajax/update_cart.php',
            type: 'POST',
            data: {
                product_id: productId,
                quantity: currentQuantity + change
            },
            success: function (response) {
                const data = JSON.parse(response);
                if (data.success) {
                    // Update quantity and subtotal dynamically
                    quantityElement.text(data.new_quantity);
                    $(`#subtotal-${productId}`).text(`Rs. ${data.new_subtotal.toFixed(2)}`);
                    $('#subtotal').text(`Rs. ${data.cart_subtotal.toFixed(2)}`);
                    $('#total').text(`Rs. ${data.cart_total.toFixed(2)}`);
                } else {
                    alert(data.message);
                }
            }
        });
    });


        // Remove Item Logic
        $('.remove-btn').click(function () {
            const productId = $(this).data('product-id');

            $.ajax({
                url: 'ajax/remove_from_cart.php',
                type: 'POST',
                data: { product_id: productId },
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $(`#item-${productId}`).remove();
                        $('#subtotal').text(`Rs. ${data.cart_subtotal.toFixed(2)}`);
                        $('#total').text(`Rs. ${data.cart_total.toFixed(2)}`);
                    } else {
                        alert(data.message);
                    }
                }
            });
        });

        // Clear Cart Logic
        window.clearCart = function () {
            if (confirm('Are you sure you want to clear your cart?')) {
                $.ajax({
                    url: 'ajax/clear_cart.php',
                    type: 'POST',
                    success: function (response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    }
                });
            }
        };
    });
</script>

</body>
</html>