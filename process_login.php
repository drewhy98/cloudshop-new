<?php
// =====================================================
// ShopSphere - User Login Processor (MySQL version)
// =====================================================

session_start();
require_once "dbconnect.php";   // provides: $mysqli

// =====================================================
// Handle login submission
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Basic validation
    if (empty($email) || empty($password)) {
        header("Location: login.php?error=" . urlencode("Please fill in all fields."));
        exit();
    }

    if (!$mysqli) {
        header("Location: login.php?error=" . urlencode("Database connection failed."));
        exit();
    }

    // Fetch user record using prepared statement
    $sql = "SELECT id, name, email, password FROM shopusers WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        // Verify password hash
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            header("Location: index.php");
            exit();
        } else {
            header("Location: login.php?error=" . urlencode("Incorrect password."));
            exit();
        }
    } else {
        header("Location: login.php?error=" . urlencode("No account found with that email."));
        exit();
    }

} else {
    // Accessed directly
    header("Location: login.php");
    exit();
}
?>
