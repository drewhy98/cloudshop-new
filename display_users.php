<?php
session_start();

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php?error=" . urlencode("Please log in as an admin."));
    exit();
}

// Use MySQLi database
require_once "dbconnect.php"; // provides $mysqli

if (!$mysqli) {
    die("Database connection failed.");
}

// Get all registered users
$sql  = "SELECT name, email, password, created_at FROM shopusers ORDER BY created_at DESC";
$result = $mysqli->query($sql);

if (!$result) {
    die("Query failed: " . $mysqli->error);
}

// Fetch all users
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Registered Users</title>
<style>
body {
    font-family: 'Helvetica Neue', Arial, sans-serif;
    margin: 0;
    background: #fafafa;
    color: #333;
}
header {
    background: #fff;
    border-bottom: 1px solid #ddd;
    padding: 15px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
header h1 {
    color: #2e5d34;
    font-family: 'Georgia', serif;
    margin: 0;
}
.logout-btn {
    padding: 6px 12px;
    background: #2e5d34;
    border: none;
    border-radius: 4px;
    color: white;
    cursor: pointer;
}
nav {
    background: #f2f5f1;
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
    padding: 12px 0;
}
nav a {
    text-decoration: none;
    color: #2e5d34;
    font-weight: bold;
}
.container {
    max-width: 900px;
    margin: 30px auto;
    background: white;
    padding: 20px;
    border-radius: 8px;
}
.user-count {
    background: #e8f5e8;
    padding: 10px;
    border-radius: 5px;
    margin: 10px 0;
    text-align: center;
    font-weight: bold;
}
.all-users {
    background: #f9f9f9;
    padding: 20px;
    margin: 20px 0;
    border-radius: 5px;
    max-height: 400px;
    overflow-y: auto;
}
.all-users h3 {
    text-align: center;
    margin-top: 0;
    color: #333;
}
.user-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}
.user-table th, .user-table td {
    padding: 8px 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
.user-table th {
    background-color: #2e5d34;
    color: white;
}
.user-table tr:hover {
    background-color: #f5f5f5;
}
.password-hash {
    font-family: monospace;
    font-size: 10px;
    color: #666;
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
}
.btn {
    display: inline-block;
    background-color: #2e5d34;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    margin: 5px 0;
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

<div class="container">
    <h2>Registered Users</h2>
    <div class="user-count">
        Total Registered Users: <?= count($users) ?>
    </div>

    <?php if (count($users) > 0): ?>
        <div class="all-users">
            <h3>All Users (Newest First)</h3>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Password Hash</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td class="password-hash" title="<?= htmlspecialchars($user['password']) ?>">
                                <?= htmlspecialchars(substr($user['password'], 0, 20) . '...') ?>
                            </td>
                            <td><?= htmlspecialchars($user['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;">
            No users found in the database.
        </div>
    <?php endif; ?>

    <a href="admin_index.php" class="btn">Home</a>
</div>

<?php
$result->free();
$mysqli->close();
?>

</body>
</html>
