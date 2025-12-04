<?php
// Pull values from Azure Web App environment variables
$host     = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname   = getenv('DB_NAME');
$port     = getenv('DB_PORT') ?: 3306; // default to 3306

// Initialize MySQLi with SSL (required for Azure MySQL)
$mysqli = mysqli_init();

// Required for SSL on Azure MySQL (no CA needed)
$mysqli->ssl_set(NULL, NULL, NULL, NULL, NULL);

// Connect
$mysqli->real_connect(
    $host,
    $username,
    $password,
    $dbname,
    $port,
    NULL,
    MYSQLI_CLIENT_SSL
);

// Connection error handling
if ($mysqli->connect_errno) {
    die("Database connection failed: " . $mysqli->connect_error);
}
?>
