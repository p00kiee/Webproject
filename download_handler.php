<?php
// Start a new session or resume the existing one
session_start();

// This file handles the actual file download process - no includes, no output before headers
if (!isset($_GET['file'])) {
    // No file requested, redirect to downloads page
    header('Location: downloads.php');
    exit;
}

// Connect to database
require_once 'config/database.php';

// Get the file ID from the URL
$file_id = intval($_GET['file']);

try {
    // Get document information from the database
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ? AND active = 1");
    $stmt->execute([$file_id]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$document) {
        // Document not found or not active, redirect to downloads page with error
        header('Location: downloads.php?error=file_not_found');
        exit;
    }
    
    // File exists in database, check if the file exists on the server
    $file_path = 'uploads/documents/' . $document['file_name'];
    
    if (file_exists($file_path)) {
        // Update download count
        $stmt = $pdo->prepare("UPDATE documents SET download_count = download_count + 1 WHERE id = ?");
        $stmt->execute([$file_id]);
        
        // Log the download if desired
        if (isset($pdo)) {
            $user_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            
            $stmt = $pdo->prepare("INSERT INTO download_logs (document_id, user_id, ip_address, user_agent) 
                                 VALUES (?, ?, ?, ?)");
            $stmt->execute([$file_id, $user_id, $ip_address, $user_agent]);
        }
        
        // Set appropriate headers for file download
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $document['file_type']);
        header('Content-Disposition: attachment; filename="' . $document['original_file_name'] . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Pragma: public');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        
        // Read and output file
        readfile($file_path);
        exit;
    } else {
        // For demonstration - if file doesn't exist, check for a sample.pdf
        $sample_file_path = 'sample.pdf';
        
        if (file_exists($sample_file_path)) {
            // Update download count anyway
            $stmt = $pdo->prepare("UPDATE documents SET download_count = download_count + 1 WHERE id = ?");
            $stmt->execute([$file_id]);
            
            // Set appropriate headers for file download
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $document['original_file_name'] . '"');
            header('Content-Length: ' . filesize($sample_file_path));
            header('Pragma: public');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');
            
            // Read and output file
            readfile($sample_file_path);
            exit;
        } else {
            // No file found, redirect to downloads page with error
            header('Location: downloads.php?error=file_not_found');
            exit;
        }
    }
} catch(PDOException $e) {
    // Database error occurred
    header('Location: downloads.php?error=database_error');
    exit;
}

// If we get here, something went wrong
header('Location: downloads.php?error=unknown_error');
exit;
?>