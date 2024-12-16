<?php
session_start();

// Destroy all session variables
$_SESSION = [];

// Destroy the session itself
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit;
?>