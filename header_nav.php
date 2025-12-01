<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header>
    <h1>ShopSphere</h1>
    <div class="auth-links">
        <?php if (isset($_SESSION['user_name'])): ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?></span>

            <form method="post" action="logout.php" style="display:inline;">
                <button class="logout-btn">Logout</button>
            </form>

            <!-- Links styled as text, not buttons -->
            <a href="wishlist.php">View Wishlist</a> |
            <a href="basket.php">View Basket</a> |
            <a href="view_orders.php">View Orders</a>

        <?php else: ?>
            <a href="login.php">Log In</a> |
            <a href="register.php">Sign Up</a>
        <?php endif; ?>
    </div>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="display_meat.php">Meat</a>
    <a href="display_veg.php">Vegetables</a>
    <a href="display_bakery.php">Bakery</a>
</nav>

<style>
header { 
    background: #fff; 
    border-bottom: 1px solid #e0e0e0; 
    padding: 15px 40px; 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
}

header h1 { 
    color: #2e5d34; 
    margin: 0; 
}

.auth-links span { 
    margin-right: 12px; 
    font-weight: bold; 
}

.auth-links a {
    color: #2e5d34;
    font-weight: bold;
    text-decoration: none;
    margin-left: 10px;
}

.auth-links a:hover {
    text-decoration: underline;
}

.logout-btn { 
    background: #2e5d34; 
    color: #fff; 
    border: none; 
    padding: 5px 10px; 
    border-radius: 4px; 
    cursor: pointer; 
    font-weight: bold; 
}

.logout-btn:hover { 
    background-color: #244928; 
}

nav { 
    background: #f2f5f1; 
    padding: 12px 30px; 
    text-align: center; 
}

nav a { 
    margin: 0 15px; 
    text-decoration: none; 
    color: #2e5d34; 
    font-weight: 600; 
}

nav a:hover { 
    border-bottom: 2px solid #2e5d34; 
}
</style>
