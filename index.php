<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopSphere - Christmas Dinner Specials</title>

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

        .search-bar button:hover {
            background-color: #244928;
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

        .sub-featured {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .sub-featured h3 {
            color: #2e5d34;
            border-bottom: 2px solid #2e5d34;
            padding-bottom: 5px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.4em;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        .product {
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            text-align: center;
            padding: 15px;
            transition: box-shadow 0.3s;
        }

        .product:hover {
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .product img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 5px;
        }

        .product h4 {
            color: #2e5d34;
            font-size: 1.1em;
            margin: 10px 0 5px;
        }

        .product p {
            color: #555;
            margin: 5px 0 10px;
        }

        .product button {
            background-color: #2e5d34;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 4px;
            cursor: pointer;
        }

        .product button:hover {
            background-color: #244928;
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
    <h1>ShopSphere</h1>

    <form class="search-bar" method="get" action="#">
        <input type="text" name="search" placeholder="Search Stock...">
        <button type="submit">Search</button>
    </form>

    <div class="auth-links">
        <?php if (isset($_SESSION['user_name'])): ?>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <form method="post" action="logout.php" style="display:inline;">
                <button type="submit" class="logout-btn">Logout</button>
                <a href="wishlist.php">View Wishlist</a> |
                <a href="basket.php">View Basket</a> |
                <a href="view_orders.php">View Orders</a> |
            </form>
        <?php else: ?>
            <a href="login.php">Customer Log In</a> |
            <a href="register.php">Customer Sign Up</a> |
            <a href="admin_login.php">Admin Log In</a> |
          <!--  <a href="add_users.php">Sign Up Automatically</a> | -->
            <!--  <a href="display_users.php">View Registered Users</a> |-->
          <!--  <a href="test_replication_lag.php">Test DB Replication</a> -->
        <?php endif; ?>
    </div>
</header>

<nav>
    <a href="display_meat.php">Meat</a>
    <a href="display_veg.php">Vegetables</a>
    <a href="display_bakery.php">Bakery</a>
    <a href="display_products.php">View all Products</a>
</nav>

<!-- MAIN FEATURED -->
<section class="featured">
    <h2>Featured This December</h2>
    <img src="https://learn.surlatable.com/wp-content/uploads/2022/12/Tips-for-planning-your-Christmas-dinner.jpg"
         alt="Christmas Dinner">
</section>

    </div>
</section>

<footer>
    <p>&copy; 2025 ShopSphere | Fresh, Local & Healthy</p>
</footer>

</body>
</html>






