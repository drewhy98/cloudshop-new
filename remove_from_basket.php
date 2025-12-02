<?php
session_start();
require_once "dbconnect.php"; // provides $mysqli

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

// Prepare statement to delete item
$sql = "DELETE FROM user_cart WHERE cart_id = ? AND user_id = ?";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param("ii", $cart_id, $user_id);

if (!$stmt->execute()) {
    die("Error removing item from cart: " . $stmt->error);
}

$stmt->close();

// Redirect back to basket
header("Location: basket.php");
exit();
?>
