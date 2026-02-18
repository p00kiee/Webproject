<?php
// esewa_success.php
declare(strict_types=1);
session_start();
require_once 'config/database.php'; // must expose $pdo (PDO) with ERRMODE_EXCEPTION

// ---- eSewa ePay v2 (TEST) status endpoint ----
// Make sure these match what you used in checkout.php
$product_code = 'EPAYTEST'; // test merchant code (product_code)
$status_url   = 'https://rc.esewa.com.np/api/epay/transaction/status/';

// We stored these before redirecting to eSewa
$order_id = isset($_SESSION['pending_order_id']) ? (int)$_SESSION['pending_order_id'] : 0;
$total    = isset($_SESSION['pending_order_total']) ? (string)$_SESSION['pending_order_total'] : '';
$uuid     = isset($_SESSION['pending_txn_uuid']) ? (string)$_SESSION['pending_txn_uuid'] : '';
$items    = isset($_SESSION['pending_order_items']) ? $_SESSION['pending_order_items'] : [];

if ($order_id <= 0 || $total === '' || $uuid === '') {
    die('<h2>Missing payment context. Please contact support.</h2>');
}

// ---- Call eSewa status API (GET) ----
$query = http_build_query([
    'product_code'     => $product_code,
    'total_amount'     => $total,
    'transaction_uuid' => $uuid,
]);

$ch = curl_init($status_url . '?' . $query);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($response === false) {
    die('Payment verification failed: ' . htmlspecialchars($curlErr));
}

$data = json_decode($response, true);
if (!is_array($data)) {
    echo "<h2>Invalid response from eSewa</h2>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    exit;
}

// eSewa test returns status "COMPLETE" when payment is verified.
// Some docs show "SUCCESS" — accept both to be safe.
$status = strtoupper((string)($data['status'] ?? ''));
if (!in_array($status, ['COMPLETE', 'SUCCESS'], true)) {
    echo "<h2>Payment not completed</h2>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    exit;
}

// ---- Finalize order: insert items & update stock ----
try {
    if (!is_array($items) || empty($items)) {
        throw new Exception('Order items missing in session.');
    }

    $pdo->beginTransaction();

    $stmtItem  = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmtStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

    foreach ($items as $it) {
        $stmtItem->execute([$order_id, (int)$it['product_id'], (int)$it['quantity'], (float)$it['price']]);
        $stmtStock->execute([(int)$it['quantity'], (int)$it['product_id']]);
    }

    // Optional: tracking entry
    try {
        $pdo->prepare("
            INSERT INTO order_tracking (order_id, status, description)
            VALUES (?, 'Payment Received', 'eSewa payment verified. Order confirmed.')
        ")->execute([$order_id]);
    } catch (PDOException $e) {
        // tracking table may not exist — ignore
    }

    $pdo->commit();

    // ---- Cleanup session ----
    unset(
        $_SESSION['cart'],
        $_SESSION['pending_order_id'],
        $_SESSION['pending_order_total'],
        $_SESSION['pending_order_items'],
        $_SESSION['pending_txn_uuid']
    );

    // ---- Simple confirmation page ----
    $paid = number_format((float)($data['total_amount'] ?? $total), 2);
    $ref  = htmlspecialchars((string)($data['ref_id'] ?? ''));
    $oid  = htmlspecialchars((string)$order_id);

    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Order Confirmed</title>
    <style>
      body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,'Helvetica Neue',Arial,sans-serif;
           max-width:700px;margin:60px auto;padding:0 20px;background:#f9fafb}
      .card{border:1px solid #e5e7eb;border-radius:12px;padding:24px;background:#fff;
            box-shadow:0 4px 20px rgba(0,0,0,.06)}
      h1{margin:0 0 8px 0}
      .ok{color:#16a34a;font-weight:700}
      a{color:#2563eb;text-decoration:none}
      a:hover{text-decoration:underline}
    </style></head><body>
      <div class='card'>
        <h1 class='ok'>✅ Order Confirmed</h1>
        <p>Your eSewa payment was verified successfully.</p>
        <p><strong>Order ID:</strong> {$oid}<br>
           <strong>Paid Amount:</strong> Rs. {$paid}<br>
           <strong>Reference:</strong> {$ref}</p>
        <p><a href='confirmation?order_id={$oid}'>View order details</a></p>
      </div>
    </body></html>";

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log($e->getMessage());
    $safeOid  = htmlspecialchars((string)$order_id);
    $safeUuid = htmlspecialchars($uuid);
    echo "<h2>We received your payment, but finalizing the order failed.</h2>
          <p>Please contact support with Order ID <strong>{$safeOid}</strong> and Transaction UUID <strong>{$safeUuid}</strong>.</p>";
}
