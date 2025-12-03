<?php
session_start();         // ✅ Start session
session_unset();         // ✅ Clear all session variables
session_destroy();       // ✅ Destroy the session completely

// ✅ Redirect to login page
header("Location: login.html");
exit;
?>
