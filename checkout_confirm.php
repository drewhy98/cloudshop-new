<?php
session_start();
require_once "dbconnect.php"; // MySQLi connection variable: $mysqli

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id  = intval($_SESSION['user_id']);
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    die("No order specified.");
}

// Fetch order details
$sql_order = "
    SELECT order_id, total_amount, address, payment_method, status, created_at
    FROM orders
    WHERE order_id = ? AND user_id = ?
";

$stmt_order = $mysqli->prepare($sql_order);
if (!$stmt_order) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt_order->bind_param("ii", $order_id, $user_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();

$order = $result_order->fetch_assoc();
$stmt_order->close();

if (!$order) {
    die("Order not found.");
}

// Fetch order items
$sql_items = "
    SELECT p.name, oi.quantity, oi.price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
";

$stmt_items = $mysqli->prepare($sql_items);
if (!$stmt_items) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$order_items = [];
while ($row = $result_items->fetch_assoc()) {
    $order_items[] = $row;
}
$stmt_items->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmation - ShopSphere</title>
<style>
body { font-family: Arial, sans-serif; background:#fafafa; margin:0; }
h1, h2 { color:#2e5d34; }
.container { max-width:800px; margin:30px auto; background:white; padding:20px; border-radius:8px; }
table { width:100%; border-collapse: collapse; margin-top:15px; }
th, td { border:1px solid #ccc; padding:10px; }
th { background:#2e5d34; color:white; }
.total { text-align:right; font-weight:bold; margin-top:10px; }
footer { background:#f2f5f1; padding:15px; text-align:center; }
</style>
</head>
<body>

<?php include "header_nav.php"; ?>

<div class="container">
<h1>Thank you for your order!</h1>
<p>Order ID: <strong><?= $order['order_id'] ?></strong></p>
<p>Payment Method: <strong><?= htmlspecialchars($order['payment_method']) ?></strong></p>
<p>Delivery Address: <strong><?= htmlspecialchars($order['address']) ?></strong></p>
<p>Ordered At: <strong><?= htmlspecialchars($order['created_at']) ?></strong></p>
<p>Status: <strong><?= htmlspecialchars($order['status']) ?></strong></p>

<h3>Order Items</h3>
<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Price (£)</th>
            <th>Qty</th>
            <th>Subtotal (£)</th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0; ?>
        <?php foreach ($order_items as $item): 
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
        ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= number_format($item['price'], 2) ?></td>
            <td><?= intval($item['quantity']) ?></td>
            <td><?= number_format($subtotal, 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="total">Total Paid: £<?= number_format($total, 2) ?></div>
</div>

<footer>&copy; 2025 ShopSphere | Fresh, Local & Healthy</footer>

</body>
</html>
