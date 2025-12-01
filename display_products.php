<?php 
session_start(); 
require_once "dbconnect.php";   // provides $mysqli
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopSphere - Bakery</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; margin: 0; background-color: #fafafa; color: #333; line-height: 1.6; }
        h1,h2,h3,h4{font-family:'Georgia',serif;}
        header{background:#fff; border-bottom:1px solid #e0e0e0; padding:15px 40px; display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center;}
        header h1{color:#2e5d34; font-size:1.8em; margin:0;}
        .auth-links a, .auth-links span{color:#2e5d34; font-weight:bold; margin-left:12px; text-decoration:none;}
        .logout-btn{background:#2e5d34;color:white;border:none;padding:5px 10px;border-radius:4px;cursor:pointer;font-weight:bold;}
        nav{background:#f2f5f1;padding:12px 30px;display:flex;justify-content:center;gap:35px;flex-wrap:wrap;border-bottom:1px solid #ddd;}
        nav a{color:#2e5d34;font-weight:600;font-size:0.95em;text-decoration:none;}
        .page-title{text-align:center;margin:40px 0 10px;color:#2e5d34;font-size:2em;}
        .products-grid{max-width:1200px;margin:30px auto;display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:25px;padding:0 20px;}
        .product-card{background:white;border:1px solid #e0e0e0;border-radius:8px;padding:15px;text-align:center;transition:box-shadow 0.2s;}
        .product-card:hover{box-shadow:0 3px 10px rgba(0,0,0,0.08);}
        .product-card img{width:100%;height:180px;object-fit:contain;background:#fafafa;border-radius:5px;}
        .product-card h4{color:#2e5d34;margin:10px 0 5px;}
        .product-card p{color:#555;}
        .btn{background:#2e5d34;color:white;padding:8px 14px;border:none;border-radius:4px;cursor:pointer;margin-top:6px;}
        .btn:hover{background-color:#244928;}
        footer{margin-top:50px;background:#f2f5f1;padding:15px;text-align:center;color:#2e5d34;border-top:1px solid #ddd;}
    </style>
</head>
<body>

<header>
    <h1>ShopSphere</h1>
    <form class="search-bar" method="get">
        <input type="text" name="search" placeholder="Search Stock...">
        <button type="submit">Search</button>
    </form>
    <div class="auth-links">
        <?php if(isset($_SESSION['user_name'])): ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?></span>
            <form method="post" action="logout.php" style="display:inline;">
                <button class="logout-btn">Logout</button>
            </form>
        <?php else: ?>
            <a href="login.php">Log In</a> |
            <a href="register.php">Sign Up</a>
        <?php endif; ?>
    </div>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="display_meat.php">Meat</a>
    <a href="display_veg.php">Vegetables</a>
    <a href="display_bakery.php">Bakery</a>
    <a href="display_products.php">View All Products</a>
</nav>

<h2 class="page-title">Full Product Selection</h2>

<div class="products-grid">

<?php
// ---- MYSQL VERSION ---- //
$sql = "SELECT product_id, name, price, image_url 
        FROM products 
        ORDER BY created_at DESC";

$result = $mysqli->query($sql);

if (!$result) {
    echo "<p>Error retrieving products.</p>";
} else {
    if ($result->num_rows === 0) {
        echo "<p style='grid-column:1 / -1; text-align:center;'>No products found.</p>";
    } else {
        while ($row = $result->fetch_assoc()):
            $image = !empty($row['image_url']) ? htmlspecialchars($row['image_url']) : 'placeholder.png';
?>
    <div class="product-card">
        <img src="<?= $image ?>" alt="<?= htmlspecialchars($row['name']); ?>">
        <h4><?= htmlspecialchars($row['name']); ?></h4>
        <p>£<?= number_format($row['price'], 2); ?></p>

        <?php if(isset($_SESSION['user_name'])): ?>
            <form method="post" action="add_to_cart.php">
                <input type="hidden" name="product_id" value="<?= $row['product_id']; ?>">
                <button class="btn">Add to Cart</button>
            </form>

            <form method="post" action="add_to_wishlist.php">
                <input type="hidden" name="product_id" value="<?= $row['product_id']; ?>">
                <button class="btn">Add to Wishlist</button>
            </form>
        <?php endif; ?>
    </div>
<?php
        endwhile;
    }
}
?>

</div>

<footer>
    <p>&copy; 2025 ShopSphere | Fresh, Local & Healthy</p>
</footer>

<?php
$mysqli->close();
?>
</body>
</html>
