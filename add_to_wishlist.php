<?php
session_start();
require_once "dbconnect.php"; // Uses your new MySQLi connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ensure product_id is provided
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    die("Invalid request. No product specified.");
}

$product_id = (int) $_POST['product_id'];

// Insert into user_wishlist
$sql = "
    INSERT INTO user_wishlist (user_id, product_id)
    VALUES (?, ?)
";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param("ii", $user_id, $product_id);

// Attempt to execute (catch duplicate entry)
if (!$stmt->execute()) {

    // MySQL duplicate key error code = 1062
    if ($stmt->errno === 1062) {
        $_SESSION['wishlist_message'] = "This product is already in your wishlist.";
    } else {
        $_SESSION['wishlist_message'] = "Error adding to wishlist.";
    }

} else {
    $_SESSION['wishlist_message'] = "Product added to your wishlist!";
}

$stmt->close();
$mysqli->close();

// Redirect back to the previous page or wishlist page
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "wishlist.php"));
exit();
?>

