<?php
// ✅ Connect to database
$con = mysqli_connect('localhost', 'root', '', 'users');
if (!$con) {
  die("❌ Connection failed: " . mysqli_connect_error());
}

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // ✅ Terms and Conditions validation
  if (!isset($_POST['agree'])) {
    echo "<script>alert('❌ Please agree to the terms before signing up.'); window.history.back();</script>";
    exit;
  }

  // ✅ Collect form data
  $name             = $_POST['name'];
  $email            = $_POST['mail'];
  $phone            = $_POST['phone'];
  $password         = $_POST['pass'];
  $aadhaar          = $_POST['aadhaar_card_no'];
  $pan              = $_POST['pan_card_no'];
  $card             = $_POST['card_type'];

  // ✅ Server-side field validations
  if (!preg_match("/^[A-Za-z\s]+$/", $name)) {
    echo "<script>alert('❌ Name must contain only alphabets and cannot be blank.'); window.history.back();</script>";
    exit;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('❌ Invalid email format or blank email.'); window.history.back();</script>";
    exit;
  }

  // ✅ Check if email already exists
  $checkEmail = $con->prepare("SELECT email FROM mydata WHERE email = ?");
  $checkEmail->bind_param("s", $email);
  $checkEmail->execute();
  $checkEmail->store_result();

  if ($checkEmail->num_rows > 0) {
    echo "<script>alert('❌ Email already used. Please login.'); window.location.href='login.html';</script>";
    $checkEmail->close();
    $con->close();
    exit;
  }
  $checkEmail->close();

  if (!preg_match("/^\d{10}$/", $phone)) {
    echo "<script>alert('❌ Phone must be exactly 10 digits and cannot be blank.'); window.history.back();</script>";
    exit;
  }

  if (empty($password)) {
    echo "<script>alert('❌ Password cannot be blank.'); window.history.back();</script>";
    exit;
  }

  if (!preg_match("/^\d{12}$/", $aadhaar)) {
    echo "<script>alert('❌ Aadhaar must be exactly 12 digits and cannot be blank.'); window.history.back();</script>";
    exit;
  }

  if (!preg_match("/^[A-Z]{5}[0-9]{4}[A-Z]$/", $pan)) {
    echo "<script>alert('❌ PAN must be 10 characters (5 letters, 4 digits, 1 letter) and cannot be blank.'); window.history.back();</script>";
    exit;
  }


  // ✅ Card-specific fields
  $ageGroup         = $_POST['age_group'] ?? '';
  $familyOpt        = $_POST['family_opt'] ?? '';
  $matureFamilyOpt  = $_POST['mature_family_opt'] ?? '';
  $describeAge      = $_POST['describe_age'] ?? '';
  $familyMembers    = $_POST['family_members_selected'] ?? '';
  $rxCardOpt        = $_POST['rxmedo_card_family_opt'] ?? '';
  $rxCardMembers    = $_POST['rxmedo_card_family_members'] ?? '';
  $rxTopUpOpt       = $_POST['rxmedo_topup_family_opt'] ?? '';
  $rxTopUpMembers   = $_POST['rxmedo_topup_family_members'] ?? '';
  $upgradeAmount    = $_POST['upgrade_amount'] ?? '';
  $membersBelow25   = '';
  $membersAbove25   = '';

  // ✅ RX Medo Card Validation
  if ($card === "RX Medo Card") {
    if (!$rxCardOpt) {
      echo "<script>alert('❌ Please select Individual or Family under RX Medo Card.'); window.history.back();</script>";
      exit;
    }
    if ($rxCardOpt === "Family" && !$rxCardMembers) {
      echo "<script>alert('❌ Please select family members under RX Medo Card.'); window.history.back();</script>";
      exit;
    }
    if ($rxCardOpt === "Individual" && $rxCardMembers) {
      echo "<script>alert('❌ Family members selection is only allowed when Family is selected under RX Medo Card.'); window.history.back();</script>";
      exit;
    }
  }

  // ✅ RX Medo Insure Card Validation
  if ($card === "RX Medo Insure Card") {
    if (!$ageGroup) {
      echo "<script>alert('❌ Please select your age group for RX Medo Insure Card.'); window.history.back();</script>";
      exit;
    }
    if (!$familyOpt && !$matureFamilyOpt) {
      echo "<script>alert('❌ Please select Individual or Family under \"Want to opt for your family?\"'); window.history.back();</script>";
      exit;
    }
    if (!$familyOpt) {
      $familyOpt = $matureFamilyOpt;
    }
    if ($familyOpt === "Family") {
      if ($ageGroup === "Young India (18-35)" && !$describeAge) {
        echo "<script>alert('❌ Please describe your age under Young India.'); window.history.back();</script>";
        exit;
      }
      if (!$familyMembers) {
        echo "<script>alert('❌ Please select family members based on your age group.'); window.history.back();</script>";
        exit;
      }
    }
    if ($familyOpt === "Individual" && ($describeAge || $familyMembers)) {
      echo "<script>alert('❌ Age and family members selection is only allowed when Family is selected.'); window.history.back();</script>";
      exit;
    }
  }

  // ✅ RX Medo Insure Top Up Card Validation
  if ($card === "RX Medo Insure Top Up Card") {
    if (!$upgradeAmount) {
      echo "<script>alert('❌ Please select an upgrade amount.'); window.history.back();</script>";
      exit;
    }
  }

  // ✅ RX Medo Top Up Card Validation
  if ($card === "RX Medo Top Up Card") {
    if (!$rxTopUpOpt) {
      echo "<script>alert('❌ Please select Individual or Family under RX Medo Top Up Card.'); window.history.back();</script>";
      exit;
    }
    if ($rxTopUpOpt === "Family" && !$rxTopUpMembers) {
      echo "<script>alert('❌ Please select family members under RX Medo Top Up Card.'); window.history.back();</script>";
      exit;
    }
    if ($rxTopUpOpt === "Individual" && $rxTopUpMembers) {
      echo "<script>alert('❌ Family members selection is only allowed when Family is selected under RX Medo Top Up Card.'); window.history.back();</script>";
      exit;
    }
  }

  // ✅ Normalize familyOpt and familyMembers
  $finalFamilyOpt = $card === "RX Medo Card" ? $rxCardOpt :
                    ($card === "RX Medo Top Up Card" ? $rxTopUpOpt : ($familyOpt ?? $matureFamilyOpt));

  $finalFamilyMembers = $card === "RX Medo Card" ? $rxCardMembers :
                        ($card === "RX Medo Top Up Card" ? $rxTopUpMembers : $familyMembers);

  // ✅ Handle optional file upload
  $insuranceCardPath = '';
  if (isset($_FILES['insuranceCardUpload']) && $_FILES['insuranceCardUpload']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0777, true);
    }
    $fileName = basename($_FILES['insuranceCardUpload']['name']);
    $targetPath = $uploadDir . time() . '_' . $fileName;
    if (move_uploaded_file($_FILES['insuranceCardUpload']['tmp_name'], $targetPath)) {
      $insuranceCardPath = $targetPath;
    }
  }

  // ✅ Prepare and execute insert query
  $stmt = $con->prepare("INSERT INTO mydata (
    name, email, phone, password, aadhaar_card_no, pan_card_no,
    card_type, age_group, family_opt, rxmedo_card_family_opt, describe_age,
    family_members_selected, family_members_below25, family_members_above25,
    upgrade_amount, insurance_card_photo, mature_family_opt
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $stmt->bind_param("sssssssssssssssss",
    $name, $email, $phone, $password, $aadhaar, $pan,
    $card, $ageGroup, $finalFamilyOpt, $rxCardOpt, $describeAge,
    $finalFamilyMembers, $membersBelow25, $membersAbove25,
    $upgradeAmount, $insuranceCardPath, $matureFamilyOpt
  );

    if ($stmt->execute()) {
    echo "<script>alert('✅ You have successfully signed up. Welcome to RX Medo Card!'); window.location.href='login.html';</script>";
  } else {
    echo "<script>alert('❌ Signup failed. Please try again.'); window.history.back();</script>";
  }

  $stmt->close();
  $con->close();
}
?>
