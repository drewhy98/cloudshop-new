<?php
// =====================================================
// ShopSphere - User Registration Processor
// =====================================================

require_once "db_write.php";   // <-- THIS replaces all server details
// Provides: $conn_write

// =====================================================
// Handle Form Submission
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize & validate inputs
    $name     = trim($_POST['name'] ?? '');
    $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        header("Location: register.php?error=" . urlencode("Please fill in all required fields."));
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.php?error=" . urlencode("Please enter a valid email address."));
        exit();
    }

    // Use write database connection
    $conn = $conn_write;

    if (!$conn) {
        header("Location: register.php?error=" . urlencode("Database connection failed."));
        exit();
    }

    // Check if email already exists
    $checkSql = "SELECT email FROM shopusers WHERE email = ?";
    $checkStmt = sqlsrv_query($conn, $checkSql, [$email]);

    if ($checkStmt && sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC)) {
        header("Location: register.php?error=" . urlencode("This email is already registered."));
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $insertSql = "INSERT INTO shopusers (name, email, password) VALUES (?, ?, ?)";
    $params = [$name, $email, $hashedPassword];
    $stmt = sqlsrv_query($conn, $insertSql, $params);

    if ($stmt) {
        header("Location: success.php");
        exit();
    } else {
        header("Location: register.php?error=" . urlencode("Registration failed."));
        exit();
    }

} else {
    header("Location: register.php");
    exit();
}
