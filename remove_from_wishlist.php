<?php
session_start();
require_once "dbconnect.php"; // provides $mysqli

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

// Prepare statement to delete item
$sql = "DELETE FROM user_wishlist WHERE product_id = ? AND user_id = ?";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param("ii", $product_id, $user_id);

if (!$stmt->execute()) {
    die("Error removing item from wishlist: " . $stmt->error);
}

$stmt->close();

// Redirect back to wishlist
header("Location: wishlist.php");
exit();
?>
