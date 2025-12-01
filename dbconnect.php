<?php
$host = 'drewhdb.mysql.database.azure.com';
$port = 3306;
$dbname = 'myDatabase';
$username = 'cmet01';
$password = 'Cardiff01';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_SSL_MODE => PDO::MYSQL_ATTR_SSL_MODE_REQUIRED, // SSL without local CA file
];

try {
    $conn = new PDO($dsn, $username, $password, $options);
    echo "Connected successfully to MySQL!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
