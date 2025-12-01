<?php
session_start();
require_once "db_write.php"; // Use writable DB connection for inserting orders

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_submitted = false;
$order_id = null;
$order_items = [];
$total_amount = 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address']);

    if (!empty($address)) {
        //  Calculate total from basket
        $sql_basket = "
            SELECT c.product_id, c.quantity, p.price, p.name
            FROM user_cart c
            JOIN products p ON c.product_id = p.product_id
            WHERE c.user_id = ?
        ";
        $params = [$user_id];
        $stmt = sqlsrv_query($conn_write, $sql_basket, $params);

        if ($stmt === false) {
            die("Failed to get basket items: " . print_r(sqlsrv_errors(), true));
        }

        $items = [];
        $total_amount = 0;
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $items[] = $row;
            $total_amount += $row['price'] * $row['quantity'];
        }
        sqlsrv_free_stmt($stmt);

        if (count($items) > 0) {
            // Insert order
            $sql_order = "INSERT INTO orders (user_id, total_amount, address) VALUES (?, ?, ?)";
            $params_order = [$user_id, $total_amount, $address];
            $stmt_order = sqlsrv_query($conn_write, $sql_order, $params_order);

            if ($stmt_order === false) {
                die("Failed to create order: " . print_r(sqlsrv_errors(), true));
            }

            // Get inserted order_id
            $sql_id = "SELECT SCOPE_IDENTITY() AS order_id";
            $stmt_id = sqlsrv_query($conn_write, $sql_id);
            $row_id = sqlsrv_fetch_array($stmt_id, SQLSRV_FETCH_ASSOC);
            $order_id = $row_id['order_id'];
            sqlsrv_free_stmt($stmt_id);

            // Insert each item into order_items
            foreach ($items as $item) {
                $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                $params_item = [$order_id, $item['product_id'], $item['quantity'], $item['price']];
                sqlsrv_query($conn_write, $sql_item, $params_item);
            }

            //  Clear user's basket
            $sql_clear = "DELETE FROM user_cart WHERE user_id = ?";
            sqlsrv_query($conn_write, $sql_clear, [$user_id]);

            $order_items = $items;
            $order_submitted = true;
        } else {
            $error = "Your basket is empty.";
        }
    } else {
        $error = "Please enter a delivery address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ShopSphere - Checkout</title>
<style>
body { font-family: 'Helvetica Neue', Arial, sans-serif; background:#fafafa; color:#333; margin:0; }
header, footer { background:#f2f5f1; padding:15px 30px; text-align:center; }
h1 { color:#2e5d34; }
.container { max-width:800px; margin:30px auto; padding:20px; background:white; border-radius:8px; }
input, textarea { width:100%; padding:10px; margin:5px 0 15px; border:1px solid #ccc; border-radius:4px; }
button { background-color:#2e5d34; color:white; padding:10px 15px; border:none; border-radius:4px; cursor:pointer; }
button:hover { background-color:#244928; }
table { width:100%; border-collapse: collapse; margin-top:20px; }
th, td { padding:8px 12px; border:1px solid #ddd; text-align:left; }
th { background:#2e5d34; color:white; }
.total { text-align:right; font-weight:bold; margin-top:15px; }
.error { color:red; font-weight:bold; }
</style>
</head>
<body>

<header>
    <h1>Checkout</h1>
</header>

<div class="container">

<?php if ($order_submitted): ?>

    <h2>Thank you for your order!</h2>
    <p>Your Order ID is: <strong><?= $order_id ?></strong></p>

    <h3>Order Summary</h3>
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
        <?php foreach ($order_items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']); ?></td>
                <td><?= number_format($item['price'], 2); ?></td>
                <td><?= $item['quantity']; ?></td>
                <td><?= number_format($item['price'] * $item['quantity'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="total">Total Paid: £<?= number_format($total_amount, 2); ?></div>

<?php else: ?>

    <h2>Enter Delivery Details</h2>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="post" action="">
        <label for="address">Delivery Address</label>
        <textarea id="address" name="address" rows="4" required></textarea>

        <button type="submit">Pay Now</button>
    </form>

<?php endif; ?>

</div>

<footer>
    &copy; 2025 ShopSphere | Fresh, Local & Healthy
</footer>

<?php sqlsrv_close($conn_write); ?>
</body>
</html>
