<?php
// =====================================================
// ShopSphere - Add 100 Sample Users
// =====================================================

// Use WRITE database connection
require_once "db_write.php";  // provides $conn_write
$conn = $conn_write;

if (!$conn) {
    die("Database connection failed.");
}

echo "Starting to add 100 sample users...\n<br>";

$firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Lisa', 'Robert', 'Emily'];
$lastNames  = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis'];

for ($i = 1; $i <= 100; $i++) {

    $firstName = $firstNames[array_rand($firstNames)];
    $lastName  = $lastNames[array_rand($lastNames)];
    $name      = $firstName . ' ' . $lastName;
    $email     = strtolower($firstName . '.' . $lastName . $i . '@example.com');
    $password  = 'Pass' . $i . '!';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql    = "INSERT INTO shopusers (name, email, password) VALUES (?, ?, ?)";
    $params = [$name, $email, $hashed_password];

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        echo "Added user $i: $name ($email)\n<br>";
        sqlsrv_free_stmt($stmt);
    } else {
        echo "Error adding user $i: " . print_r(sqlsrv_errors(), true) . "\n<br>";
    }

    ob_flush();
    flush();
}

echo "Completed adding 100 users!\n<br>";

sqlsrv_close($conn);
?>
