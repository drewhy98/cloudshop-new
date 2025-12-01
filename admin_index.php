<?php 
session_start(); 
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
    <title>ShopSphere - Admin Dashboard</title>

    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            background-color: #fafafa;
            color: #333;
            line-height: 1.6;
        }

        h1, h2, h3, h4 {
            font-family: 'Georgia', serif;
        }

        header {
            background-color: #ffffff;
            border-bottom: 1px solid #e0e0e0;
            padding: 15px 40px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            color: #2e5d34;
            font-size: 1.8em;
            letter-spacing: 1px;
            margin: 0;
        }

        .auth-links a, .auth-links span {
            color: #2e5d34;
            font-weight: bold;
            margin-left: 15px;
            text-decoration: none;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .logout-btn {
            background-color: #2e5d34;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .logout-btn:hover {
            background-color: #244928;
        }

        .search-bar {
            display: flex;
            align-items: center;
            flex-grow: 1;
            justify-content: center;
            margin: 10px 0;
        }

        .search-bar input {
            width: 60%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px 0 0 4px;
            outline: none;
        }

        .search-bar button {
            background-color: #2e5d34;
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }

        nav {
            background-color: #f2f5f1;
            padding: 12px 30px;
            display: flex;
            justify-content: center;
            gap: 35px;
            flex-wrap: wrap;
            border-bottom: 1px solid #ddd;
        }

        nav a {
            color: #2e5d34;
            font-weight: 600;
            font-size: 0.95em;
            text-decoration: none;
        }

        nav a:hover {
            border-bottom: 2px solid #2e5d34;
        }

        .featured {
            max-width: 1100px;
            margin: 50px auto 20px auto;
            text-align: center;
        }

        .featured h2 {
            color: #2e5d34;
            border-bottom: 2px solid #2e5d34;
            display: inline-block;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .featured img {
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        footer {
            background-color: #f2f5f1;
            color: #2e5d34;
            text-align: center;
            padding: 15px;
            margin-top: 60px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>

<header>
    <h1>ShopSphere Admin</h1>

    <form class="search-bar" method="get" action="#">
        <input type="text" name="search" placeholder="Search Stock...">
        <button type="submit">Search</button>
    </form>

    <div class="auth-links">
        <?php if (isset($_SESSION['admin_name'])): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>

            <form method="post" action="admin_logout.php" style="display:inline;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>

        <?php else: ?>
            <a href="admin_login.php">Admin Log In</a>
        <?php endif; ?>
    </div>
</header>

<nav>
    <a href="admin_view_products.php">Manage Products</a>
    <a href="admin_view_orders.php">Manage Orders</a>
    <a href="add_users.php">Sign Customers Up Automatically</a>
    <a href="display_users.php">View Registered Customers</a> |
    <!--  <a href="test_replication_lag.php">Test DB Replication</a> -->
</nav>

<section class="featured">
    <h2>Admin Dashboard</h2>
    <img src="https://www.groovypost.com/wp-content/uploads/2017/12/sign-in-security-feature.jpg"
         alt="admin">
</section>

<footer>
    <p>&copy; 2025 ShopSphere | Admin Panel</p>
</footer>

</body>
</html>
