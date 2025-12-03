<?php
require_once __DIR__ . '/razorpay_php/Razorpay.php';
use Razorpay\Api\Api;

$TEST_KEY_ID = "rzp_test_Rhbj2k3fw4TENL";
$TEST_KEY_SECRET = "CPoAmyFwig8LH8AdF9HmPC6H";

$con = new mysqli("localhost", "root", "", "users");
if ($con->connect_error) die("DB Connection failed");

$api = new Api($TEST_KEY_ID, $TEST_KEY_SECRET);

// ✅ Get Razorpay POST data
$orderId   = $_POST['razorpay_order_id'] ?? '';
$paymentId = $_POST['razorpay_payment_id'] ?? '';
$signature = $_POST['razorpay_signature'] ?? '';

try {
  // ✅ Verify signature
  $api->utility->verifyPaymentSignature([
    'razorpay_order_id'   => $orderId,
    'razorpay_payment_id' => $paymentId,
    'razorpay_signature'  => $signature
  ]);

  // ✅ Fetch mapping: user + all card fields + amount
  $stmt = $con->prepare("SELECT user_id, card_type, card_category, card_subcategory, age_group, amount FROM razorpay_orders WHERE order_id = ?");
  $stmt->bind_param("s", $orderId);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($res->num_rows === 0) {
    throw new Exception("Order mapping not found");
  }
  $row          = $res->fetch_assoc();
  $userId       = $row['user_id'];
  $cardType     = $row['card_type'];
  $cardCategory = $row['card_category'] ?? '';
  $cardSubcat   = $row['card_subcategory'] ?? '';
  $ageGroup     = $row['age_group'] ?? '';
  $amount       = $row['amount'];

  // ✅ Insert payment record (immutable timestamp)
  $status = "success";
  $pay = $con->prepare("INSERT INTO payments (user_id, order_id, payment_id, amount, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
  $pay->bind_param("issis", $userId, $orderId, $paymentId, $amount, $status);
  $pay->execute();

  // ✅ Activate user
  $upd = $con->prepare("UPDATE mydata SET status = 'active' WHERE id = ?");
  $upd->bind_param("i", $userId);
  $upd->execute();

  // ✅ Fetch user display details
  $userStmt = $con->prepare("SELECT name FROM mydata WHERE id = ?");
  $userStmt->bind_param("i", $userId);
  $userStmt->execute();
  $userData = $userStmt->get_result()->fetch_assoc();

  // ✅ Fetch payment display details (use created_at for validity)
  $payStmt = $con->prepare("SELECT payment_id, created_at FROM payments WHERE order_id = ? LIMIT 1");
  $payStmt->bind_param("s", $orderId);
  $payStmt->execute();
  $payData = $payStmt->get_result()->fetch_assoc();

  // ✅ Validity window
  $startDate = date("d-m-Y", strtotime($payData['created_at']));
  $endDate   = date("d-m-Y", strtotime("+1 year -1 day", strtotime($payData['created_at'])));
?>
<!DOCTYPE html>
<html>
<head>
  <title>Payment Receipt</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; max-width: 720px; margin: auto; border: 1px solid #e5e5e5; border-radius: 8px; }
    h2 { text-align: center; color: #2c3e50; margin-bottom: 20px; letter-spacing: 0.5px; }
    .field { margin: 8px 0; display: flex; }
    .label { font-weight: bold; width: 220px; color: #333; }
    .value { color: #111; }
    .hr { margin: 16px 0; border: 0; border-top: 1px dashed #bbb; }
    .footer { margin-top: 24px; text-align: center; font-size: 13px; color: #444; font-weight: bold; }
    .btnbar { text-align: center; margin-top: 18px; }
    .btn { display: inline-block; margin: 6px; padding: 10px 18px; background: #3399cc; color: #fff; text-decoration: none; border-radius: 5px; font-weight: 600; }
    .btn.secondary { background: #6c757d; }
  </style>
</head>
<body>
  <h2>RX MEDO PAYMENT RECEIPT</h2>

  <div class="field"><span class="label">Receipt No:</span><span class="value"><?php echo htmlspecialchars($payData['payment_id']); ?></span></div>
  <div class="field"><span class="label">Date:</span><span class="value"><?php echo htmlspecialchars($startDate); ?></span></div>
  <hr class="hr">

  <div class="field"><span class="label">Received From:</span><span class="value"><?php echo htmlspecialchars($userData['name']); ?></span></div>
  <div class="field"><span class="label">Card Type:</span><span class="value"><?php echo htmlspecialchars($cardType); ?></span></div>
  <?php if (!empty($cardCategory)) : ?>
    <div class="field"><span class="label">Category:</span><span class="value"><?php echo htmlspecialchars($cardCategory); ?></span></div>
  <?php endif; ?>
  <?php if (!empty($cardSubcat)) : ?>
    <div class="field"><span class="label">Subcategory:</span><span class="value"><?php echo htmlspecialchars($cardSubcat); ?></span></div>
  <?php endif; ?>
  <?php if (!empty($ageGroup)) : ?>
    <div class="field"><span class="label">Age Group:</span><span class="value"><?php echo htmlspecialchars($ageGroup); ?></span></div>
  <?php endif; ?>

  <div class="field"><span class="label">Amount:</span><span class="value">₹<?php echo number_format($amount / 100, 2); ?></span></div>
  <div class="field"><span class="label">Payment Mode:</span><span class="value">Online</span></div>
  <div class="field"><span class="label">Payment For:</span><span class="value">Membership Card</span></div>
  <div class="field"><span class="label">Validity:</span><span class="value"><?php echo htmlspecialchars($startDate); ?> to <?php echo htmlspecialchars($endDate); ?></span></div>

  <div class="footer">Thank you | RX Medical Trust and Services.</div>

  <div class="btnbar">
    <a href="download_receipt.php?order_id=<?php echo urlencode($orderId); ?>" class="btn">Download Receipt</a>
    <a href="login.html" class="btn secondary">Back to Login</a>
  </div>
</body>
</html>
<?php
  exit;

} catch (Exception $e) {
  echo "<script>alert('❌ Payment verification failed.'); window.location.href='signup.html';</script>";
}
?>
