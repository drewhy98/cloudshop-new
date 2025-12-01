<?php
session_start();
require_once "dbconnect.php"; // <-- for all operations

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ensure product_id and quantity are provided - setting default to 1 
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    die("Invalid request. Product not specified.");
}

$product_id = (int) $_POST['product_id'];

// Default quantity to 1 if missing or invalid
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
if ($quantity < 1) {
    $quantity = 1;
}

$product_id = (int) $_POST['product_id'];
$quantity = (int) $_POST['quantity'];

if ($quantity < 1) {
    $quantity = 1; // minimum quantity
}

// Check if product is already in the cart
$sql_check = "
    SELECT cart_id, quantity
    FROM user_cart
    WHERE user_id = ? AND product_id = ?
";
$params_check = [$user_id, $product_id];
$stmt_check = sqlsrv_query($conn_write, $sql_check, $params_check);

if ($stmt_check === false) {
    die("Error checking cart: " . print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC);

if ($row) {
    // Product exists in cart, update quantity
    $new_quantity = $row['quantity'] + $quantity;
    $sql_update = "
        UPDATE user_cart
        SET quantity = ?, added_at = GETDATE()
        WHERE cart_id = ?
    ";
    $params_update = [$new_quantity, $row['cart_id']];
    $stmt_update = sqlsrv_query($conn_write, $sql_update, $params_update);

    if ($stmt_update === false) {
        die("Error updating cart: " . print_r(sqlsrv_errors(), true));
    }
} else {
    // Insert new product into cart
    $sql_insert = "
        INSERT INTO user_cart (user_id, product_id, quantity)
        VALUES (?, ?, ?)
    ";
    $params_insert = [$user_id, $product_id, $quantity];
    $stmt_insert = sqlsrv_query($conn_write, $sql_insert, $params_insert);

    if ($stmt_insert === false) {
        die("Error adding to cart: " . print_r(sqlsrv_errors(), true));
    }
}

sqlsrv_free_stmt($stmt_check);
sqlsrv_close($conn_write);

// Redirect back to previous page
$_SESSION['cart_message'] = "Product added to your cart!";
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "basket.php"));
exit();
?>
