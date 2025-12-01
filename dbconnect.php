<?php
$host = 'drewhdb.mysql.database.azure.com';
$port = 3306;
$dbname = 'myDatabase';
$username = 'cmet01';
$password = 'Cardiff01';

// Enable SSL mode
$mysqli = mysqli_init();
$mysqli->ssl_set(NULL, NULL, NULL, NULL, NULL); // Required to use SSL without CA file
$mysqli->real_connect($host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);

if ($mysqli->connect_errno) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Connected successfully to MySQL!";
?>
