<?php
include 'db.php';

// Basic validation
$service_type      = isset($_POST['service_type'])   ? trim($_POST['service_type'])   : '';
$provider_id       = isset($_POST['provider_id'])    ? trim($_POST['provider_id'])    : '';
$appointment_date  = isset($_POST['appointment_date']) ? $_POST['appointment_date']   : '';
$appointment_time  = isset($_POST['appointment_time']) ? $_POST['appointment_time']   : '';
$card_no           = isset($_POST['card_no'])        ? trim($_POST['card_no'])        : '';

// ✅ Validation rules
if ($service_type === '' || $provider_id === '' || $card_no === '') {
    die("Missing required fields.");
}

// ✅ Appointment required only for Radiology, OPD, Hospital Visit
if (in_array($service_type, ['Radiology', 'OPD', 'Hospital Visit']) 
    && ($appointment_date === '' || $appointment_time === '')) {
    die("Missing appointment details.");
}

// Handle prescription upload
$prescription_path = null;
if (isset($_FILES['prescription_image']) && $_FILES['prescription_image']['error'] === 0) {
    // Safe filename
    $fname = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES['prescription_image']['name']));
    // Ensure uploads dir exists
    if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
    $prescription_path = "uploads/" . $fname;
    move_uploaded_file($_FILES['prescription_image']['tmp_name'], $prescription_path);
}

// Fetch hospital name only if provider_id is numeric
$hospital_name = null;
if (ctype_digit($provider_id)) {
    $pid = intval($provider_id);
    $hres = mysqli_query($conn, "SELECT name FROM service_providers WHERE id = {$pid} LIMIT 1");
    if ($hres && mysqli_num_rows($hres) > 0) {
        $hrow = mysqli_fetch_assoc($hres);
        $hospital_name = $hrow['name'];
    }
}

// Ensure service_request has hospital_name & appointment_time columns; if not, run:
// ALTER TABLE service_request ADD COLUMN hospital_name VARCHAR(255) AFTER provider_id;
// ALTER TABLE service_request ADD COLUMN appointment_time VARCHAR(20) AFTER appointment_date;

if ($hospital_name) {
    // Prepared statement insert with hospital_name
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO service_request (service_type, provider_id, hospital_name, appointment_date, appointment_time, prescription_image, card_no)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "sssssss", $service_type, $provider_id, $hospital_name, $appointment_date, $appointment_time, $prescription_path, $card_no);
} else {
    // Fallback without hospital_name
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO service_request (service_type, provider_id, appointment_date, appointment_time, prescription_image, card_no)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "ssssss", $service_type, $provider_id, $appointment_date, $appointment_time, $prescription_path, $card_no);
}

$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$ok) {
    die("Insert failed: " . mysqli_error($conn));
}

// Success message with selected hospital name
echo "Request submitted successfully" . ($hospital_name ? " for {$hospital_name}" : "") . "!";
?>
