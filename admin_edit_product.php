<?php
session_start();
require_once "dbconnect.php"; // admin needs write access

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php?error=" . urlencode("Please log in as an admin."));
    exit();
}

// Get product ID
if (!isset($_GET['id'])) {
    header("Location: admin_view_products.php?error=" . urlencode("No product selected."));
    exit();
}

$product_id = intval($_GET['id']);

// Fetch current product details
$sql = "SELECT product_id, name, price, category, image_url, stock
        FROM products
        WHERE product_id = ?";
$stmt = sqlsrv_query($conn_write, $sql, [$product_id]);

if ($stmt === false || ($product = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) === null) {
    die("Product not found: " . print_r(sqlsrv_errors(), true));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - ShopSphere Admin</title>

    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            background-color: #fafafa;
            color: #333;
            line-height: 1.6;
        }

        h1, h2, h3, h4 {
            font-family: 'Georgia', serif;
        }

        header {
            background-color: #ffffff;
            border-bottom: 1px solid #e0e0e0;
            padding: 15px 40px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            color: #2e5d34;
            font-size: 1.8em;
            letter-spacing: 1px;
            margin: 0;
        }

        .auth-links a, .auth-links span {
            color: #2e5d34;
            font-weight: bold;
            margin-left: 15px;
            text-decoration: none;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .logout-btn {
            background-color: #2e5d34;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .logout-btn:hover {
            background-color: #244928;
        }

        .search-bar {
            display: flex;
            align-items: center;
            flex-grow: 1;
            justify-content: center;
            margin: 10px 0;
        }

        .search-bar input {
            width: 60%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px 0 0 4px;
            outline: none;
        }

        .search-bar button {
            background-color: #2e5d34;
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }

        nav {
            background-color: #f2f5f1;
            padding: 12px 30px;
            display: flex;
            justify-content: center;
            gap: 35px;
            flex-wrap: wrap;
            border-bottom: 1px solid #ddd;
        }

        nav a {
            color: #2e5d34;
            font-weight: 600;
            font-size: 0.95em;
            text-decoration: none;
        }

        nav a:hover {
            border-bottom: 2px solid #2e5d34;
        }

        /* ---- PAGE CONTENT ---- */

        .container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        .container h2 {
            color: #2e5d34;
            text-align: center;
            border-bottom: 2px solid #2e5d34;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-top: 12px;
            display: block;
            color: #2e5d34;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button[type="submit"] {
            background-color: #2e5d34;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 20px;
        }

        button[type="submit"]:hover {
            background-color: #244928;
        }

        footer {
            background-color: #f2f5f1;
            color: #2e5d34;
            text-align: center;
            padding: 15px;
            margin-top: 60px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>

<header>
    <h1>ShopSphere Admin</h1>

    <form class="search-bar" method="get" action="#">
        <input type="text" name="search" placeholder="Search Stock...">
        <button type="submit">Search</button>
    </form>

    <div class="auth-links">
        <?php if (isset($_SESSION['admin_name'])): ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION['admin_name']); ?></span>

            <form method="post" action="admin_logout.php" style="display:inline;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>

        <?php else: ?>
            <a href="admin_login.php">Admin Log In</a>
        <?php endif; ?>
    </div>
</header>

<nav>
    <a href="admin_view_products.php">Manage Products</a>
    <a href="admin_view_orders.php">Manage Orders</a>

</nav>

<div class="container">
    <h2>Edit Product</h2>

    <form method="post" action="admin_process_edit_product.php">
        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">

        <label for="name">Product Name</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']); ?>" required>

        <label for="price">Price (£)</label>
        <input type="number" step="0.01" name="price" id="price" value="<?= htmlspecialchars($product['price']); ?>" required>

        <label for="category">Category</label>
        <input type="text" name="category" id="category" value="<?= htmlspecialchars($product['category']); ?>" required>

        <label for="image_url">Image URL</label>
        <input type="text" name="image_url" id="image_url" value="<?= htmlspecialchars($product['image_url']); ?>">

        <label for="stock">Stock Quantity</label>
        <input type="number" name="stock" id="stock" value="<?= htmlspecialchars($product['stock']); ?>" min="0" required>

        <button type="submit">Update Product</button>
    </form>
</div>

<footer>
    &copy; <?= date("Y") ?> ShopSphere Admin Panel
</footer>

</body>
</html>
