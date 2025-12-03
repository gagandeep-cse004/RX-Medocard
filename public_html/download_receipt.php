<?php
require_once __DIR__ . '/libs/fpdf.php'; // ✅ FPDF library include

$con = new mysqli("localhost","root","","users");
if($con->connect_error) die("DB Connection failed");

$orderId = $_GET['order_id'] ?? '';
if(!$orderId) die("❌ Invalid order ID");

// ✅ Fetch order + payment + user details
$stmt = $con->prepare("SELECT ro.user_id, ro.card_type, ro.card_category, ro.card_subcategory, ro.age_group, 
                              p.payment_id, p.amount, p.created_at, m.name 
                       FROM razorpay_orders ro 
                       JOIN payments p ON ro.order_id = p.order_id 
                       JOIN mydata m ON ro.user_id = m.id 
                       WHERE ro.order_id=? LIMIT 1");
$stmt->bind_param("s",$orderId);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

if(!$data) die("❌ Receipt not found");

// ✅ Prepare receipt fields
$startDate = date("d-m-Y", strtotime($data['created_at']));
$endDate   = date("d-m-Y", strtotime("+1 year -1 day", strtotime($data['created_at'])));

// ✅ Generate PDF
$pdf = new FPDF();
$pdf->AddPage();

// Header
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'RX MEDO PAYMENT RECEIPT',0,1,'C');
$pdf->Ln(8);

// Details
$pdf->SetFont('Arial','',12);
$labelWidth = 50; $valueWidth = 130;

$pdf->Cell($labelWidth,8,'Reference Id:',0,0); $pdf->Cell($valueWidth,8,$data['payment_id'],0,1);
$pdf->Cell($labelWidth,8,'Date:',0,0); $pdf->Cell($valueWidth,8,$startDate,0,1);
$pdf->Cell($labelWidth,8,'Name:',0,0); $pdf->Cell($valueWidth,8,ucwords($data['name']),0,1);
$pdf->Cell($labelWidth,8,'Card Type:',0,0); $pdf->Cell($valueWidth,8,$data['card_type'],0,1);

if(!empty($data['card_category'])) {
  $pdf->Cell($labelWidth,8,'Category:',0,0); $pdf->Cell($valueWidth,8,$data['card_category'],0,1);
}
if(!empty($data['card_subcategory'])) {
  $pdf->Cell($labelWidth,8,'Subcategory:',0,0); $pdf->Cell($valueWidth,8,$data['card_subcategory'],0,1);
}
if(!empty($data['age_group'])) {
  $pdf->Cell($labelWidth,8,'Age Group:',0,0); $pdf->Cell($valueWidth,8,$data['age_group'],0,1);
}

$pdf->Cell($labelWidth,8,'Amount:',0,0); $pdf->Cell($valueWidth,8,'Rs '.number_format($data['amount']/100,2),0,1);
$pdf->Cell($labelWidth,8,'Payment Mode:',0,0); $pdf->Cell($valueWidth,8,'Online',0,1);

// ✅ New Column
$pdf->Cell($labelWidth,8,'Payment For:',0,0); $pdf->Cell($valueWidth,8,'Membership Card',0,1);

$pdf->Cell($labelWidth,8,'Validity:',0,0); $pdf->Cell($valueWidth,8,$startDate.' to '.$endDate,0,1);

$pdf->Ln(12);
$pdf->SetFont('Arial','I',11);
$pdf->Cell(0,10,'Thank you | RX Medical Trust and Services.',0,1,'C');

// ✅ Output PDF (force download)
$pdf->Output('D','Payment_Receipt_'.$data['payment_id'].'.pdf');
?>
