<?php
session_start();

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php?error=" . urlencode("Please log in as an admin."));
    exit();
}

// Use your dbconnect
require_once "dbconnect.php"; // provides $mysqli

if (!$mysqli) {
    die("Database connection failed.");
}

// Get all registered users
$sql  = "SELECT name, email, password, created_at FROM shopusers ORDER BY created_at DESC";
$result = mysqli_query($mysqli, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($mysqli));
}

// Fetch all users
$users = [];
while ($row = mysqli_fetch_assoc($result)) {
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
/* ... same CSS as before ... */
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
mysqli_free_result($result);
mysqli_close($mysqli);
?>
</body>
</html>
