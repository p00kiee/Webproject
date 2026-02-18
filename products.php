<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables with default values to prevent undefined errors
$page_title = "Premium Equipment - TechEquipments";
$current_page = 'products';
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$subcategory_id = isset($_GET['subcategory']) ? (int)$_GET['subcategory'] : null;
$availability_type = isset($_GET['availability']) ? $_GET['availability'] : '';
$products = []; // Initialize as empty array
$total_products = 0;
$categories = []; // Initialize as empty array
$subcategories = []; // Initialize as empty array
$category_counts = []; // Initialize as empty array
$subcategory_counts = []; // Initialize as empty array
$inquiry_count = 0; // Count of products requiring inquiry
$purchase_count = 0; // Count of products available for direct purchase

require_once 'includes/header.php';
require_once 'config/database.php';

// Check database connection
if (!$pdo) {
    // Display a user-friendly error instead of stopping execution
    $db_error = 'Database connection failed. Please try again later.';
} else {
    // Fetch categories and subcategories with hierarchical structure
    try {
        // Get all categories with parent_id to distinguish between main categories and subcategories
        $stmt = $pdo->query("SELECT id, name, parent_id FROM categories ORDER BY name");
        $allCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organize into main categories and subcategories
        $categories = [];
        $subcategories = [];
        $categoryHierarchy = [];
        
        foreach ($allCategories as $category) {
            if ($category['parent_id'] === null) {
                // This is a main category
                $categories[] = $category;
                $categoryHierarchy[$category['id']] = [
                    'name' => $category['name'],
                    'subcategories' => []
                ];
            } else {
                // This is a subcategory
                $subcategories[] = $category;
                
                // Add to hierarchy if parent exists
                if (isset($categoryHierarchy[$category['parent_id']])) {
                    $categoryHierarchy[$category['parent_id']]['subcategories'][] = $category;
                }
            }
        }
        
        // Get active category/subcategory details for breadcrumb
        $activeCategory = null;
        $activeSubcategory = null;
        $activeCategoryPath = [];
        
        if ($category_id) {
            // Check if it's a main category or subcategory
            foreach ($categories as $category) {
                if ($category['id'] == $category_id) {
                    $activeCategory = $category;
                    break;
                }
            }
            
            if (!$activeCategory) {
                // Check if it's a subcategory
                foreach ($subcategories as $subcategory) {
                    if ($subcategory['id'] == $category_id) {
                        $activeSubcategory = $subcategory;
                        
                        // Find the parent category
                        foreach ($categories as $category) {
                            if ($category['id'] == $subcategory['parent_id']) {
                                $activeCategory = $category;
                                $activeCategoryPath = [
                                    $category,
                                    $subcategory
                                ];
                                break;
                            }
                        }
                        break;
                    }
                }
            } else {
                $activeCategoryPath = [$activeCategory];
            }
        }
        
        // Count products in each category and subcategory
        // First count main categories
        $stmt = $pdo->query("
            SELECT c.id, COUNT(p.id) as count 
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id
            WHERE c.parent_id IS NULL
            GROUP BY c.id
        ");
        $mainCategoryCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Then count subcategories
        $stmt = $pdo->query("
            SELECT c.id, COUNT(p.id) as count 
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id
            WHERE c.parent_id IS NOT NULL
            GROUP BY c.id
        ");
        $subcategoryCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Combine both for display
        $category_counts = $mainCategoryCounts;
        $subcategory_counts = $subcategoryCounts;
        
        // Also count total products in main categories including their subcategories
        $categoryTotalCounts = [];
        foreach ($categories as $category) {
            // Start with products directly in this category
            $categoryTotalCounts[$category['id']] = $mainCategoryCounts[$category['id']] ?? 0;
            
            // Add counts from subcategories
            foreach ($subcategories as $subcategory) {
                if ($subcategory['parent_id'] == $category['id']) {
                    $categoryTotalCounts[$category['id']] += $subcategoryCounts[$subcategory['id']] ?? 0;
                }
            }
        }
    } catch(PDOException $e) {
        error_log("Error fetching categories: " . $e->getMessage());
        $categories = [];
        $subcategories = [];
        $categoryHierarchy = [];
        $category_counts = [];
        $subcategory_counts = [];
        $categoryTotalCounts = [];
    }

    // Build product query with support for categories and subcategories
    $query = "SELECT p.*, c.name as category_name, c.parent_id,
              pc.name as parent_category_name
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              LEFT JOIN categories pc ON c.parent_id = pc.id
              WHERE 1=1";
    $params = [];

    if ($category_id) {
        // Check if it's a main category
        $isMainCategory = false;
        foreach ($categories as $category) {
            if ($category['id'] == $category_id) {
                $isMainCategory = true;
                break;
            }
        }
        
        if ($isMainCategory) {
            // If main category selected, include all its subcategories
            $subcategoryIds = [];
            foreach ($subcategories as $subcategory) {
                if ($subcategory['parent_id'] == $category_id) {
                    $subcategoryIds[] = $subcategory['id'];
                }
            }
            
            if (!empty($subcategoryIds)) {
                // Include main category and all its subcategories
                $placeholders = implode(',', array_fill(0, count($subcategoryIds), '?'));
                $query .= " AND (p.category_id = ? OR p.category_id IN ($placeholders))";
                $params[] = $category_id;
                foreach ($subcategoryIds as $id) {
                    $params[] = $id;
                }
            } else {
                // Only main category (no subcategories)
                $query .= " AND p.category_id = ?";
                $params[] = $category_id;
            }
        } else {
            // Subcategory selected - show only products from this subcategory
            $query .= " AND p.category_id = ?";
            $params[] = $category_id;
        }
    }

    if ($search) {
        $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    // Add filter for availability type
    if ($availability_type === 'inquiry') {
        $query .= " AND (p.show_price = 0 OR p.show_price IS NULL)";
    } else if ($availability_type === 'purchase') {
        $query .= " AND p.show_price = 1";
    }

    switch ($sort) {
        case 'price_low':
            $query .= " ORDER BY p.price ASC";
            break;
        case 'price_high':
            $query .= " ORDER BY p.price DESC";
            break;
        case 'name_asc':
            $query .= " ORDER BY p.name ASC";
            break;
        case 'name_desc':
            $query .= " ORDER BY p.name DESC";
            break;
        default:
            $query .= " ORDER BY p.created_at DESC";
    }

    // Fetch products
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total_products = count($products);
    } catch(PDOException $e) {
        error_log("Error fetching products: " . $e->getMessage());
        $products = []; // Set to empty array on error
        $total_products = 0;
    }
    
    // Count products by availability type
    try {
        $stmt = $pdo->query("SELECT 
                             SUM(CASE WHEN show_price = 1 THEN 1 ELSE 0 END) as purchase_count,
                             SUM(CASE WHEN show_price = 0 OR show_price IS NULL THEN 1 ELSE 0 END) as inquiry_count
                             FROM products");
        $availability_counts = $stmt->fetch(PDO::FETCH_ASSOC);
        $purchase_count = $availability_counts['purchase_count'] ?? 0;
        $inquiry_count = $availability_counts['inquiry_count'] ?? 0;
    } catch(PDOException $e) {
        error_log("Error counting products by availability: " . $e->getMessage());
        // Silently fail, counts are optional
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

        .products-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Hero Section */
        .products-hero {
            position: relative;
            background: linear-gradient(145deg, #003366, #1e40af);
            color: white;
            text-align: center;
            padding: 5rem 2rem;
            margin-bottom: 3rem;
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .products-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB4PSIwIiB5PSIwIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSgxMCkiPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIyIiBoZWlnaHQ9IjIiIGZpbGw9IiNmZmZmZmYiIG9wYWNpdHk9IjAuMSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNwYXR0ZXJuKSIvPjwvc3ZnPg==');
            background-size: cover;
            opacity: 0.15;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }

        .products-hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            letter-spacing: -0.02em;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .products-hero p {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            font-weight: 400;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .product-stats {
            display: flex;
            justify-content: center;
            gap: 3.5rem;
            margin-top: 2.5rem;
            position: relative;
        }

        .product-stats::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background-color: rgba(255,255,255,0.3);
            border-radius: 4px;
        }

        .stat-item {
            text-align: center;
            position: relative;
        }

        .stat-item:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -1.75rem;
            top: 50%;
            transform: translateY(-50%);
            height: 40px;
            width: 1px;
            background-color: rgba(255,255,255,0.3);
        }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 700;
            display: block;
            margin-bottom: 0.25rem;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        /* Filter Section */
        .filter-section {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 1.75rem;
            margin-bottom: 2.5rem;
            position: relative;
            border: 1px solid var(--gray-100);
        }

        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--gray-100);
            padding-bottom: 1rem;
        }

        .filter-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text);
        }

        .filter-results {
            font-size: 0.95rem;
            color: var(--text-light);
            background: var(--gray-100);
            padding: 0.4rem 0.75rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 0.25rem;
        }

        .filter-select, .filter-input {
            padding: 0.85rem 1rem;
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius-sm);
            font-size: 0.95rem;
            color: var(--text);
            background-color: var(--white);
            transition: var(--transition);
            -webkit-appearance: none;
            appearance: none;
            box-shadow: var(--shadow-sm);
        }

        .filter-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }

        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .search-group {
            position: relative;
        }

        .search-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary);
            pointer-events: none;
        }

        .search-input {
            padding-left: 2.5rem;
            width: 100%;
        }

        .filter-button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.85rem 1.5rem;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 2px 5px rgba(37, 99, 235, 0.2);
        }

        .filter-button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
        }

        .filter-button i {
            font-size: 0.9rem;
        }
        
        /* Breadcrumb navigation */
        .category-breadcrumb {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            background-color: var(--white);
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius-sm);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-100);
        }

        .category-breadcrumb a, 
        .category-breadcrumb span {
            font-size: 0.875rem;
            color: var(--text-light);
            text-decoration: none;
            padding: 0.25rem 0.5rem;
            border-radius: var(--border-radius-sm);
            transition: var(--transition);
        }

        .category-breadcrumb a:hover {
            background-color: var(--gray-100);
            color: var(--primary);
        }

        .category-breadcrumb i {
            margin: 0 0.25rem;
            font-size: 0.75rem;
            color: var(--text-light);
        }

        .category-breadcrumb .current {
            font-weight: 600;
            color: var(--primary);
        }

        .category-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--gray-100);
        }

        .category-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--gray-100);
            border: 1px solid var(--gray-200);
            border-radius: 20px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            color: var(--text);
            text-decoration: none;
            transition: var(--transition);
            font-weight: 500;
        }

        .category-chip:hover {
            background: var(--primary-extra-light);
            border-color: var(--primary-light);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .category-chip.active {
            background: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary-dark);
            font-weight: 600;
        }

        .category-chip .count {
            background: var(--primary-extra-light);
            color: var(--primary);
            border-radius: 12px;
            padding: 0.15rem 0.6rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .category-chip.active .count {
            background: var(--primary);
            color: white;
        }
        
        /* Subcategory styles */
        .subcategory-title {
            margin-top: 1.75rem;
            color: var(--primary);
            font-weight: 600;
        }

        .subcategory-chips {
            background-color: var(--primary-extra-light);
            padding: 0.75rem;
            border-radius: var(--border-radius-sm);
            margin-bottom: 2rem;
        }

        .subcategory-chip {
            background-color: white;
            border-color: var(--primary-light);
        }

        .subcategory-chip:hover {
            background-color: white;
            box-shadow: var(--shadow);
        }

        .subcategory-chip.active {
            background-color: var(--primary);
            color: white;
        }

        .subcategory-chip.active .count {
            background-color: white;
            color: var(--primary);
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.75rem;
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

        .product-image {
            position: relative;
            height: 240px;
            background: var(--gray-50);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--gray-100);
        }

        .product-image img {
            max-width: 85%;
            max-height: 85%;
            object-fit: contain;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.08);
        }

        .product-badge {
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

        .product-content {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            position: relative;
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
        
        /* Category hierarchy display in product cards */
        .category-hierarchy {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .parent-category {
            font-size: 0.75rem;
            color: var(--text-light);
            font-weight: 500;
        }

        .category-separator {
            font-size: 0.65rem;
            margin: 0 0.4rem;
            color: var(--text-light);
        }

        .product-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 3.15rem;
        }

        .product-price {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 1.25rem;
            margin-top: auto;
            display: flex;
            align-items: center;
        }

        .product-price::before {
            content: 'Rs.';
            font-size: 0.9rem;
            font-weight: 600;
            margin-right: 0.3rem;
            opacity: 0.7;
        }

        .price-inquiry {
            font-size: 1rem;
            color: var(--text-light);
            margin-bottom: 1.25rem;
            margin-top: auto;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .price-inquiry i {
            color: var(--warning);
        }

        .product-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .btn {
            flex: 1;
            padding: 0.85rem;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            font-size: 0.9rem;
            text-align: center;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
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

/* No Products Found */
.no-products {
    grid-column: 1 / -1;
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 4rem 2rem;
    text-align: center;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-100);
}

.no-products-icon {
    font-size: 3.5rem;
    color: var(--gray-300);
    margin-bottom: 1.5rem;
    background: var(--gray-100);
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin: 0 auto 2rem;
}

.no-products h3 {
    font-size: 1.75rem;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 1rem;
}

.no-products p {
    color: var(--text-light);
    max-width: 500px;
    margin: 0 auto 1.5rem;
    font-size: 1.05rem;
}

.reset-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
    background: var(--primary-extra-light);
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius-sm);
}

.reset-link:hover {
    color: var(--primary-dark);
    background: var(--primary-light);
    transform: translateY(-2px);
}

/* Tooltip styles */
.tooltip {
    position: relative;
    display: inline-block;
}

.tooltip .tooltip-text {
    visibility: hidden;
    width: 180px;
    background-color: var(--text);
    color: white;
    text-align: center;
    padding: 8px;
    border-radius: 6px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity 0.3s;
    font-size: 0.8rem;
    font-weight: normal;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.tooltip .tooltip-text::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: var(--text) transparent transparent transparent;
}

.tooltip:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
}

/* Database Error Message */
.db-error {
    background-color: var(--primary-extra-light);
    border: 1px solid var(--primary-light);
    border-radius: var(--border-radius);
    padding: 1rem;
    margin-bottom: 2rem;
    color: var(--primary-dark);
    text-align: center;
}

/* Availability Chips */
.availability-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-100);
}

.availability-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--gray-100);
    border: 1px solid var(--gray-200);
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    color: var(--text);
    text-decoration: none;
    transition: var(--transition);
    font-weight: 500;
}

.availability-chip:hover {
    background: var(--primary-extra-light);
    border-color: var(--primary-light);
    color: var(--primary);
    transform: translateY(-2px);
}

.availability-chip.active {
    background: var(--primary-light);
    border-color: var(--primary);
    color: var(--primary-dark);
    font-weight: 600;
}

.availability-chip .count {
    background: var(--primary-extra-light);
    color: var(--primary);
    border-radius: 12px;
    padding: 0.15rem 0.6rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.availability-chip.active .count {
    background: var(--primary);
    color: white;
}

.availability-chip.get-now {
    border-color: var(--success);
}

.availability-chip.get-now:hover, 
.availability-chip.get-now.active {
    background: rgba(16, 185, 129, 0.1);
    border-color: var(--success);
    color: var(--success);
}

.availability-chip.get-now.active .count {
    background: var(--success);
    color: white;
}

.availability-chip.inquiry {
    border-color: var(--warning);
}

.availability-chip.inquiry:hover,
.availability-chip.inquiry.active {
    background: rgba(245, 158, 11, 0.1);
    border-color: var(--warning);
    color: var(--warning);
}

.availability-chip.inquiry.active .count {
    background: var(--warning);
    color: white;
}

.filter-section-title {
    font-size: 1rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-section-title i {
    color: var(--primary);
    font-size: 0.9rem;
}

/* Enhanced product badge for availability */
.product-badge.get-now {
    background: var(--success);
    box-shadow: 0 2px 5px rgba(16, 185, 129, 0.3);
}

.product-badge.inquiry {
    background: var(--warning);
    box-shadow: 0 2px 5px rgba(245, 158, 11, 0.3);
}

/* Responsive Styles */
@media (max-width: 992px) {
    .products-hero h1 {
        font-size: 2.5rem;
    }

    .filter-form {
        grid-template-columns: 1fr 1fr;
    }

    .products-hero {
        padding: 3.5rem 1.5rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
}

@media (max-width: 768px) {
    .products-container {
        padding: 1.25rem;
    }

    .product-stats {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 2rem;
        justify-content: space-around;
    }
    
    .stat-item:not(:last-child)::after {
        display: none;
    }

    .filter-form {
        grid-template-columns: 1fr;
    }

    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.25rem;
    }

    .products-hero h1 {
        font-size: 2rem;
    }

    .products-hero p {
        font-size: 1rem;
    }
    
    .product-image {
        height: 200px;
    }
    
    .category-hierarchy {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .category-separator {
        display: none;
    }
    
    .parent-category {
        font-size: 0.7rem;
        opacity: 0.7;
    }
}

@media (max-width: 480px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-section {
        padding: 1.25rem;
    }

    .product-actions {
        flex-direction: column;
    }
    
    .products-container {
        padding: 1rem 0.75rem;
    }
    
    .category-chips {
        gap: 0.5rem;
    }
    
    .category-chip {
        padding: 0.4rem 0.75rem;
        font-size: 0.8rem;
    }
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.product-card {
    animation: fadeIn 0.5s ease forwards;
    opacity: 0;
}

.product-card:nth-child(2) { animation-delay: 0.06s; }
.product-card:nth-child(3) { animation-delay: 0.12s; }
.product-card:nth-child(4) { animation-delay: 0.18s; }
.product-card:nth-child(5) { animation-delay: 0.24s; }
.product-card:nth-child(6) { animation-delay: 0.3s; }
.product-card:nth-child(7) { animation-delay: 0.36s; }
.product-card:nth-child(8) { animation-delay: 0.42s; }
.product-card:nth-child(9) { animation-delay: 0.48s; }

/* Enhance select for subcategories */
optgroup {
    font-weight: 600;
    color: var(--text-dark);
    background-color: var(--gray-100);
}

option.subcategory-option {
    padding-left: 15px;
    color: var(--text);
    font-weight: normal;
}

.total-products-count {
    font-size: 1.125rem;
    color: var(--primary);
    font-weight: 600;
    margin-bottom: 1rem;
}
</style>
</head>
<body>
    <div class="products-container">
        <?php if (isset($db_error)): ?>
            <div class="db-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $db_error; ?>
            </div>
        <?php endif; ?>

        <!-- Hero Section -->
        <section class="products-hero">
            <div class="hero-content">
                <h1>Premium Equipment Collection</h1>
                <p>Discover high-quality equipment designed to meet your professional needs with exceptional performance and reliability.</p>
                
                <div class="product-stats">
                    <div class="stat-item">
                        <span class="stat-value"><?php echo $total_products; ?></span>
                        <span class="stat-label">Products</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?php echo count($categories); ?></span>
                        <span class="stat-label">Categories</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">24/7</span>
                        <span class="stat-label">Support</span>
                    </div>
                </div>
            </div>
        </section>

        <?php if ($activeCategory): ?>
        <!-- Breadcrumb Navigation -->
        <div class="category-breadcrumb">
            <a href="products.php"><i class="fas fa-home"></i> All Products</a>
            <?php if (count($activeCategoryPath) > 1): ?>
                <i class="fas fa-chevron-right"></i>
                <a href="products.php?category=<?php echo $activeCategoryPath[0]['id']; ?>">
                    <?php echo htmlspecialchars($activeCategoryPath[0]['name']); ?>
                </a>
                <i class="fas fa-chevron-right"></i>
                <span class="current"><?php echo htmlspecialchars($activeCategoryPath[1]['name']); ?></span>
            <?php else: ?>
                <i class="fas fa-chevron-right"></i>
                <span class="current"><?php echo htmlspecialchars($activeCategory['name']); ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Filter Section -->
        <section class="filter-section">
            <div class="filter-header">
                <h2 class="filter-title">Find Your Equipment</h2>
                <div class="filter-results">
                    <?php echo $total_products; ?> product<?php echo $total_products !== 1 ? 's' : ''; ?> found
                </div>
            </div>
            
            <form action="" method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="category" class="filter-label">Categories</label>
                    <select name="category" id="category" class="filter-select">
                        <option value="">All Categories</option>
                        
                        <?php foreach ($categories as $cat): 
                            // Skip categories with no products
                            if (!isset($categoryTotalCounts[$cat['id']]) || $categoryTotalCounts[$cat['id']] <= 0) continue;
                        ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?> 
                                (<?php echo $categoryTotalCounts[$cat['id']] ?? 0; ?>)
                            </option>
                            
                            <?php 
                            // Add subcategories indented under the main category
                            if (isset($categoryHierarchy[$cat['id']]['subcategories'])):
                                foreach ($categoryHierarchy[$cat['id']]['subcategories'] as $subcat):
                                    // Skip subcategories with no products
                                    if (!isset($subcategory_counts[$subcat['id']]) || $subcategory_counts[$subcat['id']] <= 0) continue;
                            ?>
                                <option value="<?php echo $subcat['id']; ?>" 
                                        <?php echo ($category_id == $subcat['id']) ? 'selected' : ''; ?>
                                        class="subcategory-option">
                                    &nbsp;&nbsp;&nbsp;— <?php echo htmlspecialchars($subcat['name']); ?> 
                                    (<?php echo $subcategory_counts[$subcat['id']] ?? 0; ?>)
                                </option>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="search" class="filter-label">Search Products</label>
                    <div class="search-group">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search" name="search" class="filter-input search-input" 
                               placeholder="Search by name or description..." value="<?php echo $search; ?>">
                    </div>
                </div>

                <div class="filter-group">
                    <label for="sort" class="filter-label">Sort By</label>
                    <select name="sort" id="sort" class="filter-select">
                        <option value="default" <?php echo ($sort == 'default') ? 'selected' : ''; ?>>Latest Products</option>
                        <option value="price_low" <?php echo ($sort == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo ($sort == 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="name_asc" <?php echo ($sort == 'name_asc') ? 'selected' : ''; ?>>Name: A to Z</option>
                        <option value="name_desc" <?php echo ($sort == 'name_desc') ? 'selected' : ''; ?>>Name: Z to A</option>
                    </select>
                </div>

                <button type="submit" class="filter-button">
                    <i class="fas fa-filter"></i>
                    Apply Filters
                </button>
            </form>
            
            <!-- Availability filter -->
            <div class="filter-section-title">
                <i class="fas fa-tag"></i> Shop By Availability
            </div>
            <div class="availability-chips">
                <a href="?<?php echo $search ? 'search=' . urlencode($search) : ''; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $sort && $sort != 'default' ? '&sort=' . $sort : ''; ?>" 
                   class="availability-chip <?php echo !$availability_type ? 'active' : ''; ?>">
                    All Products
                    <span class="count"><?php echo $total_products; ?></span>
                </a>
                
                <a href="?availability=purchase<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $sort && $sort != 'default' ? '&sort=' . $sort : ''; ?>" 
                   class="availability-chip get-now <?php echo $availability_type == 'purchase' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> Get It Now
                    <span class="count"><?php echo $purchase_count; ?></span>
                </a>
                
                <a href="?availability=inquiry<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $sort && $sort != 'default' ? '&sort=' . $sort : ''; ?>" 
                   class="availability-chip inquiry <?php echo $availability_type == 'inquiry' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> By Inquiry
                    <span class="count"><?php echo $inquiry_count; ?></span>
                </a>
            </div>
            
            <!-- Category chips -->
            <div class="filter-section-title">
                <i class="fas fa-th-large"></i> Shop By Category
            </div>
            <div class="category-chips">
                <a href="?<?php echo $search ? 'search=' . urlencode($search) : ''; ?><?php echo $availability_type ? '&availability=' . $availability_type : ''; ?><?php echo $sort && $sort != 'default' ? '&sort=' . $sort : ''; ?>" 
                   class="category-chip <?php echo !$category_id ? 'active' : ''; ?>">
                    All Categories
                    <span class="count"><?php echo $total_products; ?></span>
                </a>
                
                <?php foreach ($categories as $cat): 
                    $count = $categoryTotalCounts[$cat['id']] ?? 0;
                    if ($count > 0): // Only show categories with products
                ?>
                    <a href="?category=<?php echo $cat['id']; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $availability_type ? '&availability=' . $availability_type : ''; ?><?php echo $sort && $sort != 'default' ? '&sort=' . $sort : ''; ?>" 
                       class="category-chip <?php echo ($category_id == $cat['id']) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                        <span class="count"><?php echo $count; ?></span>
                    </a>
                <?php 
                    endif;
                endforeach; ?>
            </div>
            
            <!-- Display subcategory chips only when a main category is selected -->
            <?php 
            $selectedMainCategory = null;

            // Check if the selected category is a main category
            foreach ($categories as $cat) {
                if ($cat['id'] == $category_id) {
                    $selectedMainCategory = $cat;
                    break;
                }
            }

            // Check if selected category is a subcategory and get its parent
            if (!$selectedMainCategory && $category_id) {
                foreach ($subcategories as $subcat) {
                    if ($subcat['id'] == $category_id) {
                        foreach ($categories as $cat) {
                            if ($cat['id'] == $subcat['parent_id']) {
                                $selectedMainCategory = $cat;
                                break 2;
                            }
                        }
                    }
                }
            }

            // Show subcategory chips when a main category is selected or a subcategory is selected
            if ($selectedMainCategory && isset($categoryHierarchy[$selectedMainCategory['id']]['subcategories'])):
                $categorySubcategories = $categoryHierarchy[$selectedMainCategory['id']]['subcategories'];
                
                if (!empty($categorySubcategories)):
            ?>
            <div class="filter-section-title subcategory-title">
                <i class="fas fa-tags"></i> <?php echo htmlspecialchars($selectedMainCategory['name']); ?> Subcategories
            </div>
            <div class="category-chips subcategory-chips">
                <a href="?category=<?php echo $selectedMainCategory['id']; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $availability_type ? '&availability=' . $availability_type : ''; ?><?php echo $sort && $sort != 'default' ? '&sort=' . $sort : ''; ?>" 
                   class="category-chip subcategory-chip <?php echo ($category_id == $selectedMainCategory['id']) ? 'active' : ''; ?>">
                    All <?php echo htmlspecialchars($selectedMainCategory['name']); ?>
                    <span class="count"><?php echo $category_counts[$selectedMainCategory['id']] ?? 0; ?></span>
                </a>
                
                <?php foreach ($categorySubcategories as $subcat): 
                    $count = $subcategory_counts[$subcat['id']] ?? 0;
                    if ($count > 0): // Only show subcategories with products
                ?>
                    <a href="?category=<?php echo $subcat['id']; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $availability_type ? '&availability=' . $availability_type : ''; ?><?php echo $sort && $sort != 'default' ? '&sort=' . $sort : ''; ?>" 
                       class="category-chip subcategory-chip <?php echo ($category_id == $subcat['id']) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($subcat['name']); ?>
                        <span class="count"><?php echo $count; ?></span>
                    </a>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
            <?php 
                endif;
            endif; 
            ?>
        </section>

        <!-- Products Grid -->
        <div class="products-grid">
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <div class="no-products-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>No products found</h3>
                    <p>We couldn't find any products matching your criteria. Try adjusting your filters or search terms.</p>
                    <a href="products.php" class="reset-link">
                        <i class="fas fa-redo"></i>
                        Reset all filters
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (isset($product['is_featured']) && $product['is_featured']): ?>
                                <div class="product-badge">Featured</div>
                            <?php else: ?>
                                <?php if (!isset($product['show_price']) || $product['show_price'] == 1): ?>
                                    <div class="product-badge get-now">Get It Now</div>
                                <?php else: ?>
                                    <div class="product-badge inquiry">By Inquiry</div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <img src="assets/uploads/<?php echo htmlspecialchars($product['image'] ?? 'default.jpg'); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.src='assets/images/default-placeholder.png'">
                        </div>
                        <div class="product-content">
                            <?php if (isset($product['parent_id']) && $product['parent_id']): ?>
                                <!-- This is a subcategory product, show both main and subcategory -->
                                <div class="category-hierarchy">
                                    <a href="products.php?category=<?php echo $product['parent_id']; ?>" class="parent-category">
                                        <?php echo htmlspecialchars($product['parent_category_name'] ?? ''); ?>
                                    </a>
                                    <i class="fas fa-chevron-right category-separator"></i>
                                    <a href="products.php?category=<?php echo $product['category_id']; ?>" class="product-category">
                                        <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                                    </a>
                                </div>
                            <?php else: ?>
                                <!-- This is a main category product -->
                                <a href="products.php?category=<?php echo $product['category_id']; ?>" class="product-category">
                                    <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                                </a>
                            <?php endif; ?>
                            
                            <h3 class="product-title" title="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </h3>
                            
                            <?php if (!isset($product['show_price']) || $product['show_price'] == 1): ?>
                                <div class="product-price">
                                    <?php echo number_format($product['price'], 2); ?>
                                </div>
                            <?php else: ?>
                                <div class="price-inquiry">
                                    <i class="fas fa-info-circle"></i> Contact for Price
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-actions">
                                <?php if (!isset($product['show_price']) || $product['show_price'] == 1): ?>
                                    <button type="button" class="btn btn-primary add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-shopping-cart"></i>
                                        Add to Cart
                                    </button>
                                <?php else: ?>
                                    <a href="contact_us.php?product=<?php echo $product['id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-envelope"></i>
                                        Inquire Now
                                    </a>
                                <?php endif; ?>
                                <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn btn-outline">
                                    <i class="fas fa-eye"></i>
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Function to handle Add to Cart
            const addToCart = (productId) => {
                fetch('ajax/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=1`, // Default quantity is 1
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

            // Add to Cart buttons
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    addToCart(productId);
                });
            });
            
            // Automatic form submission when select changes
            document.getElementById('category').addEventListener('change', function() {
                this.form.submit();
            });
            
            document.getElementById('sort').addEventListener('change', function() {
                this.form.submit();
            });
            
            // Add animation classes to product cards
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.05}s`;
            });
            
            // Enhance category select to make subcategories more visible
            const categorySelect = document.getElementById('category');
            const options = categorySelect.querySelectorAll('option');
            options.forEach(option => {
                if (option.textContent.includes('—')) {
                    option.setAttribute('data-is-subcategory', 'true');
                    option.style.paddingLeft = '20px';
                    option.style.fontWeight = 'normal';
                }
            });
            
            // Helper function to show subcategory chips based on main category selection
            const updateSubcategoryVisibility = () => {
                const selectedCategory = categorySelect.value;
                const subcategorySections = document.querySelectorAll('.subcategory-chips');
                
                subcategorySections.forEach(section => {
                    const mainCategoryId = section.getAttribute('data-main-category');
                    if (mainCategoryId && mainCategoryId === selectedCategory) {
                        section.style.display = 'flex';
                    } else {
                        section.style.display = 'none';
                    }
                });
            };
            
            // Apply on page load and category change
            updateSubcategoryVisibility();
            categorySelect.addEventListener('change', updateSubcategoryVisibility);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
</body>
</html>