<?php
session_start();
require_once "dbconnect.php"; // $mysqli read-only is enough

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Wishlist - ShopSphere</title>

<style>
body {
    font-family: 'Helvetica Neue', Arial, sans-serif;
    background: #fafafa;
    color: #333;
    margin: 0;
    line-height: 1.6;
}
header {
    background: #fff;
    border-bottom: 1px solid #e0e0e0;
    padding: 15px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
header h1 { color: #2e5d34; margin: 0; }
nav { background: #f2f5f1; padding: 12px 30px; text-align: center; }
nav a { margin: 0 15px; text-decoration: none; color: #2e5d34; font-weight: 600; }
.products-grid {
    max-width: 1200px;
    margin: 30px auto;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 25px;
    padding: 0 20px;
}
.product-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
}
.product-card img {
    width: 100%;
    height: 180px;
    object-fit: contain;
    background: #fafafa;
    border-radius: 5px;
}
.product-card h4 { color: #2e5d34; margin: 10px 0 5px; }
.product-card p { color: #555; }
.btn {
    background-color: #2e5d34;
    color: white;
    padding: 8px 14px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 6px;
}
.btn:hover { background-color: #244928; }
footer {
    margin-top: 50px;
    background: #f2f5f1;
    padding: 15px;
    text-align: center;
    color: #2e5d34;
    border-top: 1px solid #ddd;
}
</style>
</head>
<body>

<?php include "header_nav.php"; ?>

<h2 style="text-align:center; margin:40px 0; color:#2e5d34;">My Wishlist</h2>

<div class="products-grid">

<?php
// Query wishlist products for this user
$sql = "
    SELECT p.product_id, p.name, p.price, p.image_url
    FROM user_wishlist uw
    JOIN products p ON uw.product_id = p.product_id
    WHERE uw.user_id = ?
    ORDER BY uw.added_at DESC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='grid-column:1/-1; text-align:center;'>Your wishlist is empty.</p>";
} else {
    while ($row = $result->fetch_assoc()):
        $image = !empty($row['image_url']) ? htmlspecialchars($row['image_url']) : "placeholder.png";
?>

    <div class="product-card">
        <img src="<?= $image ?>" alt="<?= htmlspecialchars($row['name']); ?>">
        <h4><?= htmlspecialchars($row['name']); ?></h4>
        <p>£<?= number_format($row['price'], 2); ?></p>

        <form method="post" action="remove_from_wishlist.php">
            <input type="hidden" name="product_id" value="<?= $row['product_id']; ?>">
            <button class="btn">Remove from Wishlist</button>
        </form>
    </div>

<?php
    endwhile;
}

$stmt->close();
$mysqli->close();
?>

</div>

<footer>
    <p>&copy; 2025 ShopSphere | Fresh, Local & Healthy</p>
</footer>

</body>
</html>
