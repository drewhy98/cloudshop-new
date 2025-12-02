<?php
session_start();
require_once "dbconnect.php"; // MySQLi connection variable: $mysqli

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $address = trim($_POST['address']);
    $payment_method = trim($_POST['payment_method']);

    if (empty($address) || empty($payment_method)) {
        $error = "Please complete all fields.";
    } else {

        // Fetch basket items
        $sql_basket = "
            SELECT c.product_id, c.quantity, p.price, p.name
            FROM user_cart c
            JOIN products p ON c.product_id = p.product_id
            WHERE c.user_id = ?
        ";

        $stmt = $mysqli->prepare($sql_basket);
        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        $total_amount = 0;
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
            $total_amount += $row['price'] * $row['quantity'];
        }
        $stmt->close();

        if (count($items) === 0) {
            $error = "Your basket is empty.";
        } else {

            // Start transaction
            $mysqli->begin_transaction();

            try {
                // Insert order
                $sql_order = "
                    INSERT INTO orders (user_id, total_amount, address, payment_method, status)
                    VALUES (?, ?, ?, ?, 'Pending')
                ";
                $stmt_order = $mysqli->prepare($sql_order);
                if (!$stmt_order) {
                    throw new Exception("Prepare failed: " . $mysqli->error);
                }
                $stmt_order->bind_param("idss", $user_id, $total_amount, $address, $payment_method);
                $stmt_order->execute();
                $order_id = $stmt_order->insert_id;
                $stmt_order->close();

                // Insert order items
                $sql_item = "
                    INSERT INTO order_items (order_id, product_id, quantity, price)
                    VALUES (?, ?, ?, ?)
                ";
                $stmt_item = $mysqli->prepare($sql_item);
                if (!$stmt_item) {
                    throw new Exception("Prepare failed: " . $mysqli->error);
                }

                foreach ($items as $item) {
                    $stmt_item->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                    $stmt_item->execute();
                }
                $stmt_item->close();

                // Clear basket
                $stmt_clear = $mysqli->prepare("DELETE FROM user_cart WHERE user_id = ?");
                if (!$stmt_clear) {
                    throw new Exception("Prepare failed: " . $mysqli->error);
                }
                $stmt_clear->bind_param("i", $user_id);
                $stmt_clear->execute();
                $stmt_clear->close();

                // Commit transaction
                $mysqli->commit();

                // Redirect to confirmation
                header("Location: checkout_confirm.php?order_id=" . $order_id);
                exit();

            } catch (Exception $e) {
                $mysqli->rollback();
                $error = "Failed to process order: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - ShopSphere</title>
<style>
body { font-family: Arial, sans-serif; background:#fafafa; margin:0; }
.container { max-width:600px; margin:50px auto; background:white; padding:20px; border-radius:8px; }
h1 { color:#2e5d34; }
label { display:block; margin-top:10px; font-weight:bold; }
input[type=text], select { width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:4px; }
button { margin-top:15px; padding:10px 15px; background:#2e5d34; color:white; border:none; border-radius:4px; cursor:pointer; }
button:hover { background:#244928; }
.error { color:red; font-weight:bold; margin-top:10px; }
</style>
</head>
<body>

<?php include "header_nav.php"; ?>

<div class="container">
<h1>Checkout</h1>

<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

<form method="post" action="">
    <label for="address">Delivery Address:</label>
    <input type="text" name="address" id="address" required>

    <label for="payment_method">Payment Method:</label>
    <select name="payment_method" id="payment_method" required>
        <option value="">Select Payment Method</option>
        <option value="Card">Card</option>
        <option value="PayPal">PayPal</option>
        <option value="Cash on Delivery">Cash on Delivery</option>
    </select>

    <button type="submit">Submit Order</button>
</form>
</div>

<footer>&copy; 2025 ShopSphere | Fresh, Local & Healthy</footer>

<?php $mysqli->close(); ?>
</body>
</html>
