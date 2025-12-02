<?php
session_start();
require_once "dbconnect.php"; // <-- uses your new MySQLi connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ensure product_id and quantity are provided
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    die("Invalid request. Product not specified.");
}

$product_id = (int) $_POST['product_id'];

// Default quantity to 1 if missing or invalid
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
if ($quantity < 1) {
    $quantity = 1;
}

/* ------------------------------------------------------
   CHECK IF PRODUCT ALREADY EXISTS IN CART
------------------------------------------------------- */
$sql_check = "
    SELECT cart_id, quantity
    FROM user_cart
    WHERE user_id = ? AND product_id = ?
";

$stmt_check = $mysqli->prepare($sql_check);
if (!$stmt_check) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt_check->bind_param("ii", $user_id, $product_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

$row = $result_check->fetch_assoc();

/* ------------------------------------------------------
   UPDATE EXISTING CART ITEM
------------------------------------------------------- */
if ($row) {
    $new_quantity = $row['quantity'] + $quantity;

    $sql_update = "
        UPDATE user_cart
        SET quantity = ?, added_at = NOW()
        WHERE cart_id = ?
    ";

    $stmt_update = $mysqli->prepare($sql_update);
    if (!$stmt_update) {
        die("Prepare failed: " . $mysqli->error);
    }

    $stmt_update->bind_param("ii", $new_quantity, $row['cart_id']);
    
    if (!$stmt_update->execute()) {
        die("Error updating cart: " . $stmt_update->error);
    }

    $stmt_update->close();

} else {

    /* ------------------------------------------------------
       INSERT NEW CART ITEM
    ------------------------------------------------------- */
    $sql_insert = "
        INSERT INTO user_cart (user_id, product_id, quantity, added_at)
        VALUES (?, ?, ?, NOW())
    ";

    $stmt_insert = $mysqli->prepare($sql_insert);
    if (!$stmt_insert) {
        die("Prepare failed: " . $mysqli->error);
    }

    $stmt_insert->bind_param("iii", $user_id, $product_id, $quantity);

    if (!$stmt_insert->execute()) {
        die("Error adding to cart: " . $stmt_insert->error);
    }

    $stmt_insert->close();
}

$stmt_check->close();
$mysqli->close();

// Redirect back to previous page
$_SESSION['cart_message'] = "Product added to your cart!";
header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "basket.php"));
exit();
?>
