<?php
$page_title = 'Home - Surgical Equipment E-commerce';
$current_page = 'home';
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            background: #f1f5f9;
            color: #333;
        }

        /* Hero Section with Carousel */
        .hero-section {
            position: relative;
            height: 500px;
            width: 100%;
            overflow: hidden;
         }

        .hero-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-slide.active {
            opacity: 1;
        }

        .slide-1 {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('assets/bg.png');
        }
        
        .slide-2 {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('assets/bg1.jpg');
        }
        
        .slide-3 {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('assets/bg2.jpg');
        }

        .hero-content {
            max-width: 800px;
            padding: 20px;
            text-align: center;
            color: white;
            z-index: 2;
        }

        .hero-content h1 {
            font-size: 3rem;
            margin-bottom: 15px;
            animation: fadeUp 0.8s ease-out;
        }

        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            animation: fadeUp 1s ease-out;
        }

        .hero-btn {
            display: inline-block;
            background: #003366;
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            animation: fadeUp 1.2s ease-out;
        }

        .hero-btn:hover {
            background: #003366;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        /* Carousel Controls */
        .carousel-controls {
            position: absolute;
            bottom: 30px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 10px;
            z-index: 10;
        }

        .carousel-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .carousel-dot.active {
            background: #003366;
            transform: scale(1.2);
        }

        .carousel-arrows {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 30px;
            transform: translateY(-50%);
            z-index: 10;
        }

        .arrow {
            width: 50px;
            height: 50px;
            background: rgba(0,0,0,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .arrow:hover {
            background: #003366;
        }

        /* Animation */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section {
            background: #fff;
            margin-bottom: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 25px;
            position: relative;
            overflow: hidden;
        }

        .section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #003366;
        }

        h2 {
            color: #1e293b;
            font-size: 28px;
            margin-bottom: 25px;
            position: relative;
            display: inline-block;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 60px;
            height: 3px;
            background: #003366;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .card {
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            min-height: 380px;
            position: relative;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .card img {
            width: 100%;
            height: 220px;
            object-fit: contain;
            background: #f8fafc;
            padding: 15px;
            transition: transform 0.3s ease;
        }

        .card:hover img {
            transform: scale(1.08);
        }

        .hot-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ef4444;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }

        .card-content {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
        }

        .card-content h3 {
            font-size: 18px;
            color: #334155;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .price {
            color: #003366;
            font-size: 22px;
            font-weight: 600;
            margin: 15px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .price::before {
            content: '';
            width: 25px;
            height: 3px;
            background: #003366;
        }

        .add-to-cart-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 14px;
            margin-top: auto;
            background: #003366;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            gap: 10px;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);
        }

        .add-to-cart-btn:hover {
            background: #003366;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(59, 130, 246, 0.3);
        }

        .add-to-cart-btn i {
            transition: transform 0.3s ease;
        }

        .add-to-cart-btn:hover i {
            transform: translateX(5px);
        }

        /* Features Section */
        .features {
            padding: 70px 0;
            background: #fff;
            border-radius: 12px;
            margin: 50px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 40px;
            text-align: center;
        }

        .feature-card {
            padding: 30px 20px;
            transition: transform 0.3s ease;
            border-radius: 10px;
            background: #f8fafc;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            background: white;
        }

        .feature-card img {
            width: the image size in your assets folder;
            height: 70px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .feature-card:hover img {
            transform: scale(1.1);
        }

        .feature-card h4 {
            color: #1e293b;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .feature-card p {
            color: #64748b;
            font-size: 0.95rem;
        }

        /* Testimonials Section */
        .testimonials {
            background: #f8fafc;
            padding: 80px 0;
            margin: 50px 0;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
        }

        .testimonials::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('assets/pattern.svg');
            opacity: 0.05;
            z-index: 0;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 50px;
            position: relative;
            z-index: 1;
        }

        .testimonial-card {
            background: #fff;
            padding: 35px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            position: relative;
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }

        .quote {
            font-size: 5rem;
            position: absolute;
            top: -15px;
            left: 20px;
            color: #003366;
            opacity: 0.1;
        }

        .testimonial-card p {
            margin-bottom: 25px;
            color: #475569;
            font-style: italic;
            font-size: 1.05rem;
            line-height: 1.7;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .testimonial-author img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #003366;
            padding: 3px;
        }

        .testimonial-author h4 {
            color: #1e293b;
            margin: 0;
            font-size: 1.1rem;
        }

        .testimonial-author span {
            color: #64748b;
            font-size: 0.9rem;
        }

        /* News Section */
        .news-section {
            padding: 80px 0;
            background: #fff;
            border-radius: 12px;
            margin: 50px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .news-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .news-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }

        .news-card .news-img {
            height: 200px;
            width: 100%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .news-card img {
            width: 80px;
            height: 80px;
            transition: transform 0.3s ease;
        }

        .news-card:hover img {
            transform: scale(1.1);
        }

        .news-content {
            padding: 25px;
        }

        .date {
            color: #64748b;
            font-size: 0.9rem;
            display: inline-block;
            padding: 5px 12px;
            background: #f1f5f9;
            border-radius: 20px;
            margin-bottom: 10px;
        }

        .news-content h3 {
            color: #1e293b;
            margin: 10px 0;
            font-size: 1.3rem;
            line-height: 1.4;
        }

        .news-content p {
            color: #475569;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .read-more {
            color: #003366;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 0;
            position: relative;
        }

        .read-more::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: #003366;
            transition: width 0.3s ease;
        }

        .read-more:hover::after {
            width: 100%;
        }

        .read-more i {
            transition: transform 0.3s ease;
        }

        .read-more:hover i {
            transform: translateX(5px);
        }

        /* Brands Section */
        .brands {
            padding: 70px 0;
            background: #f8fafc;
            border-radius: 12px;
            margin: 50px 0;
        }

        .brands h2 {
            text-align: center;
            margin-bottom: 50px;
        }

        .brands h2::after {
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
        }

        .brands-slider {
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            gap: 50px;
        }

        .brands-slider img {
            height: 50px;
            object-fit: contain;
            filter: grayscale(100%);
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .brands-slider img:hover {
            filter: grayscale(0);
            opacity: 1;
            transform: scale(1.1);
        }
        
        /* Rolling Advertisements and Offers Section */
        .rolling-ads {
            background: #003366;
            color: white;
            padding: 15px 0;
            margin: 0 0 50px 0;
            overflow: hidden;
            position: relative;
        }
        
        .ads-wrapper {
            overflow: hidden;
            width: 100%;
            height: 50px;
            position: relative;
        }
        
        .ads-track {
            display: flex;
            position: absolute;
            white-space: nowrap;
            will-change: transform;
            animation: rolling 30s linear infinite;
            width: fit-content;
        }
        
        .ad-item {
            display: flex;
            align-items: center;
            padding: 0 40px;
            font-weight: 500;
            font-size: 1.1rem;
        }
        
        .ad-item i {
            margin-right: 15px;
            font-size: 1.3rem;
        }
        
        @keyframes rolling {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-100%);
            }
        }
        

        @media (max-width: 768px) {
            .hero-section {
                height: 450px;
            }

            .hero-content h1 {
                font-size: 2.2rem;
            }

            .hero-content p {
                font-size: 1.1rem;
            }

            .container {
                padding: 0 15px;
            }
            
            .section {
                padding: 20px;
            }

            .cards {
                grid-template-columns: 1fr;
            }
            
            .testimonials-grid,
            .news-grid {
                grid-template-columns: 1fr;
            }
            
            .brands-slider {
                gap: 30px;
            }
            
            .brands-slider img {
                height: 40px;
            }
            
            .arrow {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    
    <!-- Hero Section with Carousel -->
    <div class="hero-section">
        <div class="hero-slide slide-1 active">
            <div class="hero-content">
                <h1>Find Your Perfect Surgical Equipment</h1>
                <p>Advanced technology for precise surgical procedures</p>
                <a href="./products.php" class="hero-btn">Explore Products</a>
            </div>
        </div>
        <div class="hero-slide slide-2">
            <div class="hero-content">
                <h1>State-of-the-Art Medical Solutions</h1>
                <p>Cutting-edge tools for modern healthcare professionals</p>
                <a href="./products.php" class="hero-btn">View Collection</a>
            </div>
        </div>
        <div class="hero-slide slide-3">
            <div class="hero-content">
                <h1>Precision Instruments for Surgeons</h1>
                <p>Quality equipment for every surgical specialty</p>
                <a href="./products.php" class="hero-btn">Shop Now</a>
            </div>
        </div>
        
        <!-- Carousel Controls -->
        <div class="carousel-arrows">
            <div class="arrow prev"><i class="fas fa-chevron-left"></i></div>
            <div class="arrow next"><i class="fas fa-chevron-right"></i></div>
        </div>
        
        <div class="carousel-controls">
            <div class="carousel-dot active" data-slide="0"></div>
            <div class="carousel-dot" data-slide="1"></div>
            <div class="carousel-dot" data-slide="2"></div>
        </div>
    </div>

    <!-- Rolling Advertisements and Offers Section -->
    <div class="rolling-ads">
        <div class="container">
            <div class="ads-wrapper">
                <div class="ads-track">
                    <div class="ad-item">
                        <i class="fas fa-tags"></i>
                        <span>SPECIAL OFFER: 15% OFF on all laparoscopic equipment until June 30</span>
                    </div>
                    <div class="ad-item">
                        <i class="fas fa-truck"></i>
                        <span>FREE SHIPPING on orders over Rs. 50,000</span>
                    </div>
                    <div class="ad-item">
                        <i class="fas fa-certificate"></i>
                        <span>NEW ARRIVALS: Advanced imaging systems now in stock</span>
                    </div>
                    <div class="ad-item">
                        <i class="fas fa-gift"></i>
                        <span>BUY 3 GET 1 FREE on selected surgical instruments</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- News Section (Modernized) -->
    <div class="news-section">
        <div class="container">
            <h2>Latest News & Updates</h2>
            <div class="news-grid">
                <div class="news-card">
                    <div class="news-img">
                        <img src="assets/icons/bag2-new.svg" alt="New Arrival">
                    </div>
                    <div class="news-content">
                        <span class="date">June 15, 2024</span>
                        <h3>New Surgical Equipment Arrival</h3>
                        <p>Latest advanced surgical instruments with improved precision now available.</p>
                        <a href="#" class="read-more">Read more <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="news-card">
                    <div class="news-img">
                        <img src="assets/icons/news.svg" alt="News">
                    </div>
                    <div class="news-content">
                        <span class="date">June 10, 2024</span>
                        <h3>Hospital Special Offer</h3>
                        <p>Special discounts for hospitals and medical centers on bulk orders.</p>
                        <a href="#" class="read-more">Read more <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Brands Section (Modernized) -->
    <div class="brands">
        <div class="container">
            <h2>Authorized Distributor</h2>
            <div class="brands-slider"> 
                <img src="assets/icons/mjWebLogo.png" alt="Medical Systems" width="200">
                <img src="assets/icons/MERICON.png" alt="Mericon" width="200">
                <img src="assets/icons/Northern-Surgical-Co (1).png" alt="Northern Surgical" width="200">
                <img src="assets/icons/KONTOUR.png" alt="Kontour" width="200">
            </div>
        </div>
    </div>

    <!-- Testimonials Section (Modernized) -->
    <div class="testimonials">
        <div class="container">
            <h2>What Our Customers Say</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="quote">"</div>
                    <p>Best surgical equipment supplier in the region. Great quality products and excellent customer service. Really happy with our partnership!</p>
                    <div class="testimonial-author">
                        <img src="assets/image.png" alt="User">
                        <div>
                            <h4>Dr.Dipendra Guragain</h4>
                            <span>Chief Surgeon</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="quote">"</div>
                    <p>Their technical expertise is impressive. They helped us choose the perfect equipment for our hospital's specific needs.</p>
                    <div class="testimonial-author">
                        <img src="assets/anil.png" alt="User">
                        <div>
                            <h4>Dr.Anil Singjali</h4>
                            <span>Hospital Director</span>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="quote">"</div>
                    <p>Quick delivery and genuine products with excellent after-sales support. Will definitely recommend to other healthcare facilities!</p>
                    <div class="testimonial-author">
                        <img src="assets/bikku.png" alt="User">
                        <div>
                            <h4>Dr. Bijaya Karki</h4>
                            <span>Medical Procurement</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section (Modernized) -->
    <div class="features">
        <div class="container">
            <div class="features-grid">
                <div class="feature-card">
                    <img src="assets/icons/free-delivery-free.svg" alt="free-delivery">
                    <h4>Free Shipping</h4>
                    <p>On orders above Rs. 50,000</p>
                </div>
                <div class="feature-card">
                    <img src="assets/icons/warranty.svg" alt="Warranty">
                    <h4>2 Year Warranty</h4>
                    <p>Official warranty support</p>
                </div>
                <div class="feature-card">
                    <img src="assets/icons/24-hours.svg" alt="24/7 Support">
                    <h4>24/7 Support</h4>
                    <p>Dedicated customer service</p>
                </div>
                <div class="feature-card">
                    <img src="assets/icons/secure-payment.svg" alt="Secure Payment">
                    <h4>Secure Payment</h4>
                    <p>100% secure checkout</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Hero Carousel functionality
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.hero-slide');
            const dots = document.querySelectorAll('.carousel-dot');
            const prevBtn = document.querySelector('.prev');
            const nextBtn = document.querySelector('.next');
            let currentSlide = 0;
            let slideInterval;
            
            // Initialize automatic slideshow
            startSlideshow();
            
            // Function to start automatic slideshow
            function startSlideshow() {
                slideInterval = setInterval(nextSlide, 3000);
            }
            
            // Function to reset interval when manually changing slides
            function resetInterval() {
                clearInterval(slideInterval);
                startSlideshow();
            }
            
            // Function to show a specific slide
            function showSlide(index) {
                slides.forEach(slide => slide.classList.remove('active'));
                dots.forEach(dot => dot.classList.remove('active'));
                
                slides[index].classList.add('active');
                dots[index].classList.add('active');
                currentSlide = index;
            }
            
            // Function for next slide
            function nextSlide() {
                let nextIndex = currentSlide + 1;
                if (nextIndex >= slides.length) {
                    nextIndex = 0;
                }
                showSlide(nextIndex);
            }
            
            // Function for previous slide
            function prevSlide() {
                let prevIndex = currentSlide - 1;
                if (prevIndex < 0) {
                    prevIndex = slides.length - 1;
                }
                showSlide(prevIndex);
            }
            
            // Event listeners for dots
            dots.forEach((dot, index) => {
                dot.addEventListener('click', function() {
                    showSlide(index);
                    resetInterval();
                });
            });
            
            // Event listeners for arrow buttons
            prevBtn.addEventListener('click', function() {
                prevSlide();
                resetInterval();
            });
            
            nextBtn.addEventListener('click', function() {
                nextSlide();
                resetInterval();
            });
        });
    </script>
    <script src="assets/js/add-to-cart.js"></script>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>