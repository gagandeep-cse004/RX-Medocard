<?php
session_start();
require_once __DIR__ . '/libs/fpdf.php'; // ✅ FPDF library include

// ✅ DB connection
$con = mysqli_connect('localhost', 'root', '', 'users');
if (!$con) { die("❌ DB connection failed"); }

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
  echo "<script>alert('Please login first.'); window.location.href='login.html';</script>";
  exit;
}


// ✅ Fetch user details
$stmt = $con->prepare("SELECT name, email, card_type, purchase_date, serial_no_medocard, serial_no_insure 
                       FROM mydata WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $card_type, $purchase_date, $serial_medocard, $serial_insure);
$stmt->fetch();
$stmt->close();
$con->close();

// ✅ Decide images + serial number
$card_type_lower = strtolower($card_type);
if ($card_type_lower === 'rx medo insure card') {
  $frontImage = 'cards\templates\medoinsure_front.jpg';
  $backImage  = 'cards\templates\medoinsure_back.jpg';
  $serial     = $serial_insure;
} else {
  $frontImage = 'cards\templates\medocard_front.jpg';
  $backImage  = 'cards\templates\medocard_back.jpg';
  $serial     = $serial_medocard;
}

// ✅ Calculate validity
$validUpto = date('d-m-Y', strtotime('+1 year', strtotime($purchase_date)));

// ✅ Output file path
$outputDir = 'cards/generated/';
if (!is_dir($outputDir)) { mkdir($outputDir, 0777, true); }
$pdfFile = $outputDir . preg_replace('/\s+/', '_', strtolower($name)) . '_card.pdf';

// ✅ PDF generation with FPDF
$pdf = new FPDF('P','mm','A4');

// ---------------- FRONT SIDE ----------------
$pdf->AddPage();
$pdf->Image($frontImage, 10, 20, 190); // card front image

$pdf->SetFont('Arial','B',18);
$pdf->SetTextColor(255,255,255);

// Overlay user details
$pdf->SetXY(93, 55);  $pdf->Cell(0, 8, "Name: " . $name);
$pdf->SetXY(93, 65);  $pdf->Cell(0, 8, "Card Type: " . $card_type);
$pdf->SetXY(93, 75); $pdf->Cell(0, 8, "Serial Number: " . $serial);

// ---------------- BACK SIDE ----------------
$pdf->AddPage();
$pdf->Image($backImage, 10, 20, 190); // card back image

$pdf->SetFont('Arial','B',17);
$pdf->SetTextColor(0,0,0);

// Overlay validity date
$pdf->SetXY(30, 110); 
$pdf->Cell(0, 8, "Valid Upto: " . $validUpto);

// ✅ Save PDF
$pdf->Output('F', $pdfFile);

// ✅ Trigger download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($pdfFile) . '"');
readfile($pdfFile);
exit;
?>
