<?php
// =====================================================
// ShopSphere - Add 100 Sample Users (MySQL Version) with creation date
// =====================================================

require_once "dbconnect.php"; // Uses $mysqli

// Enable MySQLi errors for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!$mysqli) {
    die("Database connection failed.");
}

echo "Starting to add 100 sample users...<br>";

$firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Lisa', 'Robert', 'Emily'];
$lastNames  = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis'];

// Prepare SQL once, now including created_at
$sql = "INSERT INTO shopusers (name, email, password, created_at) VALUES (?, ?, ?, NOW())";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

for ($i = 1; $i <= 100; $i++) {
    $firstName = $firstNames[array_rand($firstNames)];
    $lastName  = $lastNames[array_rand($lastNames)];
    $name      = $firstName . ' ' . $lastName;
    $email     = strtolower($firstName . '.' . $lastName . $i . '@example.com');

    $password  = 'Pass' . $i . '!';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt->bind_param("sss", $name, $email, $hashed_password);
        $stmt->execute();
        echo "Added user $i: $name ($email)<br>";
    } catch (mysqli_sql_exception $e) {
        echo "Error adding user $i: " . $e->getMessage() . "<br>";
    }

    ob_flush();
    flush();
}

$stmt->close();
$mysqli->close();

echo "Completed adding 100 users!<br>";
?>
