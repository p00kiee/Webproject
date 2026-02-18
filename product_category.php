<?php
session_start();
$page_title = 'Surgical Equipment Categories';
$current_page = 'categories';
require_once 'includes/header.php';
require_once 'config/database.php';

// Fetch all active categories
try {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE status = 1 ORDER BY id ASC");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<style>
.categories-section {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

.section-title {
    text-align: center;
    margin-bottom: 50px;
}

.section-title h1 {
    font-size: clamp(1.8rem, 4vw, 2.5rem);
    color: #1e293b;
    margin-bottom: 15px;
}

.section-title p {
    color: #64748b;
    font-size: clamp(1rem, 2vw, 1.1rem);
    max-width: 600px;
    margin: 0 auto;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(300px, 100%), 1fr));
    gap: clamp(15px, 3vw, 30px);
}

.category-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 12px rgba(0,0,0,0.1);
}

.category-image {
    width: 100%;
    height: 300px;
    background-size: cover;
    background-position: center;
    position: relative;
    overflow: hidden;
}

.category-image::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 100%);
}

.category-content {
    padding: clamp(15px, 3vw, 25px);
    flex: 1;
    display: flex;
    flex-direction: column;
}

.category-title {
    font-size: clamp(1.2rem, 2.5vw, 1.5rem);
    color: #1e293b;
    margin-bottom: 15px;
}

.category-description {
    color: #64748b;
    margin-bottom: 20px;
    line-height: 1.6;
    flex: 1;
}

.category-features {
    border-top: 1px solid #e2e8f0;
    padding-top: 15px;
    margin-top: 15px;
}

.feature-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.feature-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #4b5563;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.feature-list li::before {
    content: "•";
    color: #2563eb;
    font-weight: bold;
}

.view-category {
    display: inline-block;
    width: 100%;
    padding: 12px 25px;
    background: #2563eb;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    margin-top: 20px;
    transition: all 0.3s ease;
    text-align: center;
}

.view-category:hover {
    background: #1e40af;
    transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .categories-section {
        margin: 20px auto;
    }
    
    .category-image {
        height: 150px;
    }
    
    .category-content {
        padding: 15px;
    }
    
    .feature-list li {
        font-size: 0.9rem;
    }
}

/* Loading Animation */
.skeleton {
    background: linear-gradient(
        90deg,
        #f0f0f0 25%,
        #e0e0e0 50%,
        #f0f0f0 75%
    );
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    to {
        background-position: -200% 0;
    }
}

/* Fade In Animation */
.fade-in {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeIn 0.6s ease forwards;
}

@keyframes fadeIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<div class="categories-section">
    <div class="section-title fade-in">
        <h1>Surgical Equipment Categories</h1>
        <p>Discover our comprehensive range of surgical instruments and equipment</p>
    </div>

    <div class="category-grid">
        <?php 
        if (!empty($categories)):
            foreach ($categories as $index => $category): 
                $features = json_decode($category['features'], true) ?? [];
        ?>
            <div class="category-card fade-in" style="animation-delay: <?php echo $index * 0.1; ?>s">
                <div class="category-image" style="background-image: url('<?php echo !empty($category['image']) ? 'assets/uploads/categories/' . htmlspecialchars($category['image']) : 'assets/bg.png'; ?>')"></div>
                <div class="category-content">
                    <h2 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h2>
                    <p class="category-description">
                        <?php echo htmlspecialchars($category['description']); ?>
                    </p>
                    <?php if (!empty($features)): ?>
                    <div class="category-features">
                        <ul class="feature-list">
                            <?php foreach ($features as $feature): ?>
                                <li><?php echo htmlspecialchars($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <a href="products?category=<?php echo $category['id']; ?>&search=&sort=default" 
                       class="view-category">
                        Explore <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                </div>
            </div>
        <?php 
            endforeach;
        else:
        ?>
            <div class="no-categories">
                <p>No categories found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Intersection Observer for smooth scroll animations
document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
            }
        });
    }, {
        threshold: 0.1
    });

    document.querySelectorAll('.category-card').forEach((card) => {
        observer.observe(card);
    });

    // Lazy loading for images
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const src = img.getAttribute('data-src');
                if (src) {
                    img.style.backgroundImage = `url(${src})`;
                    img.removeAttribute('data-src');
                }
            }
        });
    });

    document.querySelectorAll('.category-image').forEach((img) => {
        imageObserver.observe(img);
    });
});

// Add smooth hover effect
document.querySelectorAll('.category-card').forEach(card => {
    card.addEventListener('mouseenter', function(e) {
        const bounds = this.getBoundingClientRect();
        const mouseX = e.clientX - bounds.left;
        const mouseY = e.clientY - bounds.top;
        
        this.style.transform = `
            translateY(-5px) 
            perspective(1000px) 
            rotateX(${(mouseY - bounds.height/2) * 0.01}deg) 
            rotateY(${(mouseX - bounds.width/2) * 0.01}deg)
        `;
    });

    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) perspective(1000px) rotateX(0) rotateY(0)';
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>