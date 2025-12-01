<?php
// =====================================================
// ShopSphere - User Login Processor
// =====================================================

// Use READ database for login
require_once "dbconnect.php";   // provides: $conn_read

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

    // Use read-only connection
    $conn = $conn_read;

    if (!$conn) {
        header("Location: login.php?error=" . urlencode("Database connection failed."));
        exit();
    }

    // Fetch user record
    $sql = "SELECT id, name, email, password FROM shopusers WHERE email = ?";
    $stmt = sqlsrv_query($conn, $sql, [$email]);

    if ($stmt === false) {
        header("Location: login.php?error=" . urlencode("Database query failed."));
        exit();
    }

    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($user) {

        // Verify password hash
        if (password_verify($password, $user['password'])) {

            session_start();

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
