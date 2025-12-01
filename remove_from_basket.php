<?php
session_start();
require_once "dbconnect.php"; // write connection for modifying cart

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Make sure cart_id is provided
if (!isset($_POST['cart_id']) || empty($_POST['cart_id'])) {
    header("Location: basket.php");
    exit();
}

$cart_id = intval($_POST['cart_id']);

// Delete item from user's cart safely
$sql = "DELETE FROM user_cart WHERE cart_id = ? AND user_id = ?";
$params = [$cart_id, $user_id];
$stmt = sqlsrv_query($conn_write, $sql, $params);

if ($stmt === false) {
    die("Error removing item from cart: " . print_r(sqlsrv_errors(), true));
}

// Redirect back to basket
header("Location: basket.php");
exit();
?>
