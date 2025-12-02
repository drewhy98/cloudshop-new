<?php
session_start();
require_once "dbconnect.php";  // provides $mysqli

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Basic validation
    if (empty($email) || empty($password)) {
        header("Location: admin_login.php?error=" . urlencode("Please fill in all fields."));
        exit();
    }

    if (!$mysqli) {
        header("Location: admin_login.php?error=" . urlencode("Database connection failed."));
        exit();
    }

    // Fetch admin by email
    $sql = "SELECT id, name, email, password FROM adminusers WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();

    if ($admin) {
        if (password_verify($password, $admin['password'])) {

            // Set admin session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $admin['id'];
            $_SESSION['admin_name']      = $admin['name'];
            $_SESSION['admin_email']     = $admin['email'];

            header("Location: admin_index.php");
            exit();

        } else {
            header("Location: admin_login.php?error=" . urlencode("Incorrect password."));
            exit();
        }
    } else {
        header("Location: admin_login.php?error=" . urlencode("Admin not found."));
        exit();
    }

} else {
    // Prevent direct access
    header("Location: admin_login.php");
    exit();
}
?>
