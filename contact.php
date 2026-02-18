
<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// Handle form submission
$message_sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if ($name && $email && $message) {
        $query = "INSERT INTO contact_messages (name, email, message, created_at) VALUES (:name, :email, :message, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':name' => $name, ':email' => $email, ':message' => $message]);
        $message_sent = true;
    }
}
?>

<div class="contact-container" style="max-width: 600px; margin: 3rem auto; padding: 2rem; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); text-align: center;">
    <h1 style="margin-bottom: 1rem; color: #333;">Contact Us</h1>
    <p style="margin-bottom: 2rem; color: #666;">We'd love to hear from you! Drop us a message below.</p>

    <?php if ($message_sent): ?>
        <p class="success" style="color: #28a745; font-size: 1.1rem;">Thank you for reaching out! We will get back to you shortly.</p>
    <?php else: ?>
        <form action="contact.php" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            <div class="form-group">
                <label for="name" style="display: block; margin-bottom: 0.5rem; color: #555;">Name:</label>
                <input type="text" id="name" name="name" required style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group">
                <label for="email" style="display: block; margin-bottom: 0.5rem; color: #555;">Email:</label>
                <input type="email" id="email" name="email" required style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group">
                <label for="message" style="display: block; margin-bottom: 0.5rem; color: #555;">Message:</label>
                <textarea id="message" name="message" rows="5" required style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px;"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 0.8rem; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Send Message</button>
        </form>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
