<?php
session_start();
require_once "dbconnect.php"; // admin write access

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php?error=" . urlencode("Please log in as an admin."));
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category = strtolower(trim($_POST['category'])); // ensure lowercase
    $image_url = trim($_POST['image_url']);
    $stock = intval($_POST['stock']);

    $sql = "INSERT INTO products (name, price, category, created_at, image_url, stock) 
            VALUES (?, ?, ?, GETDATE(), ?, ?)";
    $params = [$name, $price, $category, $image_url, $stock];

    $stmt = sqlsrv_query($conn_write, $sql, $params);

    if ($stmt === false) {
        die("Error adding product: " . print_r(sqlsrv_errors(), true));
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn_write);

    header("Location: admin_display_products.php?msg=" . urlencode("Product added successfully."));
    exit();
} else {
    // redirect if someone accesses this file directly
    header("Location: admin_add_product.php");
    exit();
}
?>
