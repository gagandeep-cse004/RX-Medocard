<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "users";  // database name

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: set UTF-8 for clean strings
mysqli_set_charset($conn, "utf8");
?>
