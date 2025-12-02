<?php
session_start();
require_once "dbconnect.php"; // $mysqli read-only is fine for viewing

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ShopSphere - My Orders</title>
<style>
body { font-family: 'Helvetica Neue', Arial, sans-serif; background:#fafafa; color:#333; margin:0; }
header, footer { background:#f2f5f1; padding:15px 30px; text-align:center; }
h1 { color:#2e5d34; }
.container { max-width:1000px; margin:30px auto; padding:20px; background:white; border-radius:8px; }
table { width:100%; border-collapse: collapse; margin-bottom:30px; }
th, td { padding:10px; border:1px solid #ddd; text-align:left; }
th { background:#2e5d34; color:white; }
.status { font-weight:bold; }
.status.Pending { color:orange; }
.status.Shipped { color:blue; }
.status.Delivered { color:green; }
</style>
</head>
<body>

<?php include "header_nav.php"; ?>

<div class="container">

<?php
// Query user's orders
$sql_orders = "SELECT order_id, total_amount, address, status, created_at
               FROM orders
               WHERE user_id = ?
               ORDER BY created_at DESC";

$stmt_orders = $mysqli->prepare($sql_orders);
$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();

if (!$result_orders) {
    die("Failed to retrieve orders: " . $mysqli->error);
}

$hasOrders = false;
while ($order = $result_orders->fetch_assoc()) {
    $hasOrders = true;
    $order_id = $order['order_id'];

    // Get items for this order
    $sql_items = "SELECT oi.quantity, oi.price, p.name
                  FROM order_items oi
                  JOIN products p ON oi.product_id = p.product_id
                  WHERE oi.order_id = ?";
    $stmt_items = $mysqli->prepare($sql_items);
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $result_items = $stmt_items->get_result();
?>

    <h2>Order ID: <?= $order_id ?> | Status: <span class="status <?= htmlspecialchars($order['status']); ?>"><?= htmlspecialchars($order['status']); ?></span></h2>
    <p>Delivery Address: <?= htmlspecialchars($order['address']); ?></p>
    <p>Ordered At: <?= htmlspecialchars($order['created_at']); ?></p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price (£)</th>
                <th>Quantity</th>
                <th>Subtotal (£)</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $order_total = 0;
        while ($item = $result_items->fetch_assoc()) {
            $subtotal = $item['price'] * $item['quantity'];
            $order_total += $subtotal;
        ?>
            <tr>
                <td><?= htmlspecialchars($item['name']); ?></td>
                <td><?= number_format($item['price'], 2); ?></td>
                <td><?= $item['quantity']; ?></td>
                <td><?= number_format($subtotal, 2); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <p style="text-align:right; font-weight:bold;">Order Total: £<?= number_format($order_total, 2); ?></p>

<?php
    $stmt_items->close();
}

if (!$hasOrders) {
    echo "<p>You have no orders yet.</p>";
}

$stmt_orders->close();
$mysqli->close();
?>

</div>

<footer>
    &copy; 2025 ShopSphere | Fresh, Local & Healthy
</footer>

</body>
</html>
