<?php
include 'db.php';

$type = $_GET['type'];

// MD City Hospital full name for comparison
$mdCityFullName = 'MD City Hospital, Parthala Chowk, PKC-12, Sector-122 (Free OPD)';

// ✅ If Radiology selected → fetch diagnostics hospitals with MD City first
if ($type === 'Radiology') {
    $sql = "SELECT * FROM service_providers 
            WHERE service_type = 'diagnostic'
            GROUP BY name
            ORDER BY 
              CASE WHEN name LIKE '%MD City Hospital%' THEN 0 ELSE 1 END,
              name ASC";
} else {
    // ✅ For other service types → keep same logic
    $sql = "SELECT * FROM service_providers 
            WHERE service_type = '" . mysqli_real_escape_string($conn, $type) . "'
            GROUP BY name
            ORDER BY 
            CASE WHEN name LIKE '%MD City Hospital%' THEN 0 ELSE 1 END,
            name ASC";
}

$result = mysqli_query($conn, $sql);

$shown = [];

while ($row = mysqli_fetch_assoc($result)) {
    $name = $row['name'];

    // Normalize MD City Hospital name
    if (stripos($name, 'MD City Hospital') !== false) {
        if (in_array('mdcity', $shown)) continue; // avoid duplicate
        $name = $mdCityFullName;
        $shown[] = 'mdcity';
    } else {
        if (in_array($name, $shown)) continue; // avoid duplicate
        $shown[] = $name;
    }

    echo "<div data-id='{$row['id']}'>" . htmlspecialchars($name) . "</div>";
}
?>
