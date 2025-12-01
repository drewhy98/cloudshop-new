<?php
// ---------------------
// Database Connection
// ---------------------

$con = mysqli_connect(
    "drewhdb.mysql.database.azure.com",  // Host
    "cmet01",                            // Username
    "Cardiff01",                   // Password
    "shopsphere"                         // Database name
);

// Check connection
if (mysqli_connect_errno()) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Optional: Set UTF-8 encoding
mysqli_set_charset($con, "utf8mb4");
?>
