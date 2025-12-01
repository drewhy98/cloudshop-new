<?php
// logout.php — Ends the user session

session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to homepage after logout
header("Location: index.php");
exit;
