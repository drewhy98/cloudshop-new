<?php 
session_start(); 
require_once "db_write.php"; // admin needs write permission

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
    <title>Admin - Manage Products</title>

    <style>
        body { font-family:'Helvetica Neue',Arial; margin:0; background:#fafafa; color:#333; }

        /* Header */
        header { background:white; padding:15px 40px; border-bottom:1px solid #ddd; 
                 display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; }
        header h1 { color:#2e5d34; font-family:'Georgia'; margin:0; }

        .logout-btn { padding:6px 12px; background:#2e5d34; border:none; 
                      border-radius:4px; color:white; cursor:pointer; }

        /* Navigation */
        nav { background:#f2f5f1; padding:12px 30px; 
              display:flex; justify-content:center; gap:30px; flex-wrap:wrap; }
        nav a { text-decoration:none; color:#2e5d34; font-weight:bold; }

        .page-header {
            max-width:1200px;
            margin:20px auto;
            padding:0 20px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .add-product-btn {
            background:#2e5d34;
            color:white;
            padding:8px 14px;
            border:none;
            border-radius:4px;
            cursor:pointer;
            font-weight:bold;
        }

        .products-grid {
            max-width:1200px;
            margin:20px auto;
            display:grid;
            grid-template-columns:repeat(auto-fill, minmax(250px, 1fr));
            gap:25px;
            padding:0 20px;
        }

        .product-card {
            background:white;
            border:1px solid #ddd;
            border-radius:8px;
            padding:15px;
            text-align:center;
            position:relative;
            transition:0.2s;
        }

        .product-card:hover { box-shadow:0 3px 10px rgba(0,0,0,0.1); }

        .product-card img {
            width:100%;
            height:180px;
            object-fit:contain;
            border-radius:5px;
            background:#fafafa;
        }

        .edit-icon {
            position:absolute;
            top:10px;
            right:10px;
            background:#2e5d34;
            color:white;
            padding:6px;
            border-radius:50%;
            font-size:14px;
            text-decoration:none;
        }

        .edit-icon:hover { background:#244928; }

        footer {
            background:#f2f5f1;
            text-align:center;
            padding:15px;
            border-top:1px solid #ddd;
            color:#2e5d34;
            margin-top:40px;
        }
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

<div class="page-header">
    <h2 style="color:#2e5d34;">All Products</h2>
    <a href="admin_add_product.php">
        <button class="add-product-btn">+ Add New Product</button>
    </a>
</div>

<div class="products-grid">

<?php
$sql = "SELECT product_id, name, price, image_url, stock
        FROM products 
        ORDER BY created_at DESC";

$stmt = sqlsrv_query($conn_write, $sql);

if ($stmt === false) {
    echo "<p>Error: Could not load products.</p>";
} else {
    $hasProducts = false;

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)):
        $hasProducts = true;
        $img = !empty($row['image_url']) ? htmlspecialchars($row['image_url']) : "placeholder.png";
?>
<div class="product-card">
    
    <!-- EDIT ICON -->
    <a class="edit-icon" href="admin_edit_product.php?id=<?= $row['product_id'] ?>">✏️</a>

    <img src="<?= $img ?>" alt="<?= htmlspecialchars($row['name']); ?>">
    <h4><?= htmlspecialchars($row['name']); ?></h4>
    <p>£<?= number_format($row['price'], 2); ?></p>
    <p>Stock: <?= intval($row['stock']); ?></p>
</div>

<?php
    endwhile;

    if (!$hasProducts) {
        echo "<p style='grid-column:1/-1;text-align:center;'>No products found.</p>";
    }
}
?>

</div>

<footer>
    &copy; <?= date("Y") ?> ShopSphere Admin
</footer>

<?php
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn_write);
?>

</body>
</html>
