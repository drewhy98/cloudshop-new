<?php
session_start();
require_once "db_write.php"; // <-- for write operations

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Make sure product_id is provided
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    die("Invalid request. No product specified.");
}

$product_id = (int) $_POST['product_id'];

// Insert into user_wishlist
$sql = "
    INSERT INTO user_wishlist (user_id, product_id)
    VALUES (?, ?)
";

$params = [$user_id, $product_id];

// Use try/catch to handle duplicate entries (UQ constraint)
try {
    $stmt = sqlsrv_query($conn_write, $sql, $params);

    if ($stmt === false) {
        $errors = sqlsrv_errors();
        // Check for unique constraint violation (duplicate)
        $duplicate = false;
        foreach ($errors as $error) {
            if (strpos($error['message'], 'UQ_User_Product') !== false) {
                $duplicate = true;
                break;
            }
        }

        if ($duplicate) {
            $_SESSION['wishlist_message'] = "This product is already in your wishlist.";
        } else {
            $_SESSION['wishlist_message'] = "Error adding to wishlist.";
        }
    } else {
        $_SESSION['wishlist_message'] = "Product added to your wishlist!";
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn_write);

    // Redirect back to previous page (or wishlist page)
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "wishlist.php"));
    exit();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
