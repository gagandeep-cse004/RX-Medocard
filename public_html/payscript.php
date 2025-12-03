<?php
require_once __DIR__ . '/razorpay_php/Razorpay.php';
use Razorpay\Api\Api;

$TEST_KEY_ID = "rzp_test_Rhbj2k3fw4TENL";
$TEST_KEY_SECRET = "CPoAmyFwig8LH8AdF9HmPC6H";

// ✅ Get user ID from URL
$userId = intval($_GET['user_id'] ?? 0);
if (!$userId) die("❌ Invalid user ID");

// ✅ Connect to DB
$con = new mysqli("localhost", "root", "", "users");
if ($con->connect_error) die("DB Connection failed");

// ✅ Fetch user details including all card fields
$stmt = $con->prepare("SELECT name, email, phone, card_type, card_category, card_subcategory, age_group FROM mydata WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) die("❌ User not found");
$user = $result->fetch_assoc();

$name          = $user['name'];
$email         = $user['email'];
$phone         = $user['phone'];
$cardType      = $user['card_type'];
$cardCategory  = $user['card_category'];
$cardSubcat    = $user['card_subcategory'];
$ageGroup      = $user['age_group'];

// ✅ Dynamic amount logic
if ($cardType === "RX Medo Card") {
  $amount = ($cardCategory === "Individual") ? 200000 : 300000;
}
elseif ($cardType === "RX Medo Insure Card") {
  if ($cardCategory === "Young India") {
    if ($cardSubcat === "Individual") {
      $amount = 400000;
    } elseif ($cardSubcat === "Family") {
      if ($ageGroup === "Below 25") {
        $amount = 800000;
      } elseif ($ageGroup === "Above 25") {
        $amount = 900000;
      } else {
        $amount = 49900;
      }
    } else {
      $amount = 49900;
    }
  } elseif ($cardCategory === "Mature Individual") {
    $amount = 600000;
  } elseif ($cardCategory === "Senior Citizens") {
    $amount = 700000;
  } else {
    $amount = 49900;
  }
}
elseif ($cardType === "RX Medo Top Up Card") {
  $amount = 200000;
}
elseif ($cardType === "RX Medo Insure Top Up Card") {
  $amount = 400000;
}
else {
  $amount = 49900;
}

// ✅ Create Razorpay order
$api = new Api($TEST_KEY_ID, $TEST_KEY_SECRET);
$orderData = [
  'receipt'         => 'signup_' . $userId . '_' . uniqid(),
  'amount'          => $amount,
  'currency'        => 'INR',
  'payment_capture' => 1,
  'notes'           => ['user_id' => (string)$userId]
];
$order   = $api->order->create($orderData);
$orderId = $order['id'];

// ✅ Save order mapping for receipt generation later
$map = $con->prepare("INSERT INTO razorpay_orders (order_id, user_id, card_type, card_category, card_subcategory, age_group, amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
$map->bind_param("sissssi", $orderId, $userId, $cardType, $cardCategory, $cardSubcat, $ageGroup, $amount);
$map->execute();
$map->close();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Payment</title>
  <style>
    body { font-family: Arial; padding: 20px; }
    h2 { color: #2c3e50; }
    .info { margin: 10px 0; }
  </style>
</head>
<body>
  <h2>Proceed with Payment</h2>
  <div class="info">User: <strong><?php echo htmlspecialchars($name); ?></strong> (<?php echo htmlspecialchars($email); ?>)</div>
  <div class="info">Card Type: <strong><?php echo htmlspecialchars($cardType); ?></strong></div>
  <div class="info">Category: <strong><?php echo htmlspecialchars($cardCategory); ?></strong></div>
  <?php if ($cardSubcat): ?>
  <div class="info">Subcategory: <strong><?php echo htmlspecialchars($cardSubcat); ?></strong></div>
  <?php endif; ?>
  <?php if ($ageGroup): ?>
  <div class="info">Age Group: <strong><?php echo htmlspecialchars($ageGroup); ?></strong></div>
  <?php endif; ?>
  <div class="info">Amount to Pay: <strong>₹<?php echo number_format($amount / 100, 2); ?></strong></div>
  <button id="rzp-button1">Pay Now</button>

  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
  var options = {
    "key": "<?php echo $TEST_KEY_ID; ?>",
    "amount": "<?php echo $amount; ?>",
    "currency": "INR",
    "name": "Rxmedo",
    "description": "Signup Payment",
    "order_id": "<?php echo $orderId; ?>",
    "callback_url": "payment_success.php",
    "prefill": {
      "name": "<?php echo htmlspecialchars($name); ?>",
      "email": "<?php echo htmlspecialchars($email); ?>",
      "contact": "<?php echo htmlspecialchars($phone); ?>"
    },
    "theme": { "color": "#3399cc" }
  };
  var rzp1 = new Razorpay(options);
  document.getElementById('rzp-button1').onclick = function(e) {
    rzp1.open();
    e.preventDefault();
  }
  </script>
</body>
</html>
