<?php
// Set page title
$page_title = "About Us - SS Surgical";
$current_page = 'about';

// Include header
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="about-hero">
    <div class="container">
        <div class="hero-content">
            <span class="hero-subtitle">WHO WE ARE</span>
            <h1>About SS Surgical</h1>
            <p>Dedicated to advancing healthcare through innovative surgical instruments and exceptional quality</p>
        </div>
    </div>
    <div class="hero-shape"></div>
</section>

<!-- Secondary Navigation -->
<div class="about-secondary-nav">
    <div class="container">
        <!-- <ul>
            <li class="active"><a href="#overview">Overview</a></li>
            <li><a href="#management">Management</a></li>
            <li><a href="#process">Process</a></li>
            <li><a href="#certificates">Certificates</a></li>
            <li><a href="#present">Present</a></li>
        </ul> -->
    </div>
</div>

<!-- Overview Section -->
<section id="overview" class="about-section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">OUR STORY</span>
            <h2>Company Overview</h2>
            <div class="title-bar"></div>
            <p class="section-description">Founded in 2005, SS Surgical has established itself as a leading manufacturer and supplier of high-quality medical equipment and surgical instruments.</p>
        </div>
        
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about-content">
                    <p class="lead">We combine innovative design with precision manufacturing to create instruments that healthcare professionals can rely on.</p>
                    <p>Our state-of-the-art manufacturing facility and robust quality control processes ensure that every product we deliver meets the highest standards of performance, reliability, and safety.</p>
                    <p>Through continuous research and development, we stay at the forefront of medical innovation, constantly looking for ways to improve surgical outcomes and enhance patient care.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-image">
                    <img src="assets/image/image.png" alt="SS Surgical Facility" width="600" height="400">
                </div>
            </div>
        </div>
        
        <!-- Stats with Square Grid -->
        <div class="stats-square-grid">
            <div class="stat-square">
                <div class="stat-square-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-square-number">18+</div>
                <div class="stat-square-text">Years of Excellence</div>
            </div>
            
            <div class="stat-square">
                <div class="stat-square-icon">
                    <i class="fas fa-globe-americas"></i>
                </div>
                <div class="stat-square-number">50+</div>
                <div class="stat-square-text">Countries Served</div>
            </div>
            
            <div class="stat-square">
                <div class="stat-square-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="stat-square-number">1,000+</div>
                <div class="stat-square-text">Products</div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-6">
                <div class="mission-square">
                    <div class="mission-square-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Our Mission</h3>
                    <p>To enhance healthcare delivery by providing high-quality surgical instruments and equipment that enable medical professionals to deliver the best possible care to their patients.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mission-square">
                    <div class="mission-square-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Our Vision</h3>
                    <p>To be the global leader in innovative medical equipment solutions, recognized for excellence in quality, reliability, and customer service in the healthcare industry.</p>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-12">
                <div class="section-header">
                    <h3>Our Core Values</h3>
                </div>
            </div>
        </div>
        
        <div class="values-square-grid">
            <div class="value-square">
                <div class="value-square-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h4>Quality</h4>
                <p>Unwavering commitment to excellence in all our products and services</p>
            </div>
            <div class="value-square">
                <div class="value-square-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h4>Innovation</h4>
                <p>Constantly pushing boundaries to develop cutting-edge medical solutions</p>
            </div>
            <div class="value-square">
                <div class="value-square-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h4>Integrity</h4>
                <p>Conducting business with honesty, transparency, and ethical standards</p>
            </div>
            <div class="value-square">
                <div class="value-square-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h4>Customer Focus</h4>
                <p>Dedicated to understanding and meeting our clients' needs and expectations</p>
            </div>
        </div>
    </div>
</section>

<!-- Management Section -->
<section id="management" class="about-section bg-light">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">OUR TEAM</span>
            <h2>Leadership & Management</h2>
            <div class="title-bar"></div>
            <p class="section-description">Meet our experienced leadership team guiding SS Surgical to excellence</p>
        </div>
        
        <div class="team-square-grid">
            <div class="team-square">
                <div class="team-square-image">
                    <img src="assets/image/our_team/dipendra.png" alt="CEO" width="240" height="240">
                    <div class="team-social">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="team-square-content">
                    <h3>Dipendra Guragain</h3>
                    <span class="position">Chief Executive Officer</span>
                    <p>With over 25 years of experience in the medical device industry, John leads our company with strategic vision and operational excellence.</p>
                </div>
            </div>
            
            <div class="team-square">
                <div class="team-square-image">
                    <img src="assets/image/our_team/bikku.png" alt="CTO" width="240" height="240">
                    <div class="team-social">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="team-square-content">
                    <h3>Bibek Karki</h3>
                    <span class="position">Chief Technology Officer</span>
                    <p>Sarah brings innovative thinking and technical expertise to our product development, ensuring we stay at the forefront of medical technology.</p>
                </div>
            </div>
            <div class="team-square">
                <div class="team-square-image">
                    <img src="assets/image/our_team/enos.png" alt="CTO" width="240" height="240">
                    <div class="team-social">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="team-square-content">
                    <h3>Enos Maharjan</h3>
                    <span class="position">Chief Technology Officer</span>
                    <p>Sarah brings innovative thinking and technical expertise to our product development, ensuring we stay at the forefront of medical technology.</p>
                </div>
            </div>
            <div class="team-square">
                <div class="team-square-image">
                    <img src="assets/image/our_team/anil.png" alt="COO" width="240" height="240">
                    <div class="team-social">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="team-square-content">
                    <h3>Anil Singjali</h3>
                    <span class="position">Chief Operations Officer</span>
                    <p>Michael oversees our global operations, ensuring efficient production processes and maintaining our high-quality standards.</p>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <div class="philosophy-square">
                    <div class="square-icon large-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <h3>Management Philosophy</h3>
                    <p>At SS Surgical, our management approach is guided by principles of transparency, accountability, and continuous improvement. We believe in empowering our teams to innovate while maintaining our commitment to the highest standards of quality and ethics in everything we do.</p>
                    <p>Our leadership team fosters a culture of collaboration and open communication, ensuring that every team member has the opportunity to contribute to our collective success and growth.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Process Section -->
<section id="process" class="about-section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">HOW WE WORK</span>
            <h2>Our Manufacturing Process</h2>
            <div class="title-bar"></div>
            <p class="section-description">Discover how we create premium quality surgical instruments through our meticulous manufacturing process.</p>
        </div>
        
        <div class="process-square-grid">
            <div class="process-square">
                <div class="process-square-number">1</div>
                <div class="process-square-content">
                    <h3>Design & Development</h3>
                    <p>Our experienced engineers and medical consultants collaborate to design instruments that meet the specific needs of healthcare professionals.</p>
                    <div class="process-square-image">
                        <img src="assets/image/how_we_work/1.png" alt="Design Process" width="300" height="200">
                    </div>
                </div>
            </div>
            
            <div class="process-square">
                <div class="process-square-number">2</div>
                <div class="process-square-content">
                    <h3>Raw Material Selection</h3>
                    <p>We source only the highest grade surgical stainless steel and other materials from trusted suppliers.</p>
                    <div class="process-square-image">
                        <img src="assets/image/how_we_work/2.png" alt="Raw Materials" width="300" height="200">
                    </div>
                </div>
            </div>
            
            <div class="process-square">
                <div class="process-square-number">3</div>
                <div class="process-square-content">
                    <h3>Precision Manufacturing</h3>
                    <p>Our state-of-the-art facility employs both advanced CNC machinery and skilled craftsmen to create instruments with exceptional precision.</p>
                    <div class="process-square-image">
                        <img src="assets/image/how_we_work/3.png" alt="Manufacturing" width="300" height="200">
                    </div>
                </div>
            </div>
            
            <div class="process-square">
                <div class="process-square-number">4</div>
                <div class="process-square-content">
                    <h3>Quality Control</h3>
                    <p>Our comprehensive quality control system includes multiple inspection points throughout the manufacturing process.</p>
                    <div class="process-square-image">
                        <img src="assets/image/how_we_work/4.png" alt="Quality Control" width="300" height="200">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Certificates Section -->
<section id="certificates" class="about-section bg-light">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">OUR STANDARDS</span>
            <h2>Certifications & Compliance</h2>
            <div class="title-bar"></div>
            <p class="section-description">SS Surgical maintains the highest standards of quality and compliance in the medical device industry.</p>
        </div>
        
        <div class="certificates-square-grid">
            <div class="certificate-square">
                <div class="certificate-square-image">
                    <img src="assets/images/certificates/iso13485.png" alt="ISO 13485" width="120" height="120">
                </div>
                <h3>ISO 13485:2016</h3>
                <p>Medical devices — Quality management systems — Requirements for regulatory purposes</p>
                <div class="certificate-square-meta">
                    <span>Certified Since: 2010</span>
                    <a href="#" class="btn-square-view">View Certificate</a>
                </div>
            </div>
            
            <div class="certificate-square">
                <div class="certificate-square-image">
                    <img src="assets/images/certificates/ce-mark.png" alt="CE Mark" width="120" height="120">
                </div>
                <h3>CE Marking</h3>
                <p>European Conformity certification indicating compliance with EU health, safety, and environmental standards</p>
                <div class="certificate-square-meta">
                    <span>Certified Since: 2012</span>
                    <a href="#" class="btn-square-view">View Certificate</a>
                </div>
            </div>
            
            <div class="certificate-square">
                <div class="certificate-square-image">
                    <img src="assets/images/certificates/fda.png" alt="FDA Registration" width="120" height="120">
                </div>
                <h3>FDA Registration</h3>
                <p>U.S. Food and Drug Administration registration for medical device manufacturing</p>
                <div class="certificate-square-meta">
                    <span>Certified Since: 2014</span>
                    <a href="#" class="btn-square-view">View Certificate</a>
                </div>
            </div>
            
            <div class="certificate-square">
                <div class="certificate-square-image">
                    <img src="assets/images/certificates/iso9001.png" alt="ISO 9001" width="120" height="120">
                </div>
                <h3>ISO 9001:2015</h3>
                <p>Quality management systems — Requirements for consistent provision of products and services</p>
                <div class="certificate-square-meta">
                    <span>Certified Since: 2008</span>
                    <a href="#" class="btn-square-view">View Certificate</a>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <div class="compliance-square">
                    <h3>Regulatory Compliance</h3>
                    <p>We maintain strict adherence to international regulations and standards for medical devices:</p>
                    <div class="row">
                        <div class="col-lg-6">
                            <ul class="compliance-list">
                                <li><i class="fas fa-check"></i> European Medical Device Regulation (MDR)</li>
                                <li><i class="fas fa-check"></i> U.S. FDA 21 CFR Part 820</li>
                                <li><i class="fas fa-check"></i> Canadian Medical Device Regulations (CMDR)</li>
                                <li><i class="fas fa-check"></i> Japanese Pharmaceutical and Medical Device Act</li>
                            </ul>
                        </div>
                        <div class="col-lg-6">
                            <ul class="compliance-list">
                                <li><i class="fas fa-check"></i> Australian Therapeutic Goods Administration (TGA)</li>
                                <li><i class="fas fa-check"></i> Brazilian ANVISA requirements</li>
                                <li><i class="fas fa-check"></i> Russian Roszdravnadzor registration</li>
                                <li><i class="fas fa-check"></i> Global Harmonization Task Force (GHTF) guidelines</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Present Section -->
<section id="present" class="about-section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">TODAY & TOMORROW</span>
            <h2>SS Surgical Today</h2>
            <div class="title-bar"></div>
            <p class="section-description">Discover our current operations, latest achievements, and ongoing initiatives.</p>
        </div>
        
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about-content">
                    <h3>Global Presence</h3>
                    <p>Today, SS Surgical operates in over 50 countries, with regional offices in North America, Europe, Asia, and the Middle East. Our extensive distribution network ensures that healthcare professionals worldwide have access to our premium surgical instruments and equipment.</p>
                    <p>Our international team of sales representatives, product specialists, and technical support staff provides localized assistance and expertise to our diverse customer base.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="map-square">
                    <img src="assets/image/instrument/1.png" alt="Global Presence Map" width="450" height="350">
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <div class="section-header">
                    <h3>Latest Innovations</h3>
                </div>
            </div>
        </div>
        
        <div class="innovations-square-grid">
            <div class="innovation-square">
                <div class="innovation-square-image">
                    <img src="assets/image/instrument/1.png"alt="Ergonomic Instruments" width="320" height="200">
                </div>
                <div class="innovation-square-content">
                    <h4>Ergonomic Precision Instruments</h4>
                    <p>Our new line of ergonomically designed surgical instruments reduces surgeon fatigue while enhancing precision and control during delicate procedures.</p>
                    <a href="#" class="btn-square-more">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            
            <div class="innovation-square">
                <div class="innovation-square-image">
                    <img src="assets/image/instrument/1.png"  alt="Minimally Invasive Tools" width="320" height="200">
                </div>
                <div class="innovation-square-content">
                    <h4>Advanced Minimally Invasive Tools</h4>
                    <p>Our latest minimally invasive instruments feature enhanced visualization capabilities and improved maneuverability for complex laparoscopic procedures.</p>
                    <a href="#" class="btn-square-more">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            
            <div class="innovation-square">
                <div class="innovation-square-image">
                    <img src="assets/image/instrument/1.png"  alt="Antimicrobial Coatings" width="320" height="200">
                </div>
                <div class="innovation-square-content">
                    <h4>Antimicrobial Surface Technology</h4>
                    <p>Our proprietary antimicrobial coating provides an additional layer of protection against pathogens, enhancing patient safety in surgical environments.</p>
                    <a href="#" class="btn-square-more">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-lg-12">
                <div class="future-square">
                    <h3>Looking to the Future</h3>
                    <p>As we continue to grow and evolve, SS Surgical remains focused on our core mission of enhancing healthcare delivery through innovative, high-quality medical devices.</p>
                    <div class="roadmap-squares">
                        <div class="roadmap-square">
                            <div class="roadmap-square-year">2023-2024</div>
                            <div class="roadmap-square-content">
                                <h4>Expansion of Product Portfolio</h4>
                                <p>Introducing new specialized instrument lines for emerging surgical techniques.</p>
                            </div>
                        </div>
                        <div class="roadmap-square">
                            <div class="roadmap-square-year">2024-2025</div>
                            <div class="roadmap-square-content">
                                <h4>Enhanced Digital Integration</h4>
                                <p>Developing smart surgical instruments with integrated sensors and digital connectivity.</p>
                            </div>
                        </div>
                        <div class="roadmap-square">
                            <div class="roadmap-square-year">2025-2026</div>
                            <div class="roadmap-square-content">
                                <h4>Global Manufacturing Expansion</h4>
                                <p>Opening new production facilities to serve growing markets more efficiently.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Experience the SS Surgical Difference</h2>
            <p>Join the thousands of healthcare professionals worldwide who trust our premium quality surgical instruments and equipment.</p>
            <div class="cta-buttons">
                <a href="products.php" class="btn btn-primary">Explore Our Products</a>
                <a href="contact.php" class="btn btn-outline">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<style>
/* General Styles */
:root {
    --primary: #003366;
    --primary-dark: #003366;
    --primary-light: #e6f0ff;
    --secondary: #00a896;
    --text: #333333;
    --text-light: #6c757d;
    --bg-light: #f8f9fa;
    --white: #ffffff;
    --border: #e9ecef;
    --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    --shadow-sm: 0 5px 15px rgba(0, 0, 0, 0.05);
    --shadow-lg: 0 15px 40px rgba(0, 0, 0, 0.1);
    --radius: 10px;
    --radius-lg: 15px;
    --radius-sm: 5px;
    --transition: all 0.3s ease;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    color: var(--text);
    line-height: 1.6;
    background-color: var(--white);
    padding-top: 0px; /* Adjusted for fixed navigation */
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -15px;
}

.col-lg-12 {
    width: 100%;
    padding: 0 15px;
}

.col-lg-6 {
    width: 50%;
    padding: 0 15px;
}

.mt-5 {
    margin-top: 3rem !important;
}

.bg-light {
    background-color: var(--bg-light);
}

/* Secondary Navigation - Initially Hidden, Shows on Scroll */
.about-secondary-nav {
    background-color: var(--white);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    position: fixed;
    top: 70px; /* Height of main nav */
    left: 0;
    width: 100%;
    z-index: 100;
    border-bottom: 1px solid var(--border);
    transform: translateY(-100%);
    opacity: 0;
    visibility: hidden;
    transition: transform 0.3s ease, opacity 0.3s ease, visibility 0.3s ease;
}

/* Class to show the nav when scrolled */
.about-secondary-nav.visible {
    transform: translateY(0);
    opacity: 1;
    visibility: visible;
}

.about-secondary-nav ul {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    justify-content: center;
}

.about-secondary-nav ul li {
    margin: 0;
}

.about-secondary-nav ul li a {
    display: block;
    padding: 15px 25px;
    color: var(--text);
    text-decoration: none;
    font-weight: 500;
    position: relative;
    transition: var(--transition);
}

.about-secondary-nav ul li a:hover {
    color: var(--primary);
}

.about-secondary-nav ul li.active a {
    color: var(--primary);
}

.about-secondary-nav ul li.active a:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background-color: var(--primary);
}
/* Hero Section */
.about-hero {
    padding: 80px 0;
    background-color: var(--primary);
    background-image: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: var(--white);
    position: relative;
    margin-top: 0; /* Adjusted for fixed navigation */
}

.hero-content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.hero-subtitle {
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    display: inline-block;
    padding: 5px 15px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 30px;
    margin-bottom: 20px;
}

.about-hero h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--white);
}

.about-hero p {
    font-size: 1.2rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto;
}

/* Section Styles */
.about-section {
    padding: 80px 0;
    scroll-margin-top: 200px; /* Adjusted for fixed navigation */
}

.section-header {
    text-align: center;
    margin-bottom: 50px;
}

.section-subtitle {
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 2px;
    color: var(--primary);
    text-transform: uppercase;
    display: block;
    margin-bottom: 10px;
}

.section-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 20px;
}

.section-header h3 {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 15px;
}

.title-bar {
    width: 80px;
    height: 4px;
    background-color: var(--primary);
    margin: 0 auto 25px;
    border-radius: 2px;
}

.section-description {
    max-width: 800px;
    margin: 0 auto;
    font-size: 1.1rem;
    color: var(--text-light);
}

.about-content {
    padding-right: 30px;
}

.about-content .lead {
    font-size: 1.25rem;
    font-weight: 500;
    color: var(--text);
    margin-bottom: 20px;
}

.about-content h3 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--text);
}

.about-image {
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
}

.about-image img {
    width: 100%;
    height: auto;
    display: block;
}

/* Stats Square Grid */
.stats-square-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 60px;
}

.stat-square {
    background-color: var(--white);
    border-radius: var(--radius);
    padding: 30px;
    box-shadow: var(--shadow);
    text-align: center;
    transition: var(--transition);
    aspect-ratio: 1 / 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.stat-square:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
}

.stat-square-icon {
    background-color: var(--primary-light);
    color: var(--primary);
    width: 70px;
    height: 70px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin-bottom: 20px;
    transition: var(--transition);
}

.stat-square:hover .stat-square-icon {
    background-color: var(--primary);
    color: var(--white);
    transform: rotateY(180deg);
}

.stat-square-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--text);
    margin-bottom: 10px;
    line-height: 1;
}.stat-square-text {
    font-size: 1rem;
    color: var(--text-light);
    font-weight: 500;
}

/* Mission & Vision Squares */
.mission-square {
    background-color: var(--white);
    border-radius: var(--radius);
    padding: 40px;
    box-shadow: var(--shadow);
    height: 100%;
    text-align: center;
    transition: var(--transition);
}

.mission-square:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
}

.mission-square-icon {
    font-size: 2.5rem;
    color: var(--primary);
    margin-bottom: 20px;
}

.mission-square h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: var(--text);
}

.mission-square p {
    color: var(--text-light);
    margin-bottom: 0;
}

/* Values Square Grid */
.values-square-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 30px;
}

.value-square {
    background-color: var(--white);
    border-radius: var(--radius);
    padding: 30px;
    box-shadow: var(--shadow);
    text-align: center;
    transition: var(--transition);
    height: 100%;
}

.value-square:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
}

.value-square-icon {
    font-size: 2rem;
    color: var(--primary);
    margin-bottom: 20px;
}

.value-square h4 {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: var(--text);
}

.value-square p {
    color: var(--text-light);
    margin-bottom: 0;
}

/* Team Square Grid */
.team-square-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
}

.team-square {
    background-color: var(--white);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: var(--transition);
    height: 100%;
}

.team-square:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
}

.team-square-image {
    position: relative;
    overflow: hidden;
    aspect-ratio: 1 / 1;
}

.team-square-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.team-square:hover .team-square-image img {
    transform: scale(1.05);
}

.team-social {
    position: absolute;
    top: 20px;
    right: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.team-social a {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--white);
    color: var(--primary);
    border-radius: 50%;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}

.team-social a:hover {
    background-color: var(--primary);
    color: var(--white);
    transform: translateY(-3px);
}

.team-square-content {
    padding: 25px;
}

.team-square-content h3 {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 5px;
    color: var(--text);
}

.position {
    display: block;
    font-size: 0.9rem;
    color: var(--primary);
    font-weight: 600;
    margin-bottom: 15px;
}

/* Philosophy Square */
.philosophy-square {
    background-color: var(--white);
    border-radius: var(--radius);
    padding: 40px;
    box-shadow: var(--shadow);
    text-align: center;
}

.square-icon {
    font-size: 2rem;
    color: var(--primary);
    margin-bottom: 20px;
}

.large-icon {
    font-size: 3rem;
}

/* Process Square Grid */
.process-square-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 30px;
}

.process-square {
    background-color: var(--white);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: var(--transition);
    height: 100%;
    position: relative;
}

.process-square:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
}

.process-square-number {
    position: absolute;
    top: 20px;
    left: 20px;
    width: 40px;
    height: 40px;
    background-color: var(--primary);
    color: var(--white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
    z-index: 1;
}

.process-square-content {
    padding: 30px;
    padding-top: 70px;
}

.process-square-content h3 {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: var(--text);
}

.process-square-image {
    margin-top: 20px;
    border-radius: var(--radius-sm);
    overflow: hidden;
}

.process-square-image img {
    width: 100%;
    height: auto;
    display: block;
}

/* Certificates Square Grid */
.certificates-square-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 30px;
    margin-top: 40px;
}

.certificate-square {
    background-color: var(--white);
    border-radius: var(--radius);
    padding: 30px;
    box-shadow: var(--shadow);
    transition: var(--transition);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.certificate-square:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
}

.certificate-square-image {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 120px;
    margin-bottom: 20px;
}

.certificate-square-image img {
    max-height: 100%;
    max-width: 100%;
    object-fit: contain;
}

.certificate-square h3 {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: var(--text);
}

.certificate-square p {
    flex-grow: 1;
    margin-bottom: 20px;
    color: var(--text-light);
}

.certificate-square-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid var(--border);
    padding-top: 15px;
}

.certificate-square-meta span {
    font-size: 0.85rem;
    color: var(--primary);
    font-weight: 600;
}

.btn-square-view {
    display: inline-block;
    padding: 8px 15px;
    background-color: var(--primary-light);
    color: var(--primary);
    border-radius: var(--radius-sm);
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
}

.btn-square-view:hover {
    background-color: var(--primary);
    color: var(--white);
}

/* Compliance Square */
.compliance-square {
    background-color: var(--white);
    border-radius: var(--radius);
    padding: 40px;
    box-shadow: var(--shadow);
}

.compliance-square h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--text);
    text-align: center;
}

.compliance-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.compliance-list li {
    padding: 10px 0 10px 30px;
    position: relative;
    font-weight: 500;
}

.compliance-list li i {
    position: absolute;
    left: 0;
    top: 12px;
    color: var(--primary);
}

/* Map Square */
.map-square {
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    height: 100%;
}

.map-square img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Innovations Square Grid */
.innovations-square-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
}

.innovation-square {
    background-color: var(--white);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: var(--transition);
    height: 100%;
}

.innovation-square:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
}

.innovation-square-image {
    height: 200px;
    overflow: hidden;
}

.innovation-square-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.innovation-square:hover .innovation-square-image img {
    transform: scale(1.05);
}

.innovation-square-content {
    padding: 25px;
}

.innovation-square-content h4 {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: var(--text);
}

.innovation-square-content p {
    color: var(--text-light);
    margin-bottom: 20px;
}

.btn-square-more {
    display: inline-flex;
    align-items: center;
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: var(--transition);
}

.btn-square-more i {
    margin-left: 5px;
    transition: var(--transition);
}

.btn-square-more:hover i {
    transform: translateX(5px);
}

/* Future Square */
.future-square {
    background-color: var(--white);
    border-radius: var(--radius);
    padding: 40px;
    box-shadow: var(--shadow);
    text-align: center;
}

.future-square h3 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--text);
}

.future-square p {
    max-width: 800px;
    margin: 0 auto 30px;
    color: var(--text-light);
}

.roadmap-squares {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 40px;
}

.roadmap-square {
    background-color: var(--bg-light);
    border-radius: var(--radius);
    overflow: hidden;
    transition: var(--transition);
}

.roadmap-square:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-sm);
}

.roadmap-square-year {
    background-color: var(--primary);
    color: var(--white);
    padding: 15px;
    font-weight: 600;
    text-align: center;
}

.roadmap-square-content {
    padding: 20px;
}

.roadmap-square-content h4 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: var(--text);
}

.roadmap-square-content p {
    margin-bottom: 0;
    text-align: left;
}

/* Call to Action */
.cta-section {
    padding: 80px 0;
    background-color: var(--primary);
    background-image: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: var(--white);
    text-align: center;
}

.cta-content {
    max-width: 800px;
    margin: 0 auto;
}

.cta-section h2 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--white);
}

.cta-section p {
    font-size: 1.2rem;
    margin-bottom: 30px;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.btn {
    display: inline-block;
    padding: 15px 30px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: var(--transition);
    text-align: center;
}

.btn-primary {
    background-color: var(--white);
    color: var(--primary);
    border: 2px solid var(--white);
}

.btn-primary:hover {
    background-color: transparent;
    color: var(--white);
}

.btn-outline {
    background-color: transparent;
    color: var(--white);
    border: 2px solid var(--white);
}

.btn-outline:hover {
    background-color: var(--white);
    color: var(--primary);
}

/* Responsive Styles */
@media (max-width: 1199px) {
    .section-header h2 {
        font-size: 2.2rem;
    }
    
    .about-hero h1 {
        font-size: 2.5rem;
    }
    
    .stats-square-grid,
    .team-square-grid,
    .process-square-grid,
    .certificates-square-grid,
    .innovations-square-grid,
    .roadmap-squares {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
}

@media (max-width: 991px) {
    body {
        padding-top: 120px;
    }
    
    .about-secondary-nav {
        top: 120px;
    }
    
    .about-section {
        scroll-margin-top: 170px;
    }
    
    .col-lg-6 {
        width: 100%;
        margin-bottom: 30px;
    }
    
    .about-content {
        padding-right: 0;
    }
    
    .about-hero {
        padding: 60px 0;
    }
    
    .about-hero h1 {
        font-size: 2.2rem;
    }
    
    .section-header h2 {
        font-size: 2rem;
    }
    
    .about-secondary-nav ul li a {
        padding: 12px 20px;
        font-size: 0.9rem;
    }
    
    .process-square-grid,
    .certificates-square-grid {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
}

@media (max-width: 767px) {
    body {
        padding-top: 100px;
    }
    
    .about-secondary-nav {
        top: 100px;
        overflow-x: auto;
    }
    
    .about-secondary-nav ul {
        width: max-content;
        padding: 0 15px;
    }
    
    .about-secondary-nav ul li a {
        padding: 10px 15px;
        font-size: 0.85rem;
        white-space: nowrap;
    }
    
    .about-section {
        padding: 60px 0;
        scroll-margin-top: 150px;
    }
    
    .about-hero h1 {
        font-size: 1.8rem;
    }
    
    .about-hero p {
        font-size: 1rem;
    }
    
    .section-header h2 {
        font-size: 1.8rem;
    }
    
    .section-header h3 {
        font-size: 1.5rem;
    }
    
    .stats-square-grid,
    .values-square-grid,
    .team-square-grid,
    .process-square-grid,
    .certificates-square-grid,
    .innovations-square-grid,
    .roadmap-squares {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .stat-square {
        aspect-ratio: auto;
        padding: 25px;
    }
    
    .stat-square-icon,
    .mission-square-icon,
    .value-square-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .stat-square-number {
        font-size: 2rem;
    }
    
    .cta-section h2 {
        font-size: 1.8rem;
    }
    
    .cta-section p {
        font-size: 1rem;
    }
    
    .btn {
        padding: 12px 25px;
        font-size: 0.9rem;
    }
}

@media (max-width: 575px) {
    .about-hero h1 {
        font-size: 1.6rem;
    }
    
    .section-header h2 {
        font-size: 1.6rem;
    }
    
    .mission-square,
    .philosophy-square,
    .compliance-square,
    .future-square {
        padding: 25px;
    }
    
    .team-square-image {
        aspect-ratio: 16/9;
    }
    
    .certificate-square,
    .process-square-content {
        padding: 20px;
    }
    
    .process-square-content {
        padding-top: 60px;
    }
    
    .certificate-square-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}

/* Animation Classes */
.fade-in {
    animation: fadeIn 1s ease forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
<script>
    
/* JavaScript for smooth scrolling and active section highlighting */
document.addEventListener('DOMContentLoaded', function() {
    // Get secondary navigation elements
    const navLinks = document.querySelectorAll('.about-secondary-nav ul li a');
    
    // Smooth scrolling for navigation links
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links
            navLinks.forEach(navLink => {
                navLink.parentElement.classList.remove('active');
            });
            
            // Add active class to clicked link
            this.parentElement.classList.add('active');
            
            // Get target section id from href
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                // Calculate fixed header height (main nav + secondary nav)
                const mainNavHeight = 149; // Update this value based on your actual header height
                const secondaryNavHeight = document.querySelector('.about-secondary-nav').offsetHeight;
                const offset = mainNavHeight + secondaryNavHeight;
                
                // Scroll to target section with offset
                window.scrollTo({
                    top: targetSection.offsetTop - offset,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Update active link on scroll
    function highlightNavOnScroll() {
        const sections = document.querySelectorAll('.about-section');
        
        // Calculate fixed header height for offset
        const mainNavHeight = 149; // Update this value based on your actual header height
        const secondaryNavHeight = document.querySelector('.about-secondary-nav').offsetHeight;
        const offset = mainNavHeight + secondaryNavHeight + 100; // Add some extra buffer
        
        // Find the current section in view
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop - offset;
            const sectionHeight = section.offsetHeight;
            
            if (window.pageYOffset >= sectionTop && window.pageYOffset < sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });
        
        // Update active class in navigation
        navLinks.forEach(link => {
            link.parentElement.classList.remove('active');
            
            if (link.getAttribute('href') === '#' + current) {
                link.parentElement.classList.add('active');
            }
        });
    }
    
    // Call the function on scroll
    window.addEventListener('scroll', highlightNavOnScroll);
    
    // Initialize on page load
    highlightNavOnScroll();
    
    // Animate elements on scroll if intersection observer is supported
    if ('IntersectionObserver' in window) {
        const elementsToAnimate = document.querySelectorAll('.stat-square, .mission-square, .value-square, .team-square, .process-square, .certificate-square, .innovation-square');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        elementsToAnimate.forEach(element => {
            observer.observe(element);
        });
    }
});
// Add this to your existing JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const secondaryNav = document.querySelector('.about-secondary-nav');
    
    // Function to toggle nav visibility based on scroll position
    function toggleNavVisibility() {
        if (window.scrollY > 15) {
            secondaryNav.classList.add('visible');
        } else {
            secondaryNav.classList.remove('visible');
        }
    }
    
    // Listen for scroll events
    window.addEventListener('scroll', toggleNavVisibility);
    
    // Check initial scroll position
    toggleNavVisibility();
});

</script>
<?php
// Include footer
require_once 'includes/footer.php';
?>