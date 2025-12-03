<?php
/*
require('vendor/autoload.php'); // ✅ Razorpay SDK via Composer

use Razorpay\Api\Api;

// ✅ Your Test API Credentials
$keyId = "rzp_test_Rcrn4lW5mVtwQO";
$keySecret = "z3zpKGvN6oj3liFwEoeKD21U";

// ✅ Initialize Razorpay API
$api = new Api($keyId, $keySecret);

// ✅ Order Details
$orderData = [
  'receipt' => 'RXMedo_' . rand(1000,9999),
  'amount' => 49900, // ₹499 in paise
  'currency' => 'INR',
  'payment_capture' => 1
];

// ✅ Create Order
$order = $api->order->create($orderData);

// ✅ Return Order ID to frontend
echo json_encode($order);
*/
?>

