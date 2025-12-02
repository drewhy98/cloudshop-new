<?php
// =====================================================
// ShopSphere - User Registration Processor (MySQL version)
// =====================================================

require_once "dbconnect.php";   // provides $mysqli

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

    if (!$mysqli) {
        header("Location: register.php?error=" . urlencode("Database connection failed."));
        exit();
    }

    // Check if email already exists
    $checkSql = "SELECT email FROM shopusers WHERE email = ?";
    $checkStmt = $mysqli->prepare($checkSql);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $checkStmt->close();
        header("Location: register.php?error=" . urlencode("This email is already registered."));
        exit();
    }
    $checkStmt->close();

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $insertSql = "INSERT INTO shopusers (name, email, password) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($insertSql);
    $stmt->bind_param("sss", $name, $email, $hashedPassword);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: success.php");
        exit();
    } else {
        $stmt->close();
        header("Location: register.php?error=" . urlencode("Registration failed."));
        exit();
    }

} else {
    header("Location: register.php");
    exit();
}
?>
