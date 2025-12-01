<?php
session_start();
require_once "db_write.php"; // write connection for modifying wishlist

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Make sure product_id is provided
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    header("Location: wishlist.php");
    exit();
}

$product_id = intval($_POST['product_id']);

// Delete item from user's wishlist safely
$sql = "DELETE FROM user_wishlist WHERE product_id = ? AND user_id = ?";
$params = [$product_id, $user_id];
$stmt = sqlsrv_query($conn_write, $sql, $params);

if ($stmt === false) {
    die("Error removing item from wishlist: " . print_r(sqlsrv_errors(), true));
}

// Redirect back to wishlist
header("Location: wishlist.php");
exit();
?>
