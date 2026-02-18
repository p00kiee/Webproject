<?php
// Set page title
$page_title = "Downloads Management";

// Include admin header
include_once '../includes/admin_header.php';

// Check if user is logged in and has admin rights
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Connect to database
require_once '../config/database.php';

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // DELETE document
    if (isset($_POST['delete_document'])) {
        $document_id = $_POST['document_id'];
        
        // Get file name to delete
        $stmt = $pdo->prepare("SELECT file_name FROM documents WHERE id = ?");
        $stmt->execute([$document_id]);
        $doc = $stmt->fetch();
        
        if ($doc) {
            $file_path = '../uploads/documents/' . $doc['file_name'];
            if (file_exists($file_path)) {
                unlink($file_path); // Delete the file
            }
            
            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM documents WHERE id = ?");
            if ($stmt->execute([$document_id])) {
                $success_message = "Document deleted successfully.";
            } else {
                $error_message = "Error deleting document.";
            }
        }
    }
    
    // ADD or UPDATE document
    if (isset($_POST['save_document'])) {
        $document_id = isset($_POST['document_id']) ? $_POST['document_id'] : null;
        $category_id = $_POST['category_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $featured = isset($_POST['featured']) ? 1 : 0;
        $active = isset($_POST['active']) ? 1 : 0;
        
        // File upload handling
        $file_uploaded = false;
        $file_name = '';
        $original_file_name = '';
        $file_size = 0;
        $file_type = '';
        
        if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
            $original_file_name = $_FILES['document_file']['name'];
            $file_size = $_FILES['document_file']['size'];
            $file_type = $_FILES['document_file']['type'];
            $temp_file = $_FILES['document_file']['tmp_name'];
            
            // Generate a unique filename
            $file_extension = pathinfo($original_file_name, PATHINFO_EXTENSION);
            $file_name = uniqid() . '_' . time() . '.' . $file_extension;
            
            // Create uploads directory if it doesn't exist
            $upload_dir = '../uploads/documents/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Move the file to the uploads directory
            if (move_uploaded_file($temp_file, $upload_dir . $file_name)) {
                $file_uploaded = true;
            } else {
                $error_message = "Error uploading file.";
            }
        }
        
        // Add or update document in the database
        if (!$document_id) {
            // New document - insert
            if ($file_uploaded) {
                $stmt = $pdo->prepare("INSERT INTO documents (category_id, title, description, file_name, original_file_name, file_size, file_type, featured, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$category_id, $title, $description, $file_name, $original_file_name, $file_size, $file_type, $featured, $active])) {
                    $success_message = "Document added successfully.";
                } else {
                    $error_message = "Error adding document.";
                }
            } else {
                $error_message = "File upload is required for new documents.";
            }
        } else {
            // Update existing document
            if ($file_uploaded) {
                // If a new file was uploaded, update all fields including file info
                
                // First, get the old file to delete it
                $stmt = $pdo->prepare("SELECT file_name FROM documents WHERE id = ?");
                $stmt->execute([$document_id]);
                $old_doc = $stmt->fetch();
                
                if ($old_doc && file_exists('../uploads/documents/' . $old_doc['file_name'])) {
                    unlink('../uploads/documents/' . $old_doc['file_name']);
                }
                
                // Update with new file info
                $stmt = $pdo->prepare("UPDATE documents SET category_id = ?, title = ?, description = ?, file_name = ?, original_file_name = ?, file_size = ?, file_type = ?, featured = ?, active = ? WHERE id = ?");
                if ($stmt->execute([$category_id, $title, $description, $file_name, $original_file_name, $file_size, $file_type, $featured, $active, $document_id])) {
                    $success_message = "Document updated successfully.";
                } else {
                    $error_message = "Error updating document.";
                }
            } else {
                // Update without changing the file
                $stmt = $pdo->prepare("UPDATE documents SET category_id = ?, title = ?, description = ?, featured = ?, active = ? WHERE id = ?");
                if ($stmt->execute([$category_id, $title, $description, $featured, $active, $document_id])) {
                    $success_message = "Document updated successfully.";
                } else {
                    $error_message = "Error updating document.";
                }
            }
        }
    }
    
    // CATEGORY OPERATIONS
    
    // Delete category
    if (isset($_POST['delete_category'])) {
        $category_id = $_POST['category_id'];
        
        // Check if category has documents
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $doc_count = $stmt->fetchColumn();
        
        if ($doc_count > 0) {
            $error_message = "Cannot delete category. Delete all documents in this category first.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM download_categories WHERE id = ?");
            if ($stmt->execute([$category_id])) {
                $success_message = "Category deleted successfully.";
            } else {
                $error_message = "Error deleting category.";
            }
        }
    }
    
    // Add or update category
    if (isset($_POST['save_category'])) {
        $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : null;
        $name = $_POST['name'];
        $slug = strtolower(str_replace(' ', '-', $name));
        $description = $_POST['description'];
        $icon = $_POST['icon'];
        $active = isset($_POST['active']) ? 1 : 0;
        
        if (!$category_id) {
            // New category
            $stmt = $pdo->prepare("INSERT INTO download_categories (name, slug, description, icon, active) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $slug, $description, $icon, $active])) {
                $success_message = "Category added successfully.";
            } else {
                $error_message = "Error adding category.";
            }
        } else {
            // Update category
            $stmt = $pdo->prepare("UPDATE download_categories SET name = ?, slug = ?, description = ?, icon = ?, active = ? WHERE id = ?");
            if ($stmt->execute([$name, $slug, $description, $icon, $active, $category_id])) {
                $success_message = "Category updated successfully.";
            } else {
                $error_message = "Error updating category.";
            }
        }
    }
}

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$type = isset($_GET['type']) ? $_GET['type'] : 'documents';
$item_id = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch categories for document form
$categories = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM download_categories WHERE active = 1 ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Error fetching categories.";
}

// Get item data for editing
$item_data = null;
if ($item_id && ($action === 'edit' || $action === 'view')) {
    try {
        if ($type === 'documents') {
            $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ?");
            $stmt->execute([$item_id]);
            $item_data = $stmt->fetch(PDO::FETCH_ASSOC);
        } elseif ($type === 'categories') {
            $stmt = $pdo->prepare("SELECT * FROM download_categories WHERE id = ?");
            $stmt->execute([$item_id]);
            $item_data = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch(PDOException $e) {
        $error_message = "Error fetching item data.";
    }
}
?>

<div class="admin-container">
    <!-- Sidebar included from admin_header.php -->
    
    <div class="admin-content">
        <div class="admin-header">
            <h1><?php echo $page_title; ?></h1>
            
            <?php if ($action === 'list'): ?>
            <div class="action-buttons">
                <?php if ($type === 'documents'): ?>
                <a href="?action=add&type=documents" class="btn btn-primary"><i class="fas fa-plus"></i> Add Document</a>
                <a href="?type=categories" class="btn btn-secondary"><i class="fas fa-folder"></i> Manage Categories</a>
                <?php else: ?>
                <a href="?action=add&type=categories" class="btn btn-primary"><i class="fas fa-plus"></i> Add Category</a>
                <a href="?type=documents" class="btn btn-secondary"><i class="fas fa-file"></i> Manage Documents</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="admin-body">
            <?php if ($action === 'list' && $type === 'documents'): ?>
                <!-- Documents List -->
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th width="50">ID</th>
                                <th width="60">Type</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th width="100">Size</th>
                                <th width="90">Downloads</th>
                                <th width="80">Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT d.*, c.name as category_name FROM documents d 
                                                     JOIN download_categories c ON d.category_id = c.id 
                                                     ORDER BY d.created_at DESC");
                                $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($documents) === 0): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No documents found.</td>
                                    </tr>
                                <?php else:
                                    foreach ($documents as $doc): 
                                        // Format file size
                                        $file_size = $doc['file_size'];
                                        if ($file_size >= 1048576) {
                                            $file_size = number_format($file_size / 1048576, 2) . ' MB';
                                        } elseif ($file_size >= 1024) {
                                            $file_size = number_format($file_size / 1024, 2) . ' KB';
                                        } else {
                                            $file_size = $file_size . ' bytes';
                                        }
                                        
                                        // Get file type icon
                                        $file_extension = pathinfo($doc['original_file_name'], PATHINFO_EXTENSION);
                                        $file_icon = 'fa-file';
                                        switch(strtolower($file_extension)) {
                                            case 'pdf': $file_icon = 'fa-file-pdf'; break;
                                            case 'doc':
                                            case 'docx': $file_icon = 'fa-file-word'; break;
                                            case 'xls':
                                            case 'xlsx': $file_icon = 'fa-file-excel'; break;
                                            case 'ppt':
                                            case 'pptx': $file_icon = 'fa-file-powerpoint'; break;
                                            case 'jpg':
                                            case 'jpeg':
                                            case 'png':
                                            case 'gif': $file_icon = 'fa-file-image'; break;
                                            case 'zip':
                                            case 'rar': $file_icon = 'fa-file-archive'; break;
                                        }
                                    ?>
                                    <tr>
                                        <td><?php echo $doc['id']; ?></td>
                                        <td><i class="fas <?php echo $file_icon; ?> fa-lg"></i></td>
                                        <td>
                                            <?php echo htmlspecialchars($doc['title']); ?>
                                            <?php if($doc['featured']): ?><span class="badge badge-featured">Featured</span><?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($doc['category_name']); ?></td>
                                        <td><?php echo $file_size; ?></td>
                                        <td><?php echo $doc['download_count']; ?></td>
                                        <td>
                                            <?php if($doc['active']): ?>
                                                <span class="badge badge-active">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-inactive">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="actions">
                                            <a href="?action=view&type=documents&id=<?php echo $doc['id']; ?>" class="btn-icon btn-view" title="View"><i class="fas fa-eye"></i></a>
                                            <a href="?action=edit&type=documents&id=<?php echo $doc['id']; ?>" class="btn-icon btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                            <form method="post" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                <input type="hidden" name="document_id" value="<?php echo $doc['id']; ?>">
                                                <button type="submit" name="delete_document" class="btn-icon btn-delete" title="Delete"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach;
                                endif;
                            } catch(PDOException $e) {
                                echo '<tr><td colspan="8" class="text-center">Error fetching documents: ' . $e->getMessage() . '</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($action === 'list' && $type === 'categories'): ?>
                <!-- Categories List -->
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th width="50">ID</th>
                                <th width="60">Icon</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th width="80">Documents</th>
                                <th width="80">Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM documents WHERE category_id = c.id) as doc_count 
                                                     FROM download_categories c ORDER BY c.name");
                                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($categories) === 0): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No categories found.</td>
                                    </tr>
                                <?php else:
                                    foreach ($categories as $cat): ?>
                                    <tr>
                                        <td><?php echo $cat['id']; ?></td>
                                        <td><i class="fas <?php echo $cat['icon']; ?> fa-lg"></i></td>
                                        <td><?php echo htmlspecialchars($cat['name']); ?></td>
                                        <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                                        <td class="text-center"><?php echo $cat['doc_count']; ?></td>
                                        <td>
                                            <?php if($cat['active']): ?>
                                                <span class="badge badge-active">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-inactive">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="actions">
                                            <a href="?action=edit&type=categories&id=<?php echo $cat['id']; ?>" class="btn-icon btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                            <?php if($cat['doc_count'] == 0): ?>
                                            <form method="post" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                <input type="hidden" name="category_id" value="<?php echo $cat['id']; ?>">
                                                <button type="submit" name="delete_category" class="btn-icon btn-delete" title="Delete"><i class="fas fa-trash"></i></button>
                                            </form>
                                            <?php else: ?>
                                            <button type="button" class="btn-icon btn-delete disabled" title="Cannot delete - contains documents" disabled><i class="fas fa-trash"></i></button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach;
                                endif;
                            } catch(PDOException $e) {
                                echo '<tr><td colspan="7" class="text-center">Error fetching categories: ' . $e->getMessage() . '</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <?php if ($type === 'documents'): ?>
                    <!-- Document Form -->
                    <div class="admin-form-container">
                        <form method="post" enctype="multipart/form-data" class="admin-form">
                            <?php if ($action === 'edit' && $item_data): ?>
                                <input type="hidden" name="document_id" value="<?php echo $item_data['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="title">Title <span class="required">*</span></label>
                                <input type="text" id="title" name="title" value="<?php echo isset($item_data['title']) ? htmlspecialchars($item_data['title']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="category_id">Category <span class="required">*</span></label>
                                <select id="category_id" name="category_id" required>
                                    <option value="">Select a category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo (isset($item_data['category_id']) && $item_data['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="4"><?php echo isset($item_data['description']) ? htmlspecialchars($item_data['description']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="document_file">
                                    <?php echo ($action === 'add') ? 'Document File <span class="required">*</span>' : 'Document File (leave empty to keep current file)'; ?>
                                </label>
                                <input type="file" id="document_file" name="document_file" <?php echo ($action === 'add') ? 'required' : ''; ?>>
                                <?php if ($action === 'edit' && isset($item_data['original_file_name'])): ?>
                                    <p class="form-help">Current file: <?php echo htmlspecialchars($item_data['original_file_name']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group checkbox-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="featured" <?php echo (isset($item_data['featured']) && $item_data['featured'] == 1) ? 'checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                    Featured Document
                                </label>
                            </div>
                            
                            <div class="form-group checkbox-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="active" <?php echo (!isset($item_data['active']) || $item_data['active'] == 1) ? 'checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                    Active
                                </label>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="save_document" class="btn btn-primary">Save Document</button>
                                <a href="?type=documents" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                    
                <?php elseif ($type === 'categories'): ?>
                    <!-- Category Form -->
                    <div class="admin-form-container">
                        <form method="post" class="admin-form">
                            <?php if ($action === 'edit' && $item_data): ?>
                                <input type="hidden" name="category_id" value="<?php echo $item_data['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="name">Name <span class="required">*</span></label>
                                <input type="text" id="name" name="name" value="<?php echo isset($item_data['name']) ? htmlspecialchars($item_data['name']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="3"><?php echo isset($item_data['description']) ? htmlspecialchars($item_data['description']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="icon">Icon <span class="required">*</span></label>
                                <div class="icon-selector">
                                    <input type="text" id="icon" name="icon" value="<?php echo isset($item_data['icon']) ? htmlspecialchars($item_data['icon']) : 'fa-file'; ?>" required>
                                    <div class="selected-icon">
                                        <i class="fas <?php echo isset($item_data['icon']) ? htmlspecialchars($item_data['icon']) : 'fa-file'; ?>"></i>
                                    </div>
                                </div>
                                <div class="icon-options">
                                    <div class="icon-option" data-icon="fa-file"><i class="fas fa-file"></i></div>
                                    <div class="icon-option" data-icon="fa-file-pdf"><i class="fas fa-file-pdf"></i></div>
                                    <div class="icon-option" data-icon="fa-file-word"><i class="fas fa-file-word"></i></div>
                                    <div class="icon-option" data-icon="fa-file-excel"><i class="fas fa-file-excel"></i></div>
                                    <div class="icon-option" data-icon="fa-file-image"><i class="fas fa-file-image"></i></div>
                                    <div class="icon-option" data-icon="fa-file-archive"><i class="fas fa-file-archive"></i></div>
                                    <div class="icon-option" data-icon="fa-book"><i class="fas fa-book"></i></div>
                                    <div class="icon-option" data-icon="fa-book-open"><i class="fas fa-book-open"></i></div>
                                    <div class="icon-option" data-icon="fa-clipboard"><i class="fas fa-clipboard"></i></div>
                                    <div class="icon-option" data-icon="fa-clipboard-list"><i class="fas fa-clipboard-list"></i></div>
                                    <div class="icon-option" data-icon="fa-folder"><i class="fas fa-folder"></i></div>
                                    <div class="icon-option" data-icon="fa-folder-open"><i class="fas fa-folder-open"></i></div>
                                </div>
                            </div>
                            
                            <div class="form-group checkbox-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" name="active" <?php echo (!isset($item_data['active']) || $item_data['active'] == 1) ? 'checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                    Active
                                </label>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="save_category" class="btn btn-primary">Save Category</button>
                                <a href="?type=categories" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
                
            <?php elseif ($action === 'view' && $type === 'documents' && $item_data): ?>
                <!-- Document View -->
                <div class="document-view">
                    <div class="view-header">
                        <h2><?php echo htmlspecialchars($item_data['title']); ?></h2>
                        <div class="view-actions">
                            <a href="?action=edit&type=documents&id=<?php echo $item_data['id']; ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                            <a href="?type=documents" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
                        </div>
                    </div>
                    
                    <div class="view-body">
                        <div class="view-section">
                            <div class="view-label">Category:</div>
                            <div class="view-value">
                                <?php 
                                    $stmt = $pdo->prepare("SELECT name FROM download_categories WHERE id = ?");
                                    $stmt->execute([$item_data['category_id']]);
                                    $category = $stmt->fetchColumn();
                                    echo htmlspecialchars($category);
                                ?>
                            </div>
                        </div>
                        
                        <div class="view-section">
                            <div class="view-label">Description:</div>
                            <div class="view-value"><?php echo nl2br(htmlspecialchars($item_data['description'])); ?></div>
                        </div>
                        
                        <div class="view-section">
                            <div class="view-label">File:</div>
                            <div class="view-value">
                                <a href="../download_handler.php?file=<?php echo $item_data['id']; ?>" target="_blank">
                                    <i class="fas fa-download"></i> 
                                    <?php echo htmlspecialchars($item_data['original_file_name']); ?>
                                </a>
                                <span class="file-meta">
                                    (<?php 
                                        $file_size = $item_data['file_size'];
                                        if ($file_size >= 1048576) {
                                            echo number_format($file_size / 1048576, 2) . ' MB';
                                        } elseif ($file_size >= 1024) {
                                            echo number_format($file_size / 1024, 2) . ' KB';
                                        } else {
                                            echo $file_size . ' bytes';
                                        }
                                    ?>)
                                </span>
                            </div>
                        </div>
                        
                        <div class="view-section">
                            <div class="view-label">Status:</div>
                            <div class="view-value">
                                <?php if($item_data['active']): ?>
                                    <span class="badge badge-active">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inactive</span>
                                <?php endif; ?>
                                
                                <?php if($item_data['featured']): ?>
                                    <span class="badge badge-featured">Featured</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="view-section">
                            <div class="view-label">Downloads:</div>
                            <div class="view-value"><?php echo $item_data['download_count']; ?> times</div>
                        </div>
                        
                        <div class="view-section">
                            <div class="view-label">Date Added:</div>
                            <div class="view-value"><?php echo date('F j, Y, g:i a', strtotime($item_data['created_at'])); ?></div>
                        </div>
                        
                        <div class="view-section">
                            <div class="view-label">Last Updated:</div>
                            <div class="view-value"><?php echo date('F j, Y, g:i a', strtotime($item_data['updated_at'])); ?></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Admin Downloads Manager Styles */
.admin-container {
    display: flex;
    min-height: calc(100vh - 70px);
}

.admin-content {
    flex: 1;
    padding: 20px;
    background-color: #f8fafc;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e2e8f0;
}

.admin-header h1 {
    font-size: 1.75rem;
    color: #1e40af;
    margin: 0;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}

.btn-primary {
    background-color: #2563eb;
    color: white;
}

.btn-primary:hover {
    background-color: #1d4ed8;
    transform: translateY(-1px);
}

.btn-secondary {
    background-color: #e2e8f0;
    color: #475569;
}

.btn-secondary:hover {
    background-color: #cbd5e1;
    transform: translateY(-1px);
}

.alert {
    padding: 12px 16px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background-color: #dcfce7;
    color: #166534;
    border: 1px solid #86efac;
}

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* Table Styles */
.table-responsive {
    overflow-x: auto;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th,
.admin-table td {
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.admin-table th {
    background-color: #f8fafc;
    font-weight: 600;
    color: #475569;
}

.admin-table tbody tr:hover {
    background-color: #f1f5f9;
}

.admin-table .text-center {
    text-align: center;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-active {
    background-color: #dcfce7;
    color: #166534;
}

.badge-inactive {
    background-color: #f1f5f9;
    color: #64748b;
}

.badge-featured {
    background-color: #ede9fe;
    color: #5b21b6;
    margin-left: 5px;
}

.actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 4px;
    border: none;
    background-color: transparent;
    color: #475569;
    transition: all 0.2s;
    cursor: pointer;
}

.btn-view:hover {
    background-color: #e0f2fe;
    color: #0284c7;
}

.btn-edit:hover {
    background-color: #e0f2fe;
    color: #0284c7;
}

.btn-delete:hover {
    background-color: #fee2e2;
    color: #dc2626;
}

.btn-delete.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.delete-form {
    display: inline;
}

/* Form Styles */
.admin-form-container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    padding: 24px;
}

.admin-form {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    max-width: 800px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-weight: 600;
    color: #1e293b;
}

.form-group input[type="text"],
.form-group input[type="file"],
.form-group select,
.form-group textarea {
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 0.95rem;
    width: 100%;
}

.form-group textarea {
    min-height: 100px;
    resize: vertical;
}

.form-help {
    font-size: 0.85rem;
    color: #64748b;
    margin-top: 4px;
}

.checkbox-group {
    margin-top: 10px;
}

.checkbox-container {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    user-select: none;
}

.required {
    color: #dc2626;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

/* Icon Selector */
.icon-selector {
    display: flex;
    gap: 10px;
    align-items: center;
}

.selected-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background-color: #f1f5f9;
    border-radius: 4px;
    font-size: 1.2rem;
}

.icon-options {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
    gap: 8px;
    margin-top: 10px;
}

.icon-option {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background-color: #f1f5f9;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.icon-option:hover {
    background-color: #e0f2fe;
    transform: translateY(-2px);
}

.icon-option.selected {
    background-color: #bfdbfe;
    color: #2563eb;
}

/* Document View Styles */
.document-view {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    padding: 24px;
}

.view-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e2e8f0;
}

.view-header h2 {
    font-size: 1.5rem;
    color: #1e40af;
    margin: 0;
}

.view-actions {
    display: flex;
    gap: 10px;
}

.view-body {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
}

.view-section {
    display: grid;
    grid-template-columns: 150px 1fr;
    gap: 20px;
    align-items: flex-start;
}

.view-label {
    font-weight: 600;
    color: #64748b;
}

.view-value {
    color: #1e293b;
}

.view-value a {
    color: #2563eb;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.view-value a:hover {
    text-decoration: underline;
}

.file-meta {
    color: #64748b;
    font-size: 0.9rem;
    margin-left: 10px;
}

@media (max-width: 768px) {
    .admin-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .view-section {
        grid-template-columns: 1fr;
        gap: 5px;
    }
    
    .view-label {
        margin-bottom: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Icon selector functionality
    const iconField = document.getElementById('icon');
    const selectedIcon = document.querySelector('.selected-icon i');
    const iconOptions = document.querySelectorAll('.icon-option');
    
    if (iconField && selectedIcon && iconOptions.length > 0) {
        // Initialize with current icon
        updateSelectedIcon(iconField.value);
        
        // Add click handlers to icon options
        iconOptions.forEach(option => {
            const iconClass = option.getAttribute('data-icon');
            if (iconField.value === iconClass) {
                option.classList.add('selected');
            }
            
            option.addEventListener('click', function() {
                const iconClass = this.getAttribute('data-icon');
                iconField.value = iconClass;
                updateSelectedIcon(iconClass);
                
                // Update selected state
                iconOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
    }
    
    function updateSelectedIcon(iconClass) {
        if (selectedIcon) {
            selectedIcon.className = 'fas ' + iconClass;
        }
    }
});
</script>

<?php
// Include admin footer
include_once '../includes/admin_footer.php';
?>