<?php
session_start();
require_once "dbconnect.php"; // <-- MySQLi connection ($mysqli)

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php?error=" . urlencode("Please log in as an admin."));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Add Product</title>
<style>
body { font-family:'Helvetica Neue',Arial; margin:0; background:#fafafa; color:#333; }
header, nav { padding:15px 40px; }
header { background:white; border-bottom:1px solid #ddd; display:flex; justify-content:space-between; align-items:center; }
header h1 { color:#2e5d34; font-family:'Georgia'; margin:0; }
.logout-btn { padding:6px 12px; background:#2e5d34; border:none; border-radius:4px; color:white; cursor:pointer; }
nav { background:#f2f5f1; display:flex; justify-content:center; gap:30px; flex-wrap:wrap; }
nav a { text-decoration:none; color:#2e5d34; font-weight:bold; }
.container { max-width:600px; margin:30px auto; background:white; padding:20px; border-radius:8px; }
form label { display:block; margin-top:10px; font-weight:bold; }
form input, form select { width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:4px; }
form button { margin-top:15px; padding:8px 14px; background:#2e5d34; color:white; border:none; border-radius:4px; cursor:pointer; }
form button:hover { background:#244928; }
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
    <a href="admin_display_products.php">Manage Products</a>
</nav>

<div class="container">
    <h2>Add New Product</h2>
    <form method="post" action="admin_process_add_product.php">
        <label for="name">Product Name</label>
        <input type="text" name="name" id="name" required>

        <label for="price">Price (£)</label>
        <input type="number" name="price" id="price" step="0.01" required>

        <label for="category">Category</label>
        <select name="category" id="category" required>
            <option value="meat">meat</option>
            <option value="veg">veg</option>
            <option value="bakery">bakery</option>
        </select>

        <label for="image_url">Image URL</label>
        <input type="text" name="image_url" id="image_url">

        <label for="stock">Stock Quantity</label>
        <input type="number" name="stock" id="stock" value="0" min="0" required>

        <button type="submit">Add Product</button>
    </form>
</div>

<footer style="background:#f2f5f1;text-align:center;padding:15px;margin-top:40px;color:#2e5d34;border-top:1px solid #ddd;">
    &copy; <?= date("Y") ?> ShopSphere Admin
</footer>

</body>
</html>
