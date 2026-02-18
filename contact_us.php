<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

$page_title = "Contact Us";
include 'includes/header.php';

// Check if this is a product inquiry
$product_id = isset($_GET['product']) ? (int)$_GET['product'] : 0;
$product = null;

// If product ID is provided, fetch product details
if ($product_id) {
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p 
                               LEFT JOIN categories c ON p.category_id = c.id 
                               WHERE p.id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching product: " . $e->getMessage());
    }
}

// Fetch contact settings
try {
    $stmt = $pdo->query("SELECT * FROM contact_settings ORDER BY id DESC LIMIT 1");
    $contact_settings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Error fetching contact settings: " . $e->getMessage());
    $contact_settings = [];
}

// Get any form errors
$formErrors = $_SESSION['formErrors'] ?? [];
$formData = $_SESSION['formData'] ?? [];

// Clear the session data
unset($_SESSION['formErrors']);
unset($_SESSION['formData']);
?>

<main class="contact-page">
    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="container">
            <div class="hero-content">
                <h1>Get in Touch</h1>
                <p>We're here to help with any questions or inquiries you might have.</p>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="contact-container">
            <!-- Contact Info Cards -->
            <section class="contact-info">
                <div class="info-grid">
                    <a href="https://maps.google.com/?q=<?php echo urlencode($contact_settings['address'] ?? 'Swoyambhu-15, Kathmandu'); ?>" target="_blank" class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <h3>Our Location</h3>
                            <p><?php echo htmlspecialchars($contact_settings['address'] ?? 'Swoyambhu-15, Kathmandu'); ?></p>
                        </div>
                        <div class="info-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    
                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $contact_settings['phone'] ?? '+977 9803473938'); ?>" class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="info-content">
                            <h3>Call Us</h3>
                            <p><?php echo htmlspecialchars($contact_settings['phone'] ?? '+977 9803473938'); ?></p>
                        </div>
                        <div class="info-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    
                    <a href="mailto:<?php echo htmlspecialchars($contact_settings['email'] ?? 'kritishagautam@gmail.com'); ?>" class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h3>Email Us</h3>
                            <p><?php echo htmlspecialchars($contact_settings['email'] ?? 'kritishagautam@gmail.com'); ?></p>
                        </div>
                        <div class="info-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    
                    <?php if (!empty($contact_settings['working_hours'] ?? '')): ?>
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="info-content">
                            <h3>Working Hours</h3>
                            <p><?php echo htmlspecialchars($contact_settings['working_hours']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Contact Form Section -->
            <section class="contact-form-section">
                <div class="form-card">
                    <?php if ($product): ?>
                        <div class="form-header">
                            <h2>Product Inquiry</h2>
                            <p>Ask us about this specific product</p>
                        </div>
                        
                        <div class="product-inquiry-box">
                            <img src="assets/uploads/<?php echo htmlspecialchars($product['image'] ?? 'default.jpg'); ?>" 
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                class="product-inquiry-image"
                                onerror="this.src='assets/images/placeholder.jpg'">
                            <div class="product-inquiry-details">
                                <div class="product-inquiry-title"><?php echo htmlspecialchars($product['name']); ?></div>
                                <div class="product-inquiry-category">
                                    <span class="category-badge">
                                        <i class="fas fa-tag"></i> <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                                    </span>
                                </div>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="view-product-link">
                                    <i class="fas fa-external-link-alt"></i> View Product Details
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="form-header">
                            <h2>Send us a Message</h2>
                            <p>Fill out the form below and we'll get back to you as soon as possible</p>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['successMessage'])): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <div class="alert-content">
                                <?php 
                                    echo $_SESSION['successMessage'];
                                    unset($_SESSION['successMessage']);
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['errorMessage'])): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <div class="alert-content">
                                <?php 
                                    echo $_SESSION['errorMessage'];
                                    unset($_SESSION['errorMessage']);
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="process_contact.php" id="contactForm">
                        <?php if ($product): ?>
                            <input type="hidden" name="inquiry_type" value="product">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php endif; ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name <span class="required">*</span></label>
                                <div class="input-group">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>" placeholder="Your full name" required>
                                </div>
                                <?php if (isset($formErrors['name'])): ?>
                                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($formErrors['name']); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address <span class="required">*</span></label>
                                <div class="input-group">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" placeholder="Your email address" required>
                                </div>
                                <?php if (isset($formErrors['email'])): ?>
                                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($formErrors['email']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number <span class="optional">(Optional)</span></label>
                                <div class="input-group">
                                    <i class="fas fa-phone"></i>
                                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>" placeholder="Your phone number">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Subject <span class="optional">(Optional)</span></label>
                                <div class="input-group">
                                    <i class="fas fa-heading"></i>
                                    <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($formData['subject'] ?? ($product ? 'Inquiry about ' . $product['name'] : '')); ?>" placeholder="Message subject">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Your Message <span class="required">*</span></label>
                            <div class="input-group">
                                <i class="fas fa-comment-alt"></i>
                                <textarea id="message" name="message" rows="5" placeholder="Write your message here..." required><?php echo htmlspecialchars($formData['message'] ?? ($product ? 'I am interested in getting more information about ' . $product['name'] . '. Please provide pricing details and availability.' : '')); ?></textarea>
                            </div>
                            <?php if (isset($formErrors['message'])): ?>
                                <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($formErrors['message']); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="newsletter" name="newsletter" checked>
                            <label for="newsletter">Subscribe to our newsletter to receive updates and special offers</label>
                        </div>
                        
                        <button type="submit" class="btn-submit">
                            <span><?php echo $product ? 'Send Inquiry' : 'Send Message'; ?></span>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <!-- Map Section -->
    <?php if (!empty($contact_settings['google_maps_embed'] ?? '')): ?>
    <section class="map-section">
        <div class="container">
            <div class="section-header">
                <h2>Find Us</h2>
                <p>Visit our office for a face-to-face consultation</p>
            </div>
            <div class="map-container">
                <?php echo $contact_settings['google_maps_embed']; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<style>
    :root {
        --primary-color: #2563eb;
        --primary-light: #e0f2fe;
        --primary-dark: #1d4ed8;
        --secondary-color: #f0f7ff;
        --success-color: #10b981;
        --error-color: #ef4444;
        --text-dark: #1e293b;
        --text-medium: #4b5563;
        --text-light: #6b7280;
        --border-color: #e5e7eb;
        --border-radius: 10px;
        --box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .contact-page {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: var(--text-dark);
        line-height: 1.6;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Hero Section */
    .contact-hero {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        padding: 80px 0;
        text-align: center;
        margin-bottom: 40px;
    }

    .hero-content h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .hero-content p {
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
        opacity: 0.9;
    }

    /* Contact Container */
    .contact-container {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 40px;
        margin-bottom: 60px;
    }

    /* Contact Info Cards */
    .info-grid {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .info-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s ease;
        text-decoration: none;
        color: var(--text-dark);
        position: relative;
        overflow: hidden;
    }

    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--box-shadow);
    }

    a.info-card:hover {
        background-color: var(--secondary-color);
    }

    a.info-card:hover .info-arrow {
        opacity: 1;
        right: 24px;
    }

    .info-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--primary-light);
        color: var(--primary-color);
        flex-shrink: 0;
    }

    .info-icon i {
        font-size: 22px;
    }

    .info-content {
        flex-grow: 1;
    }

    .info-content h3 {
        font-size: 18px;
        margin-bottom: 4px;
        font-weight: 600;
    }

    .info-content p {
        color: var(--text-medium);
        font-size: 15px;
        margin: 0;
    }

    .info-arrow {
        color: var(--primary-color);
        opacity: 0;
        position: absolute;
        right: -20px;
        transition: all 0.3s ease;
    }

    /* Form Section */
    .form-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 30px;
    }

    .form-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .form-header h2 {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .form-header p {
        color: var(--text-medium);
        font-size: 16px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-dark);
        font-size: 15px;
    }

    .required {
        color: var(--error-color);
    }

    .optional {
        color: var(--text-light);
        font-size: 13px;
        font-weight: normal;
    }

    .input-group {
        position: relative;
    }

    .input-group i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-light);
    }

    .input-group input,
    .input-group textarea {
        width: 100%;
        padding: 12px 16px 12px 45px;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .input-group textarea {
        padding-top: 16px;
    }

    .input-group textarea + i {
        top: 20px;
        transform: none;
    }

    .input-group input:focus,
    .input-group textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .checkbox-group {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-top: 3px;
    }

    .checkbox-group label {
        margin-bottom: 0;
        font-weight: normal;
        color: var(--text-medium);
        font-size: 14px;
    }

    .error-message {
        color: var(--error-color);
        font-size: 13px;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .alert {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        border-radius: var(--border-radius);
        margin-bottom: 25px;
    }

    .alert i {
        font-size: 20px;
        flex-shrink: 0;
        margin-top: 3px;
    }

    .alert-content {
        flex: 1;
    }

    .alert-success {
        background-color: rgba(16, 185, 129, 0.1);
        border-left: 4px solid var(--success-color);
        color: #065f46;
    }

    .alert-error {
        background-color: rgba(239, 68, 68, 0.1);
        border-left: 4px solid var(--error-color);
        color: #b91c1c;
    }

    .btn-submit {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 14px 24px;
        border-radius: var(--border-radius);
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
    }

    .btn-submit:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    /* Product Inquiry Styles */
    .product-inquiry-box {
        background: var(--secondary-color);
        border-radius: var(--border-radius);
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
    }

    .product-inquiry-image {
        width: 100px;
        height: 100px;
        object-fit: contain;
        border-radius: 8px;
        background: white;
        padding: 8px;
        border: 1px solid var(--border-color);
    }

    .product-inquiry-details {
        flex: 1;
    }

    .product-inquiry-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .category-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(37, 99, 235, 0.1);
        color: var(--primary-color);
        font-size: 13px;
        padding: 4px 10px;
        border-radius: 20px;
        margin-bottom: 10px;
    }

    .view-product-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--primary-color);
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        margin-top: 5px;
        transition: all 0.2s ease;
    }

    .view-product-link:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    /* Map Section */
    .map-section {
        margin-bottom: 60px;
    }

    .section-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .section-header h2 {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .section-header p {
        color: var(--text-medium);
        font-size: 16px;
    }

    .map-container {
        height: 400px;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    /* Responsive Styles */
    @media (max-width: 992px) {
        .contact-container {
            grid-template-columns: 1fr;
        }
        
        .contact-hero {
            padding: 60px 0;
        }
        
        .hero-content h1 {
            font-size: 2rem;
        }
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
        
        .product-inquiry-box {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .product-inquiry-image {
            margin-bottom: 15px;
        }
        
        .contact-hero {
            padding: 40px 0;
        }
        
        .map-container {
            height: 300px;
        }
    }

    @media (max-width: 480px) {
        .form-card {
            padding: 20px;
        }
        
        .hero-content h1 {
            font-size: 1.8rem;
        }
        
        .hero-content p {
            font-size: 1rem;
        }
        
        .btn-submit {
            padding: 12px 20px;
        }
    }
</style>

<script>
    // Form validation with feedback
    document.addEventListener('DOMContentLoaded', function() {
        const contactForm = document.getElementById('contactForm');
        
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                let isValid = true;
                const name = document.getElementById('name');
                const email = document.getElementById('email');
                const message = document.getElementById('message');
                
                // Clear previous error messages
                document.querySelectorAll('.error-message').forEach(el => {
                    el.remove();
                });
                
                // Validate name
                if (!name.value.trim()) {
                    showError(name, 'Please enter your name');
                    isValid = false;
                }
                
                // Validate email
                if (!email.value.trim()) {
                    showError(email, 'Please enter your email address');
                    isValid = false;
                } else if (!isValidEmail(email.value.trim())) {
                    showError(email, 'Please enter a valid email address');
                    isValid = false;
                }
                
                // Validate message
                if (!message.value.trim()) {
                    showError(message, 'Please enter your message');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
            
            // Helper function to display error messages
            function showError(inputElement, message) {
                const formGroup = inputElement.closest('.form-group');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
                formGroup.appendChild(errorDiv);
                
                inputElement.style.borderColor = 'var(--error-color)';
                
                // Remove error styling on input
                inputElement.addEventListener('input', function() {
                    this.style.borderColor = '';
                    const errorMessage = formGroup.querySelector('.error-message');
                    if (errorMessage) {
                        errorMessage.remove();
                    }
                }, { once: true });
            }
            
            // Email validation helper
            function isValidEmail(email) {
                const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(email).toLowerCase());
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>