<?php
session_start();
require_once "dbconnect.php"; // admin write access (mysqli connection)

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php?error=" . urlencode("Please log in as an admin."));
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name      = trim($_POST['name']);
    $price     = floatval($_POST['price']);
    $category  = strtolower(trim($_POST['category']));
    $image_url = trim($_POST['image_url']);
    $stock     = intval($_POST['stock']);

    // MySQL INSERT using NOW()
    $sql = "INSERT INTO products (name, price, category, created_at, image_url, stock)
            VALUES (?, ?, ?, NOW(), ?, ?)";

    $stmt = $conn_write->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn_write->error);
    }

    $stmt->bind_param("sdssi", $name, $price, $category, $image_url, $stock);

    if (!$stmt->execute()) {
        die("Error adding product: " . $stmt->error);
    }

    $stmt->close();
    $conn_write->close();

    header("Location: admin_display_products.php?msg=" . urlencode("Product added successfully."));
    exit();

} else {
    // Redirect if file accessed directly
    header("Location: admin_add_product.php");
    exit();
}
?>
