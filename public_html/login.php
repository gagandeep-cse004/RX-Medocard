<?php
session_start();

// ✅ Connect to database
$con = mysqli_connect('localhost', 'root', '', 'users');
if (!$con) {
  die("❌ Connection failed: " . mysqli_connect_error());
}

// ✅ Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['mail']);
  $password = trim($_POST['pass']);

  // ✅ Check if email exists
  $stmt = $con->prepare("SELECT id, name, password, card_type, serial_no_medocard, serial_no_insure 
                         FROM mydata WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $name, $dbPassword, $card_type, $serial_no_medocard, $serial_no_insure);
    $stmt->fetch();

    // ✅ Match password (plain text — upgrade to hash later)
    if ($password === $dbPassword) {
      // ✅ Set session variables
      $_SESSION['user_id'] = $id;
      $_SESSION['user_name'] = $name;
      $_SESSION['user_email'] = $email;

      // ✅ Update purchase_date with current login date
      $loginDate = date('Y-m-d');
      $updateStmt = $con->prepare("UPDATE mydata SET purchase_date = ? WHERE id = ?");
      $updateStmt->bind_param("si", $loginDate, $id);
      $updateStmt->execute();
      $updateStmt->close();

      // ✅ Serial number logic based on card type
      if (strtolower($card_type) === 'rx medo card' && empty($serial_no_medocard)) {
        $countQuery = mysqli_query($con, "SELECT COUNT(*) AS total FROM mydata WHERE serial_no_medocard IS NOT NULL");
        $countRow = mysqli_fetch_assoc($countQuery);
        $nextNumber = $countRow['total'] + 1;
        $serial_no_medocard = 'RMC025' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        $updateSerial = $con->prepare("UPDATE mydata SET serial_no_medocard = ? WHERE id = ?");
        $updateSerial->bind_param("si", $serial_no_medocard, $id);
        $updateSerial->execute();
        $updateSerial->close();
      }

      if (strtolower($card_type) === 'rx medo insure card' && empty($serial_no_insure)) {
        $countQuery = mysqli_query($con, "SELECT COUNT(*) AS total FROM mydata WHERE serial_no_insure IS NOT NULL");
        $countRow = mysqli_fetch_assoc($countQuery);
        $nextNumber = $countRow['total'] + 1;
        $serial_no_insure = 'RMIC025' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        $updateSerial = $con->prepare("UPDATE mydata SET serial_no_insure = ? WHERE id = ?");
        $updateSerial->bind_param("si", $serial_no_insure, $id);
        $updateSerial->execute();
        $updateSerial->close();
      }

      // ✅ Auto-fetch card number and store in session
      $cardQuery = mysqli_query($con, "SELECT card_no FROM mydata WHERE id = $id LIMIT 1");
      if ($cardQuery && mysqli_num_rows($cardQuery) > 0) {
        $cardRow = mysqli_fetch_assoc($cardQuery);
        $_SESSION['user_card_no'] = $cardRow['card_no']; // ✅ session variable for auto-fill
      }

      echo "<script>alert('✅ Login successful. Welcome $name!'); window.location.href='dashboard.php';</script>";
    } else {
      echo "<script>alert('❌ Incorrect password.'); window.history.back();</script>";
    }
  } else {
    echo "<script>alert('❌ Email not found. Please sign up.'); window.location.href='signup.html';</script>";
  }

  $stmt->close();
  $con->close();
}
?>
