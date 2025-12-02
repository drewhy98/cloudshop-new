<?php
// =====================================================
// ShopSphere - Add 100 Sample Users (MySQL Version)
// =====================================================

require_once "dbconnect.php"; // Uses your new MySQLi connection

if (!$mysqli) {
    die("Database connection failed.");
}

echo "Starting to add 100 sample users...\n<br>";

$firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Lisa', 'Robert', 'Emily'];
$lastNames  = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis'];

// Prepare SQL once (more efficient)
$sql = "INSERT INTO shopusers (name, email, password) VALUES (?, ?, ?)";

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

    // Bind the parameters
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "Added user $i: $name ($email)\n<br>";
    } else {
        echo "Error adding user $i: " . $stmt->error . "\n<br>";
    }

    ob_flush();
    flush();
}

$stmt->close();
$mysqli->close();

echo "Completed adding 100 users!\n<br>";
?>
