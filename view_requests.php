<?php
include 'db.php';

// Join to always show the latest name (even if hospital_name column exists)
$sql = "SELECT
          sr.id,
          sr.service_type,
          COALESCE(sr.hospital_name, sp.name) AS hospital_name,
          sr.appointment_date,
          sr.request_time,
          sr.prescription_image,
          sr.card_no
        FROM service_request sr
        LEFT JOIN service_providers sp ON sr.provider_id = sp.id
        ORDER BY sr.request_time DESC";

$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View service requests</title>
<style>
  table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
  th, td { border: 1px solid #ddd; padding: 8px; font-size: 14px; }
  th { background: #f5f5f5; text-align: left; }
  .muted { color:#666; font-size:12px; }
</style>
</head>
<body>

<h2>Service requests</h2>
<table>
  <tr>
    <th>ID</th>
    <th>Service type</th>
    <th>Hospital name</th>
    <th>Appointment date</th>
    <th>Request time</th>
    <th>Prescription</th>
    <th>Card no</th>
  </tr>
  <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
      <td><?php echo htmlspecialchars($row['id']); ?></td>
      <td><?php echo htmlspecialchars($row['service_type']); ?></td>
      <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
      <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
      <td><?php echo htmlspecialchars($row['request_time']); ?></td>
      <td><?php echo htmlspecialchars($row['prescription_image'] ?: ''); ?></td>
      <td><?php echo htmlspecialchars($row['card_no']); ?></td>
    </tr>
  <?php } ?>
</table>

<p class="muted">This view auto-syncs names via JOIN, so any update in service_providers reflects here instantly.</p>

</body>
</html>
