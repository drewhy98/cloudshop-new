<?php
session_start();
require_once "dbconnect.php"; // read-only DB connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

// Check if basket contains any items
$sql = "
    SELECT c.quantity, p.name, p.price
    FROM user_cart c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = ?
";

$stmt = $conn_read->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$hasItems = $result->num_rows > 0;

if (!$hasItems) {
    die("<p>Your basket is empty. <a href='basket.php'>Return to basket</a></p>");
}

$stmt->close();
$conn_read->close();
?>

<!DOCTYPE html>
<html>
<head>
<title>Checkout - ShopSphere</title>
<style>
body { font-family: Arial; background:#fafafa; margin:0; }
.container { max-width:600px; margin:30px auto; background:white; padding:20px; border-radius:8px; }
label { font-weight:bold; }
input, textarea, select { width:100%; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:4px; }
button { background:#2e5d34; color:white; padding:10px; border:none; border-radius:4px; cursor:pointer; }
button:hover { background:#244928; }
</style>
</head>
<body>

<div class="container">
    <h2>Delivery & Payment Details</h2>

    <form method="post" action="checkout_process.php">

        <label>Delivery Address</label>
        <textarea name="address" rows="4" required></textarea>

        <label>Payment Method</label>
        <select name="payment_method" required>
            <option value="">-- Select payment method --</option>
            <option value="Card">Debit / Credit Card</option>
            <option value="PayPal">PayPal</option>
            <option value="Cash on Delivery">Cash on Delivery</option>
        </select>

        <button type="submit">Review Order</button>
    </form>
</div>

</body>
</html>
