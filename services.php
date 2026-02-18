<?php
// Set page title
$page_title = "Services";

// Include header from the includes directory
include_once 'includes/header.php';

// Get the service type from URL parameter
$service_type = isset($_GET['type']) ? $_GET['type'] : 'all';

// Mock data for services - in a real site, this would come from the database
$services = [
    'installation' => [
        'title' => 'Installation Services',
        'icon' => 'fa-tools',
        'description' => 'Our professional installation services ensure that your medical equipment is set up correctly, safely, and efficiently. We handle everything from delivery to final testing, minimizing disruption to your operations.',
        'features' => [
            'Expert technicians certified in medical equipment installation',
            'Comprehensive site assessment before installation',
            'Careful handling and positioning of sensitive equipment',
            'Complete system integration with existing infrastructure',
            'Thorough testing and calibration after installation',
            'Staff training on proper equipment use and care'
        ],
        'image' => 'images/services/installation.jpg'
    ],
    'maintenance' => [
        'title' => 'Maintenance Programs',
        'icon' => 'fa-calendar-check',
        'description' => 'Regular maintenance is crucial for medical equipment reliability and longevity. Our maintenance programs are designed to keep your systems in optimal condition, prevent unexpected downtime, and extend equipment lifespan.',
        'features' => [
            'Scheduled preventive maintenance visits',
            'Comprehensive system checks and performance verification',
            'Early identification of potential issues',
            'Software updates and firmware upgrades',
            'Detailed maintenance reports and documentation',
            'Priority scheduling for service calls'
        ],
        'image' => 'images/services/maintenance.jpg'
    ],
    'repairs' => [
        'title' => 'Repair Services',
        'icon' => 'fa-wrench',
        'description' => 'When equipment malfunctions occur, our rapid-response repair services minimize downtime and restore functionality. Our technicians are equipped with extensive knowledge and genuine parts to ensure quality repairs.',
        'features' => [
            'Emergency response for critical equipment',
            'Detailed diagnostics and fault identification',
            'Use of genuine replacement parts',
            'Comprehensive testing after repairs',
            'Documentation of all repair work performed',
            'Warranty on all repair services'
        ],
        'image' => 'images/services/repairs.jpg'
    ],
    'consultation' => [
        'title' => 'Consultation Services',
        'icon' => 'fa-comments',
        'description' => 'Our consultation services help healthcare facilities make informed decisions about equipment purchases, facility design, and technology upgrades. Our experts provide valuable insights for optimal healthcare infrastructure.',
        'features' => [
            'Needs assessment and requirement analysis',
            'Equipment selection guidance based on clinical needs',
            'Facility design and space planning recommendations',
            'Technology integration planning',
            'Budget optimization strategies',
            'Regulatory compliance assistance'
        ],
        'image' => 'images/services/consultation.jpg'
    ]
];

// If specific service type is requested and exists, focus on that one
$service_detail = isset($services[$service_type]) ? $services[$service_type] : null;
?>

<div class="page-container">
    <!-- Services Hero Section -->
    <div class="services-hero">
        <div class="container">
            <h1>Our Services</h1>
            <p>SS Surgical provides comprehensive support for all your medical equipment needs, from installation and maintenance to repairs and consultation.</p>
        </div>
    </div>
    
    <!-- Services Content -->
    <div class="services-content">
        <div class="container">
            <?php if ($service_type === 'all' || !$service_detail): ?>
                <!-- Service Categories Grid -->
                <div class="services-grid">
                    <?php foreach ($services as $key => $service): ?>
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas <?php echo $service['icon']; ?>"></i>
                            </div>
                            <h2><?php echo $service['title']; ?></h2>
                            <p><?php echo substr($service['description'], 0, 150); ?>...</p>
                            <a href="services.php?type=<?php echo $key; ?>" class="service-btn">Learn More</a>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Why Choose Our Services Section -->
                <div class="why-choose-us">
                    <h2>Why Choose Our Services</h2>
                    <div class="features-grid">
                        <div class="feature">
                            <div class="feature-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <h3>Expert Technicians</h3>
                            <p>Our team consists of highly trained professionals with years of experience in medical equipment.</p>
                        </div>
                        <div class="feature">
                            <div class="feature-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3>Fast Response Time</h3>
                            <p>We understand the critical nature of medical equipment and prioritize rapid response.</p>
                        </div>
                        <div class="feature">
                            <div class="feature-icon">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <h3>Quality Guarantee</h3>
                            <p>All our services come with a satisfaction guarantee and comply with industry standards.</p>
                        </div>
                        <div class="feature">
                            <div class="feature-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <h3>Nationwide Coverage</h3>
                            <p>We provide service throughout the country, ensuring support wherever you're located.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Call to Action Section -->
                <div class="service-cta">
                    <div class="cta-content">
                        <h2>Need a Custom Service Solution?</h2>
                        <p>Contact our team to discuss your specific requirements and how we can tailor our services to meet your needs.</p>
                    </div>
                    <a href="contact_us.php" class="cta-btn">Contact Us Today</a>
                </div>
            <?php else: ?>
                <!-- Service Detail Page -->
                <div class="service-detail">
                    <div class="back-link">
                        <a href="services.php"><i class="fas fa-arrow-left"></i> Back to All Services</a>
                    </div>
                    
                    <div class="service-detail-header">
                        <div class="service-detail-icon">
                            <i class="fas <?php echo $service_detail['icon']; ?>"></i>
                        </div>
                        <h1><?php echo $service_detail['title']; ?></h1>
                    </div>
                    
                    <div class="service-detail-content">
                        <div class="service-description">
                            <p><?php echo $service_detail['description']; ?></p>
                            
                            <h2>What We Offer</h2>
                            <ul class="service-features">
                                <?php foreach ($service_detail['features'] as $feature): ?>
                                    <li><i class="fas fa-check"></i> <?php echo $feature; ?></li>
                                <?php endforeach; ?>
                            </ul>
                            
                            <div class="service-action">
                                <a href="contact_us.php?service=<?php echo $service_type; ?>" class="primary-btn">Request This Service</a>
                                <a href="downloads.php?type=brochures" class="secondary-btn">Download Brochure</a>
                            </div>
                        </div>
                        
                        <div class="service-image">
                            <img src="<?php echo $service_detail['image']; ?>" alt="<?php echo $service_detail['title']; ?>" onerror="this.src='https://via.placeholder.com/600x400?text=<?php echo urlencode($service_detail['title']); ?>'">
                        </div>
                    </div>
                    
                    <!-- Related Services -->
                    <div class="related-services">
                        <h2>Related Services</h2>
                        <div class="related-services-grid">
                            <?php 
                            $count = 0;
                            foreach ($services as $key => $service): 
                                if ($key !== $service_type && $count < 3):
                                    $count++;
                            ?>
                                <div class="related-service-card">
                                    <div class="related-service-icon">
                                        <i class="fas <?php echo $service['icon']; ?>"></i>
                                    </div>
                                    <h3><?php echo $service['title']; ?></h3>
                                    <p><?php echo substr($service['description'], 0, 100); ?>...</p>
                                    <a href="services.php?type=<?php echo $key; ?>" class="related-service-link">Learn More</a>
                                </div>
                            <?php endif; endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- FAQ Section -->
                    <div class="service-faqs">
                        <h2>Frequently Asked Questions</h2>
                        <div class="faq-list">
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>How quickly can you respond to service requests?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Our standard response time is within 24 hours for routine service requests. For emergency situations, we offer priority response times, typically within 4-6 hours depending on your location. Service contracts can include guaranteed response time SLAs.</p>
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>Do you offer service contracts?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Yes, we offer flexible service contracts tailored to your facility's needs. Our contracts can include scheduled maintenance, priority repairs, parts coverage, and extended warranties. Contact our service department for a customized quote.</p>
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>Are your technicians certified?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>All our technicians are fully certified and undergo regular training to stay current with the latest technology and best practices. They have manufacturer-specific certifications for the equipment they service and comply with all relevant industry standards.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .page-container {
        padding-bottom: 60px;
    }

    .services-hero {
        background-color: #f0f7ff;
        background-image: linear-gradient(135deg, #f0f7ff 0%, #e0f2fe 100%);
        padding: 60px 0 40px;
        margin-bottom: 50px;
    }

    .services-hero h1 {
        color: #003366;
        margin-bottom: 15px;
        font-size: 2.2rem;
        font-weight: 700;
    }

    .services-hero p {
        color: #64748b;
        font-size: 1.1rem;
        max-width: 800px;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Services Grid */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
        margin-bottom: 60px;
    }

    .service-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
        padding: 30px;
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        flex-direction: column;
    }

    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .service-icon {
        font-size: 2rem;
        color: #003366;
        margin-bottom: 20px;
        width: 70px;
        height: 70px;
        background-color: #f0f7ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .service-card h2 {
        color: #1e293b;
        font-size: 1.3rem;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .service-card p {
        color: #64748b;
        margin-bottom: 25px;
        flex-grow: 1;
    }

    .service-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: #003366;
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        align-self: flex-start;
        transition: background-color 0.3s;
    }

    .service-btn:hover {
        background-color: #003366;
    }

    /* Why Choose Us Section */
    .why-choose-us {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
        padding: 40px;
        margin-bottom: 60px;
    }

    .why-choose-us h2 {
        color: #003366;
        margin-bottom: 30px;
        font-size: 1.8rem;
        text-align: center;
        font-weight: 700;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px;
    }

    .feature {
        text-align: center;
    }

    .feature-icon {
        font-size: 1.8rem;
        color: #003366;
        margin-bottom: 15px;
        background-color: #f0f7ff;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
    }

    .feature h3 {
        color: #1e293b;
        font-size: 1.2rem;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .feature p {
        color: #64748b;
    }

    /* CTA Section */
    .service-cta {
        background-color: #003366;
        border-radius: 10px;
        padding: 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 30px;
    }

    .cta-content {
        flex: 1;
        min-width: 300px;
    }

    .cta-content h2 {
        color: white;
        margin-bottom: 10px;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .cta-content p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
    }

    .cta-btn {
        background-color: white;
        color: #003366;
        padding: 12px 24px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: background-color 0.3s, transform 0.3s;
    }

    .cta-btn:hover {
        background-color: #f8fafc;
        transform: translateY(-2px);
    }

    /* Service Detail Styles */
    .back-link {
        margin-bottom: 30px;
    }

    .back-link a {
        color: #64748b;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        transition: color 0.3s;
    }

    .back-link a:hover {
        color: #003366;
    }

    .service-detail-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
    }

    .service-detail-icon {
        font-size: 2rem;
        color: #003366;
        background-color: #f0f7ff;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .service-detail-header h1 {
        color: #003366;
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }

    .service-detail-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-bottom: 50px;
    }

    .service-description p {
        color: #1e293b;
        margin-bottom: 30px;
        font-size: 1.05rem;
        line-height: 1.7;
    }

    .service-description h2 {
        color: #003366;
        margin-bottom: 20px;
        font-size: 1.4rem;
        font-weight: 600;
    }

    .service-features {
        list-style: none;
        padding: 0;
        margin: 0 0 30px 0;
    }

    .service-features li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 15px;
        color: #1e293b;
    }

    .service-features li i {
        color: #003366;
        margin-top: 5px;
    }

    .service-action {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .primary-btn, .secondary-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .primary-btn {
        background-color: #003366;
        color: white;
    }

    .primary-btn:hover {
        background-color: #003366;
        transform: translateY(-2px);
    }

    .secondary-btn {
        background-color: #f1f5f9;
        color: #64748b;
    }

    .secondary-btn:hover {
        background-color: #e2e8f0;
        color: #1e293b;
        transform: translateY(-2px);
    }

    .service-image img {
        width: 100%;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* Related Services */
    .related-services {
        margin-bottom: 50px;
    }

    .related-services h2 {
        color: #003366;
        margin-bottom: 20px;
        font-size: 1.6rem;
        font-weight: 700;
    }

    .related-services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .related-service-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
        padding: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .related-service-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .related-service-icon {
        font-size: 1.5rem;
        color: #003366;
        margin-bottom: 15px;
        width: 50px;
        height: 50px;
        background-color: #f0f7ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .related-service-card h3 {
        color: #1e293b;
        font-size: 1.1rem;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .related-service-card p {
        color: #64748b;
        margin-bottom: 15px;
        font-size: 0.95rem;
    }

    .related-service-link {
        color: #003366;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: color 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .related-service-link:hover {
        color: #003366;
    }

    .related-service-link::after {
        content: '\f054';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 0.75rem;
    }

    /* FAQ Section */
    .service-faqs {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
        padding: 40px;
    }

    .service-faqs h2 {
        color: #003366;
        margin-bottom: 30px;
        font-size: 1.6rem;
        font-weight: 700;
    }

    .faq-item {
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 15px;
    }

    .faq-question {
        padding: 15px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        user-select: none;
        color: #1e293b;
        font-weight: 600;
    }

    .faq-question i {
        transition: transform 0.3s;
    }

    .faq-item.active .faq-question i {
        transform: rotate(180deg);
    }

    .faq-answer {
        display: none;
        padding: 0 0 20px;
    }

    .faq-answer p {
        color: #64748b;
        line-height: 1.6;
    }

    .faq-item.active .faq-answer {
        display: block;
    }

    /* Responsive Styles */
    @media (max-width: 991px) {
        .service-detail-content {
            grid-template-columns: 1fr;
        }

        .service-image {
            order: -1;
            margin-bottom: 30px;
        }
    }

    @media (max-width: 768px) {
        .services-hero {
            padding: 40px 0 30px;
        }

        .service-cta {
            flex-direction: column;
            align-items: flex-start;
            padding: 30px;
        }

        .features-grid {
            grid-template-columns: 1fr 1fr;
        }

        .service-action {
            flex-direction: column;
            gap: 15px;
        }

        .primary-btn, .secondary-btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .features-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle FAQ toggles
        const faqItems = document.querySelectorAll('.faq-item');
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            
            question.addEventListener('click', () => {
                item.classList.toggle('active');
            });
        });
        
        // Make first FAQ item active by default
        if (faqItems.length > 0) {
            faqItems[0].classList.add('active');
        }
    });
</script>

<?php
// Include footer from includes directory
include_once 'includes/footer.php';
?>