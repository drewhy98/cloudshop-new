<?php
session_start();
require_once "dbconnect.php"; // admin needs write access (MySQL)

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
$name       = trim($_POST['name']);
$price      = floatval($_POST['price']);
$category   = trim($_POST['category']);
$image_url  = trim($_POST['image_url']);
$stock      = intval($_POST['stock']);

// MySQL update query
$update_sql = "
    UPDATE products
    SET name = ?, price = ?, category = ?, image_url = ?, stock = ?
    WHERE product_id = ?
";

$stmt = $conn_write->prepare($update_sql);

if (!$stmt) {
    die("Prepare failed: " . $conn_write->error);
}

// (s = string, d = double, i = integer)
$stmt->bind_param("sdssii", $name, $price, $category, $image_url, $stock, $product_id);

if (!$stmt->execute()) {
    die("Failed to update product: " . $stmt->error);
}

$stmt->close();
$conn_write->close();

header("Location: admin_view_products.php?msg=" . urlencode("Product updated successfully."));
exit();
?>
