<?php
session_start();
require_once "dbconnect.php"; // Admin needs view access (MySQL)

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php?error=" . urlencode("Please log in as an admin."));
    exit();
}

// Update order status when form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = trim($_POST['status']);

    $update_sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn_write->prepare($update_sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn_write->error);
    }

    $stmt->bind_param("si", $status, $order_id);

    if (!$stmt->execute()) {
        die("Failed to update status: " . $stmt->error);
    }

    $stmt->close();

    header("Location: admin_view_orders.php?msg=" . urlencode("Order updated successfully."));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Manage Orders</title>

<style>
body { font-family:'Helvetica Neue',Arial; background:#fafafa; margin:0; color:#333; }
header { background:white; padding:15px 40px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #ddd; }
header h1 { color:#2e5d34; font-family:'Georgia'; margin:0; }
.logout-btn { padding:6px 12px; background:#2e5d34; border:none; color:white; border-radius:4px; cursor:pointer; }
nav { background:#f2f5f1; padding:12px 30px; display:flex; justify-content:center; gap:30px; }
nav a { text-decoration:none; color:#2e5d34; font-weight:bold; }
.container { max-width:1200px; margin:20px auto; padding:20px; background:white; border-radius:8px; }
.message { background:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:10px; border:1px solid #c3e6cb; }
table { width:100%; border-collapse: collapse; margin-top:20px; }
th, td { padding:10px; border:1px solid #ddd; text-align:left; }
th { background:#2e5d34; color:white; }
.status { font-weight:bold; }
.status.Pending { color:orange; }
.status.Out\ for\ Delivery { color:blue; }
.status.Delivered { color:green; }
select { padding:6px; border-radius:4px; }
button { padding:6px 12px; background:#2e5d34; color:white; border:none; border-radius:4px; cursor:pointer; }
button:hover { background:#244928; }
</style>
</head>
<body>

<header>
    <h1>ShopSphere Admin Panel</h1>
    <form method="post" action="admin_logout.php">
        <button class="logout-btn">Logout</button>
    </form>
</header>

<nav>
    <a href="admin_index.php">Dashboard</a>
    <a href="admin_view_orders.php">Manage Orders</a>
    <a href="admin_view_products.php">Manage Products</a>
</nav>

<div class="container">

<?php if (isset($_GET['msg'])): ?>
    <div class="message"><?= htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>

<?php
// Fetch all orders with user info
$sql_orders = "
    SELECT o.order_id, o.user_id, u.name AS customer_name,
           o.total_amount, o.address, o.status, o.created_at, o.payment_method
    FROM orders o
    LEFT JOIN shopusers u ON o.user_id = u.id
    ORDER BY o.created_at DESC
";

$result = $conn_write->query($sql_orders);

if (!$result) {
    die("Failed to retrieve orders: " . $conn_write->error);
}
?>

<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>User</th>
            <th>Payment Method</th>
            <th>Address</th>
            <th>Total (£)</th>
            <th>Status</th>
            <th>Update Status</th>
            <th>Placed At</th>
        </tr>
    </thead>
    <tbody>

<?php while ($order = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $order['order_id'] ?></td>
        <td><?= htmlspecialchars($order['customer_name'] ?? "Unknown") ?></td>
        <td><?= htmlspecialchars($order['payment_method'] ?? "N/A") ?></td>
        <td><?= htmlspecialchars($order['address']) ?></td>
        <td><?= number_format((float)$order['total_amount'], 2) ?></td>

        <td class="status <?= htmlspecialchars($order['status']) ?>">
            <?= htmlspecialchars($order['status']) ?>
        </td>

        <td>
            <form method="post" style="display:flex; gap:5px;">
                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                <select name="status">
                    <option value="Pending" <?= $order['status']==="Pending" ? "selected" : "" ?>>Pending</option>
                    <option value="Out for Delivery" <?= $order['status']==="Out for Delivery" ? "selected" : "" ?>>Out for Delivery</option>
                    <option value="Delivered" <?= $order['status']==="Delivered" ? "selected" : "" ?>>Delivered</option>
                </select>
                <button type="submit">Update</button>
            </form>
        </td>

        <td>
            <?= htmlspecialchars($order['created_at']) ?>
        </td>
    </tr>
<?php endwhile; ?>

    </tbody>
</table>

<?php
$result->free();
$conn_write->close();
?>

</div>

<footer>
    &copy; 2025 ShopSphere | Admin Panel
</footer>
</body>
</html>
