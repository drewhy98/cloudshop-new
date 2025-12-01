<?php
session_start();
require_once "db_write.php"; // admin needs write access

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php?error=" . urlencode("Please log in as an admin."));
    exit();
}

// Check required POST data
if (!isset($_POST['product_id'], $_POST['name'], $_POST['price'], $_POST['category'], $_POST['stock'])) {
    header("Location: admin_view_products.php?error=" . urlencode("Missing form data."));
    exit();
}

$product_id = intval($_POST['product_id']);
$name = trim($_POST['name']);
$price = floatval($_POST['price']);
$category = trim($_POST['category']);
$image_url = trim($_POST['image_url']);
$stock = intval($_POST['stock']);

// Update product in database
$update_sql = "UPDATE products
               SET name = ?, price = ?, category = ?, image_url = ?, stock = ?
               WHERE product_id = ?";
$params = [$name, $price, $category, $image_url, $stock, $product_id];

$update_stmt = sqlsrv_query($conn_write, $update_sql, $params);

if ($update_stmt === false) {
    die("Failed to update product: " . print_r(sqlsrv_errors(), true));
}

// Redirect back to product list with success message
header("Location: admin_view_products.php?msg=" . urlencode("Product updated successfully."));
exit();
