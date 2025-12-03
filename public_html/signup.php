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
$ageGroup         = $_POST['age_group'] ?? null;
$familyOpt        = $_POST['family_opt'] ?? null;
$matureFamilyOpt  = $_POST['mature_family_opt'] ?? null;
$describeAge      = $_POST['describe_age'] ?? null;
$familyMembers    = $_POST['family_members_selected'] ?? null;
$membersBelow25   = $_POST['family_members_below25'] ?? null;
$membersAbove25   = $_POST['family_members_above25'] ?? null;
$upgradeAmount    = $_POST['upgrade_amount'] ?? null;
$rxFamilyOpt      = $_POST['rxmedo_family_opt'] ?? null;
// ✅ Optional file upload (insurance card)
$insuranceCardPath = null;
if (isset($_FILES['insurance_card_photo']) && $_FILES['insurance_card_photo']['error'] === UPLOAD_ERR_OK) {
  $uploadDir = 'uploads/';
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }
  $fileName = basename($_FILES['insurance_card_photo']['name']);
  $targetPath = $uploadDir . time() . '_' . $fileName;
  if (move_uploaded_file($_FILES['insurance_card_photo']['tmp_name'], $targetPath)) {
    $insuranceCardPath = $targetPath;
  }
}

// ✅ Prepare and execute insert query (matching all columns except ID)
$stmt = $con->prepare("INSERT INTO mydata (
  name, email, phone, password, aadhaar_card_no, pan_card_no,
  card_type, age_group, family_opt, rxmedo_family_opt, describe_age,
  family_members_selected, family_members_below25, family_members_above25,
  upgrade_amount, insurance_card_photo, mature_family_opt
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("sssssssssssssssss",
  $name, $email, $phone, $password, $aadhaar, $pan,
  $card, $ageGroup, $familyOpt, $rxFamilyOpt, $describeAge,
  $familyMembers, $membersBelow25, $membersAbove25,
  $upgradeAmount, $insuranceCardPath, $matureFamilyOpt
);

if ($stmt->execute()) {
  $newUserId = $stmt->insert_id; // ✅ Correct way to get inserted ID
  echo "<script>
    alert('✅ You have successfully signed up. Please proceed with payment!');
    window.location.href='payscript.php?user_id={$newUserId}';
  </script>";
  exit;
} else {
  echo "<script>
    alert('❌ Signup failed. Please try again.');
    window.history.back();
  </script>";
}


$stmt->close();
$con->close();
}
?>

  // ✅ Handle optional file upload
  $insuranceCardPath = null;
  if (isset($_FILES['insurance_card_photo']) && $_FILES['insurance_card_photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0777, true);
    }
    $fileName = basename($_FILES['insurance_card_photo']['name']);
    $targetPath = $uploadDir . time() . '_' . $fileName;
    if (move_uploaded_file($_FILES['insurance_card_photo']['tmp_name'], $targetPath)) {
      $insuranceCardPath = $targetPath;
    }
  }

  // ✅ RX Medo Card Validation
  if ($card === "RX Medo Card") {
    if (!$rxFamilyOpt) {
      echo "<script>alert('❌ Please select Individual or Family under RX Medo Card.'); window.history.back();</script>";
      exit;
    }
    if ($rxFamilyOpt === "Family" && !$familyMembers) {
      echo "<script>alert('❌ Please select family members under RX Medo Card.'); window.history.back();</script>";
      exit;
    }
    if ($rxFamilyOpt === "Individual" && $familyMembers) {
      echo "<script>alert('❌ Family members selection is only allowed when Family is selected under RX Medo Card.'); window.history.back();</script>";
      exit;
    }
    $familyOpt = $rxFamilyOpt; // Normalize
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
      $familyOpt = $matureFamilyOpt; // Normalize
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
    // Photo is optional
  }
 // ✅ RX Medo Top Up Card Validation
if ($card === "RX Medo Top Up Card") {
  // ✅ Check if family opt is selected
  if (!$rxFamilyOpt) {
    echo "<script>alert('❌ Please select Individual or Family under RX Medo Top Up Card.'); window.history.back();</script>";
    exit;
  }

  // ✅ If Family is selected, ensure family members are selected
  if ($rxFamilyOpt === "Family" && !$familyMembers) {
    echo "<script>alert('❌ Please select family members under RX Medo Top Up Card.'); window.history.back();</script>";
    exit;
  }

  // ✅ If Individual is selected, ensure no family members are selected
  if ($rxFamilyOpt === "Individual" && $familyMembers) {
    echo "<script>alert('❌ Family members selection is only allowed when Family is selected under RX Medo Top Up Card.'); window.history.back();</script>";
    exit;
  }

  // ✅ No upgrade amount required — removed
}


  // ✅ Normalize familyOpt for DB insert
  $familyOpt = $rxFamilyOpt;
}


  // ✅ Prepare SQL query
  $query = "INSERT INTO mydata (
  name, email, phone, password, aadhaar_card_no, pan_card_no,
  card_type, age_group, family_opt, mature_family_opt, describe_age,
  family_members_selected, family_members_below25, family_members_above25,
  upgrade_amount, insurance_card_photo, rxmedo_family_opt
) VALUES (
  '$name', '$email', '$phone', '$password', '$aadhaar', '$pan',
  '$card', '$ageGroup', '$familyOpt', '$matureFamilyOpt', '$describeAge',
  '$familyMembers', '$membersBelow25', '$membersAbove25',
  '$upgradeAmount', '$insuranceCardPath', '$rxFamilyOpt'
)";


  // ✅ Execute query
  $execute = mysqli_query($con, $query);

  // ✅ Show result
  if ($execute) {
    echo "<script>alert('✅ Signup successful,Please proceed with payment!'); window.location.href='payscript.php';</script>";
  } else {
    echo "<script>alert('❌ Signup failed. Please try again.');</script>";
  }
}
?>
