<?php
session_start();

// ✅ Connect to database
$con = mysqli_connect('localhost', 'root', '', 'users');
if (!$con) {
  die("❌ Connection failed: " . mysqli_connect_error());
}

// ✅ Collect form data
$full_name     = trim($_POST['name'] ?? '');
$hospital_name = trim($_POST['hospital'] ?? '');
$patient_id    = trim($_POST['patient_id'] ?? '');
$contact       = trim($_POST['contact'] ?? '');
$date          = $_POST['date'] ?? '';
$card_number   = trim($_POST['card_number'] ?? '');
$description   = trim($_POST['description'] ?? '');

// ✅ Validation
$errors = [];

if (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
  $errors[] = "❌ Full Name must contain only alphabets.";
}

if (!preg_match("/^[a-zA-Z\s]+$/", $hospital_name)) {
  $errors[] = "❌ Hospital Name must contain only alphabets.";
}

if (!filter_var($patient_id, FILTER_VALIDATE_EMAIL)) {
  $errors[] = "❌ Patient ID must be a valid email.";
}

if (!preg_match("/^\d{10}$/", $contact)) {
  $errors[] = "❌ Contact Number must be 10 digits.";
}

if (empty($date)) {
  $errors[] = "❌ Appointment Date is required.";
}

// ✅ If errors exist, show alert
if (!empty($errors)) {
  $msg = implode("\\n", $errors);
  echo "<script>alert('$msg'); window.history.back();</script>";
  exit;
}

// ✅ Handle file upload
$receipt_path = '';
if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
  $uploadDir = 'uploads/';
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }
  $fileName = basename($_FILES['receipt']['name']);
  $targetPath = $uploadDir . time() . '_' . $fileName;
  if (move_uploaded_file($_FILES['receipt']['tmp_name'], $targetPath)) {
    $receipt_path = $targetPath;
  }
}

// ✅ Insert into database
$stmt = $con->prepare("INSERT INTO opd_services (
  full_name, hospital_name, patient_id, contact_number, appointment_date,
  card_number, receipt_path, description
) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssssssss", $full_name, $hospital_name, $patient_id, $contact, $date, $card_number, $receipt_path, $description);

if ($stmt->execute()) {
  echo "<script>alert('✅ Appointment submitted successfully! Thank you for booking your OPD appointment.'); window.location.href='opddash.html';</script>";
} else {
  echo "<script>alert('❌ Submission failed. Please try again.'); window.history.back();</script>";
}

$stmt->close();
$con->close();
?>
