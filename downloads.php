<?php
// Set page title
$page_title = "Downloads";

// Include header from includes directory
include_once 'includes/header.php';

// Connect to database
require_once 'config/database.php';

// Get the download type from URL parameter
$category_slug = isset($_GET['category']) ? $_GET['category'] : null;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';

// Function to get file size in readable format
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Display error message if there is one
if (isset($_GET['error'])) {
    switch($_GET['error']) {
        case 'file_not_found':
            $error_message = "The requested file was not found.";
            break;
        case 'database_error':
            $error_message = "A database error occurred. Please try again later.";
            break;
        default:
            $error_message = "An unknown error occurred.";
    }
}

// Get categories from database
try {
    $stmt = $pdo->query("SELECT * FROM download_categories WHERE active = 1 ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $categories = [];
    $error_message = "Error fetching categories: " . $e->getMessage();
}

// Count documents in each category for displaying counts
$category_counts = [];
try {
    $stmt = $pdo->query("SELECT c.id, COUNT(d.id) as doc_count 
                         FROM download_categories c 
                         LEFT JOIN documents d ON c.id = d.category_id AND d.active = 1 
                         WHERE c.active = 1 
                         GROUP BY c.id");
    $counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($counts as $count) {
        $category_counts[$count['id']] = $count['doc_count'];
    }
} catch(PDOException $e) {
    // Silently fail, counts will just not show
}

// Build the query based on filters
try {
    $params = [];
    $where_clauses = ["d.active = 1"];
    
    // Category filter
    if ($category_slug) {
        $where_clauses[] = "c.slug = ?";
        $params[] = $category_slug;
    }
    
    // Search filter
    if ($search_query) {
        $where_clauses[] = "(d.title LIKE ? OR d.description LIKE ?)";
        $params[] = "%{$search_query}%";
        $params[] = "%{$search_query}%";
    }
    
    // File type filter
    if ($filter_type) {
        $where_clauses[] = "LOWER(d.original_file_name) LIKE ?";
        $params[] = "%.{$filter_type}";
    }
    
    // Build the where clause
    $where_clause = implode(" AND ", $where_clauses);
    
    // Set the order by clause based on sort parameter
    switch ($sort_by) {
        case 'name_asc':
            $order_clause = "d.title ASC";
            break;
        case 'name_desc':
            $order_clause = "d.title DESC";
            break;
        case 'downloads':
            $order_clause = "d.download_count DESC";
            break;
        case 'size_asc':
            $order_clause = "d.file_size ASC";
            break;
        case 'size_desc':
            $order_clause = "d.file_size DESC";
            break;
        case 'oldest':
            $order_clause = "d.created_at ASC";
            break;
        case 'latest':
        default:
            $order_clause = "d.featured DESC, d.created_at DESC";
    }
    
    // Prepare and execute the query
    $query = "SELECT d.*, c.name as category_name, c.icon as category_icon, c.slug as category_slug
              FROM documents d 
              JOIN download_categories c ON d.category_id = c.id 
              WHERE {$where_clause} 
              ORDER BY {$order_clause}";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group documents by category when showing all (no category filter) and not searching
    $grouped_documents = [];
    if (!$category_slug && !$search_query) {
        foreach ($documents as $doc) {
            if (!isset($grouped_documents[$doc['category_id']])) {
                $grouped_documents[$doc['category_id']] = [
                    'category_name' => $doc['category_name'],
                    'category_icon' => $doc['category_icon'],
                    'category_slug' => $doc['category_slug'],
                    'items' => []
                ];
            }
            $grouped_documents[$doc['category_id']]['items'][] = $doc;
        }
    }
    
} catch(PDOException $e) {
    $documents = [];
    $grouped_documents = [];
    $error_message = "Error fetching documents: " . $e->getMessage();
}

// Get active category name if category is selected
$current_category_name = '';
$current_category_description = '';
if ($category_slug) {
    foreach ($categories as $category) {
        if ($category['slug'] === $category_slug) {
            $current_category_name = $category['name'];
            $current_category_description = $category['description'];
            break;
        }
    }
}

// Get file extensions for filter dropdown
$file_extensions = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT LOWER(SUBSTRING_INDEX(original_file_name, '.', -1)) as extension 
                         FROM documents 
                         WHERE active = 1 
                         ORDER BY extension");
    $extensions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($extensions as $ext) {
        if (!empty($ext['extension'])) {
            $file_extensions[] = $ext['extension'];
        }
    }
} catch(PDOException $e) {
    // Silently fail
}
?>

<div class="page-container">
    <div class="downloads-header">
        <div class="container">
            <h1><?php echo $category_slug ? htmlspecialchars($current_category_name) : 'Downloads'; ?></h1>
            <p><?php echo $category_slug && $current_category_description ? htmlspecialchars($current_category_description) : 'Access our documents, catalogs, brochures, manuals, and specifications for all products.'; ?></p>
        </div>
    </div>

    <?php if (isset($error_message)): ?>
    <div class="container">
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    </div>
    <?php endif; ?>

    <div class="downloads-content">
        <div class="container">
            <!-- Search and Filter Controls -->
            <div class="downloads-controls">
                <form action="downloads.php" method="get" class="search-form">
                    <?php if ($category_slug): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_slug); ?>">
                    <?php endif; ?>
                    
                    <div class="search-group">
                        <input type="text" name="search" placeholder="Search documents..." value="<?php echo htmlspecialchars($search_query); ?>">
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <div class="filter-group">
                        <select name="type" class="filter-select" onchange="this.form.submit()">
                            <option value="">All File Types</option>
                            <?php foreach ($file_extensions as $ext): ?>
                            <option value="<?php echo $ext; ?>" <?php echo $filter_type === $ext ? 'selected' : ''; ?>>
                                <?php echo strtoupper($ext); ?> Files
                            </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <select name="sort" class="filter-select" onchange="this.form.submit()">
                            <option value="latest" <?php echo $sort_by === 'latest' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="oldest" <?php echo $sort_by === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                            <option value="name_asc" <?php echo $sort_by === 'name_asc' ? 'selected' : ''; ?>>Name A-Z</option>
                            <option value="name_desc" <?php echo $sort_by === 'name_desc' ? 'selected' : ''; ?>>Name Z-A</option>
                            <option value="downloads" <?php echo $sort_by === 'downloads' ? 'selected' : ''; ?>>Most Downloaded</option>
                            <option value="size_asc" <?php echo $sort_by === 'size_asc' ? 'selected' : ''; ?>>Size (Smallest)</option>
                            <option value="size_desc" <?php echo $sort_by === 'size_desc' ? 'selected' : ''; ?>>Size (Largest)</option>
                        </select>
                        
                        <?php if ($search_query || $filter_type || $sort_by !== 'latest'): ?>
                        <a href="<?php echo $category_slug ? "downloads.php?category=" . htmlspecialchars($category_slug) : "downloads.php"; ?>" class="reset-filters">
                            <i class="fas fa-times"></i> Reset Filters
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Categories and Content Layout -->
            <div class="downloads-layout">
                <!-- Download Categories Sidebar -->
                <div class="categories-sidebar">
                    <h3>Categories</h3>
                    <ul class="category-list">
                        <li>
                            <a href="downloads.php" class="<?php echo (!$category_slug) ? 'active' : ''; ?>">
                                <i class="fas fa-folder"></i>
                                <span>All Documents</span>
                                <span class="count"><?php echo array_sum($category_counts); ?></span>
                            </a>
                        </li>
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="downloads.php?category=<?php echo $category['slug']; ?>" 
                                   class="<?php echo ($category_slug === $category['slug']) ? 'active' : ''; ?>">
                                    <i class="fas <?php echo $category['icon']; ?>"></i>
                                    <span><?php echo htmlspecialchars($category['name']); ?></span>
                                    <span class="count"><?php echo isset($category_counts[$category['id']]) ? $category_counts[$category['id']] : 0; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Download Items Main Content -->
                <div class="downloads-main">
                    <?php if ($search_query): ?>
                        <div class="search-results-header">
                            <h2>
                                <i class="fas fa-search"></i> 
                                Search Results for "<?php echo htmlspecialchars($search_query); ?>"
                            </h2>
                            <p>Found <?php echo count($documents); ?> document<?php echo count($documents) != 1 ? 's' : ''; ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ((!$category_slug && !$search_query) && !empty($grouped_documents)): ?>
                        <!-- Grouped by category -->
                        <?php foreach ($grouped_documents as $group): ?>
                            <div class="category-section">
                                <div class="category-header">
                                    <h2>
                                        <i class="fas <?php echo $group['category_icon']; ?>"></i>
                                        <?php echo htmlspecialchars($group['category_name']); ?>
                                    </h2>
                                    <a href="downloads.php?category=<?php echo $group['category_slug']; ?>" class="view-all">
                                        View all <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                                
                                <div class="download-grid">
                                    <?php foreach ($group['items'] as $index => $doc): ?>
                                        <?php if ($index < 4): // Show only first 4 items per category ?>
                                            <?php include 'includes/document_card.php'; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                
                                <?php if (count($group['items']) > 4): ?>
                                    <div class="more-items">
                                        <a href="downloads.php?category=<?php echo $group['category_slug']; ?>" class="more-link">
                                            View all <?php echo count($group['items']); ?> documents <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php elseif (!empty($documents)): ?>
                        <!-- Flat list for specific category or search results -->
                        <div class="download-grid<?php echo ($search_query || $category_slug) ? ' full-width' : ''; ?>">
                            <?php foreach ($documents as $doc): ?>
                                <?php include 'includes/document_card.php'; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-documents">
                            <i class="fas fa-folder-open"></i>
                            <h3>No documents found</h3>
                            <?php if ($search_query): ?>
                                <p>No documents matched your search criteria. Please try different keywords or browse by category.</p>
                            <?php else: ?>
                                <p>There are currently no documents available in this category.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($pagination)): ?>
                        <div class="pagination">
                            <?php echo $pagination; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-container {
        padding-bottom: 60px;
    }

    .downloads-header {
        background-color: #f0f7ff;
        background-image: linear-gradient(135deg, #f0f7ff 0%, #e0f2fe 100%);
        padding: 50px 0 30px;
        margin-bottom: 30px;
    }

    .downloads-header h1 {
        color: #1e40af;
        margin-bottom: 10px;
        font-size: 2rem;
        font-weight: 700;
    }

    .downloads-header p {
        color: #64748b;
        font-size: 1.1rem;
        max-width: 800px;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* Search and Filter Controls */
    .downloads-controls {
        margin-bottom: 30px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
        padding: 15px 20px;
    }

    .search-form {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
        justify-content: space-between;
    }

    .search-group {
        display: flex;
        flex-grow: 1;
        max-width: 500px;
    }

    .search-group input {
        flex-grow: 1;
        padding: 10px 15px;
        border: 1px solid #e2e8f0;
        border-right: none;
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px;
        font-size: 0.95rem;
    }

    .search-group input:focus {
        outline: none;
        border-color: #bfdbfe;
    }

    .search-button {
        background-color: #003366;
        color: white;
        border: none;
        padding: 10px 15px;
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .search-button:hover {
        background-color: #003366;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-select {
        padding: 10px 30px 10px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.95rem;
        background-color: #f8fafc;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        cursor: pointer;
    }

    .filter-select:focus {
        outline: none;
        border-color: #bfdbfe;
    }

    .reset-filters {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #64748b;
        font-size: 0.9rem;
        text-decoration: none;
        transition: color 0.2s;
    }

    .reset-filters:hover {
        color: #dc2626;
    }

    /* Layout for categories and content */
    .downloads-layout {
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 30px;
    }

    /* Categories Sidebar */
    .categories-sidebar {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
        padding: 20px;
        align-self: start;
        position: sticky;
        top: 90px;
    }

    .categories-sidebar h3 {
        color: #1e40af;
        font-size: 1.2rem;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e2e8f0;
    }

    .category-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .category-list li {
        margin-bottom: 5px;
    }

    .category-list a {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        color: #1e293b;
        text-decoration: none;
        border-radius: 6px;
        transition: all 0.2s;
    }

    .category-list a:hover {
        background-color: #f1f5f9;
        color: #003366;
    }

    .category-list a.active {
        background-color: #e0f2fe;
        color: #003366;
        font-weight: 600;
    }

    .category-list a i {
        width: 20px;
        margin-right: 10px;
    }

    .category-list .count {
        margin-left: auto;
        background-color: #f1f5f9;
        color: #64748b;
        font-size: 0.8rem;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 600;
    }

    .category-list a.active .count {
        background-color: #bfdbfe;
        color: #1e40af;
    }

    /* Search Results Header */
    .search-results-header {
        margin-bottom: 20px;
    }

    .search-results-header h2 {
        color: #1e40af;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 5px;
    }

    .search-results-header p {
        color: #64748b;
        font-size: 0.95rem;
    }

    /* Category Section */
    .category-section {
        margin-bottom: 40px;
    }

    .category-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .category-header h2 {
        color: #1e40af;
        font-size: 1.3rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .category-header h2 i {
        color: #003366;
    }

    .view-all {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #003366;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }

    .view-all:hover {
        transform: translateX(3px);
    }

    .download-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .download-grid.full-width {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }

    .more-items {
        text-align: center;
        margin-top: 10px;
    }

    .more-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: #f8fafc;
        color: #003366;
        padding: 8px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .more-link:hover {
        background-color: #e0f2fe;
        transform: translateY(-2px);
    }

    .download-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
        padding: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .download-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .download-card.featured {
        border: 1px solid #bfdbfe;
    }

    .featured-badge {
        position: absolute;
        top: 12px;
        right: 0;
        background-color: #003366;
        color: white;
        padding: 4px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }

    .download-icon {
        font-size: 2rem;
        color: #003366;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        background-color: #f0f7ff;
        border-radius: 50%;
    }

    .download-info {
        flex-grow: 1;
    }

    .download-info h3 {
        color: #1e293b;
        font-size: 1.1rem;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .download-info p {
        color: #64748b;
        margin-bottom: 15px;
        font-size: 0.95rem;
    }

    .download-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        color: #94a3b8;
        font-size: 0.85rem;
        margin-bottom: 20px;
    }

    .download-meta i {
        margin-right: 5px;
    }

    .download-btn {
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
        transition: all 0.3s;
    }

    .download-btn:hover {
        background-color: #003366;
        transform: translateY(-2px);
    }

    .category-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background-color: #f1f5f9;
        color: #1e40af;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .no-documents {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .no-documents i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 20px;
    }

    .no-documents h3 {
        color: #1e293b;
        font-size: 1.3rem;
        margin-bottom: 10px;
    }

    .no-documents p {
        color: #64748b;
        max-width: 500px;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-top: 30px;
    }

    .pagination a,
    .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 35px;
        height: 35px;
        padding: 0 10px;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .pagination a {
        background-color: #f1f5f9;
        color: #1e293b;
    }

    .pagination a:hover {
        background-color: #e0f2fe;
        color: #003366;
    }

    .pagination span.current {
        background-color: #003366;
        color: white;
        font-weight: 600;
    }

    /* Responsive Styles */
    @media (max-width: 991px) {
        .downloads-layout {
            grid-template-columns: 1fr;
        }
        
        .categories-sidebar {
            position: static;
            margin-bottom: 20px;
        }
        
        .category-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .category-list a {
            flex-direction: column;
            text-align: center;
            padding: 10px;
            flex: 1;
            min-width: 100px;
        }
        
        .category-list a i {
            margin-right: 0;
            margin-bottom: 5px;
            font-size: 1.2rem;
        }
        
        .category-list .count {
            margin-left: 0;
            margin-top: 5px;
        }
    }

    @media (max-width: 768px) {
        .search-form {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-group {
            flex-wrap: wrap;
        }
        
        .filter-select {
            flex: 1;
            min-width: 140px;
        }
        
        .download-grid {
            grid-template-columns: 1fr;
        }
        
        .download-meta {
            flex-direction: column;
            gap: 8px;
        }
        
        .downloads-header {
            padding: 30px 0 20px;
        }
    }
</style>

<!-- Template for document card (to be included) -->
<script>
    // This provides the document card template content
    // In a real implementation, this would be in a separate file: includes/document_card.php
    function documentCard(doc) {
        return `
            <div class="download-card${doc.featured ? ' featured' : ''}">
                ${doc.featured ? '<div class="featured-badge">Featured</div>' : ''}
                <div class="download-icon">
                    <i class="fas ${getFileIcon(doc.original_file_name)}"></i>
                </div>
                ${!category_slug ? `<div class="category-tag">
                    <i class="fas ${doc.category_icon}"></i> ${doc.category_name}
                </div>` : ''}
                <div class="download-info">
                    <h3>${doc.title}</h3>
                    <p>${doc.description}</p>
                    <div class="download-meta">
                        <span><i class="fas fa-calendar-alt"></i> ${formatDate(doc.created_at)}</span>
                        <span><i class="fas fa-file-alt"></i> ${formatFileSize(doc.file_size)}</span>
                        <span><i class="fas fa-download"></i> ${doc.download_count} downloads</span>
                    </div>
                </div>
                <a href="download_handler.php?file=${doc.id}" class="download-btn">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        `;
    }

    function getFileIcon(filename) {
        // Get file extension and return appropriate icon class
        const extension = filename.split('.').pop().toLowerCase();
        const iconMap = {
            'pdf': 'fa-file-pdf',
            'doc': 'fa-file-word',
            'docx': 'fa-file-word',
            'xls': 'fa-file-excel',
            'xlsx': 'fa-file-excel',
            'ppt': 'fa-file-powerpoint',
            'pptx': 'fa-file-powerpoint',
            'jpg': 'fa-file-image',
            'jpeg': 'fa-file-image',
            'png': 'fa-file-image',
            'gif': 'fa-file-image',
            'zip': 'fa-file-archive',
            'rar': 'fa-file-archive'
        };
        return iconMap[extension] || 'fa-file';
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { month: 'short', day: 'numeric', year: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }
</script>

<?php
// Create the document card include file if it doesn't exist
$document_card_path = 'includes/document_card.php';
if (!file_exists($document_card_path)) {
    $document_card_content = <<<EOT
<div class="download-card<?php echo \$doc['featured'] ? ' featured' : ''; ?>">
    <?php if (\$doc['featured']): ?>
        <div class="featured-badge">Featured</div>
    <?php endif; ?>
    <div class="download-icon">
        <?php
        // Get file type icon
        \$file_extension = pathinfo(\$doc['original_file_name'], PATHINFO_EXTENSION);
        \$file_icon = 'fa-file';
        switch(strtolower(\$file_extension)) {
            case 'pdf': \$file_icon = 'fa-file-pdf'; break;
            case 'doc':
            case 'docx': \$file_icon = 'fa-file-word'; break;
            case 'xls':
            case 'xlsx': \$file_icon = 'fa-file-excel'; break;
            case 'ppt':
            case 'pptx': \$file_icon = 'fa-file-powerpoint'; break;
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif': \$file_icon = 'fa-file-image'; break;
            case 'zip':
            case 'rar': \$file_icon = 'fa-file-archive'; break;
        }
        ?>
        <i class="fas <?php echo \$file_icon; ?>"></i>
    </div>
    
    <?php if (!isset(\$category_slug) || empty(\$category_slug)): ?>
    <div class="category-tag">
        <i class="fas <?php echo \$doc['category_icon']; ?>"></i> 
        <?php echo htmlspecialchars(\$doc['category_name']); ?>
    </div>
    <?php endif; ?>
    
    <div class="download-info">
        <h3><?php echo htmlspecialchars(\$doc['title']); ?></h3>
        <p><?php echo htmlspecialchars(\$doc['description']); ?></p>
        <div class="download-meta">
            <span><i class="fas fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime(\$doc['created_at'])); ?></span>
            <span><i class="fas fa-file-alt"></i> <?php echo formatFileSize(\$doc['file_size']); ?></span>
            <span><i class="fas fa-download"></i> <?php echo \$doc['download_count']; ?> downloads</span>
        </div>
    </div>
    <a href="download_handler.php?file=<?php echo \$doc['id']; ?>" class="download-btn">
        <i class="fas fa-download"></i> Download
    </a>
</div>
EOT;

    // Make sure the includes directory exists
    if (!is_dir('includes')) {
        mkdir('includes', 0755, true);
    }
    
    // Write the document card template file
    file_put_contents($document_card_path, $document_card_content);
}

// Include footer from includes directory
include_once 'includes/footer.php';
?>