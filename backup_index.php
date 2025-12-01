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
            </form>
        <?php else: ?>
            <a href="login.php">Log In</a> |
            <a href="register.php">Sign Up</a> |
            <a href="add_users.php">Sign Up Automatically</a> |
            <a href="display_users.php">View Registered Users</a> |
            <a href="test_replication_lag.php">Test DB Replication</a>
        <?php endif; ?>
    </div>
</header>

<nav>
    <a href="#meat">Meat</a>
    <a href="#veg">Vegetables</a>
    <a href="#bakery">Bakery</a>
</nav>

<!-- MAIN FEATURED -->
<section class="featured">
    <h2>Featured This December</h2>
    <img src="https://learn.surlatable.com/wp-content/uploads/2022/12/Tips-for-planning-your-Christmas-dinner.jpg"
         alt="Christmas Dinner">
</section>

<!-- FEATURED MEAT -->
<section class="sub-featured" id="meat">
    <h3>Meat</h3>

    <div class="products">

        <div class="product">
            <img src="https://www.cookingclassy.com/wp-content/uploads/2017/12/honey-glazed-ham-4.jpg" alt="Honey Roast Ham">
            <h4>Honey Roast Ham</h4>
            <p>£24.99 / joint</p>
            <button>Add to Cart</button>
        </div>

        <div class="product">
            <img src="https://www.inspiredtaste.net/wp-content/uploads/2023/11/Roasted-Turkey-Recipe-1-1200.jpg" alt="Roast Turkey">
            <h4>Roast Turkey</h4>
            <p>£39.99 / bird</p>
            <button>Add to Cart</button>
        </div>

        <div class="product">
            <img src="https://www.tasteofhome.com/wp-content/uploads/2025/01/Herb-Crusted-Roast-Beef_EXPS_FT25_9187_EC_0108_1.jpg"
                 alt="Roast Beef">
            <h4>Roast Beef</h4>
            <p>£24.99 / joint</p>
            <button>Add to Cart</button>
        </div>

        <div class="product">
            <img src="https://badleysbutchers.co.uk/cdn/shop/products/Whole_Chicken_6a5911ed-47a9-46fc-9bdf-437b968dfade.jpg?v=1577375639"
                 alt="Chicken">
            <h4>Chicken</h4>
            <p>£19.99 / bird</p>
            <button>Add to Cart</button>
        </div>

    </div>
</section>

<!-- FEATURED VEGETABLES -->
<section class="sub-featured" id="veg">
    <h3>Vegetables</h3>

    <div class="products">

        <div class="product">
            <img src="https://www.closetcooking.com/wp-content/uploads/2023/11/Honey-Balsamic-Roasted-Carrots-1200-1969.jpg"
                 alt="Carrots">
            <h4>Carrots</h4>
            <p>£1.99 / kg</p>
            <button>Add to Cart</button>
        </div>

        <div class="product">
            <img src="https://healthylivingjames.co.uk/wp-content/uploads/2024/07/Air-Fryer-Tenderstem-Broccoli-Square.jpg"
                 alt="Tenderstem Broccoli">
            <h4>Tenderstem Broccoli</h4>
            <p>£1.99 / kg</p>
            <button>Add to Cart</button>
        </div>

        <div class="product">
            <img src="https://tinandthyme.uk/wp-content/uploads/2023/01/Cooked-Brussels-Sprouts.jpg"
                 alt="Brussel Sprouts">
            <h4>Brussel Sprouts</h4>
            <p>£2.50 / kg</p>
            <button>Add to Cart</button>
        </div>

        <div class="product">
            <img src="https://www.allrecipes.com/thmb/FBpzlDkBGve3sv9KkapC0gTJTw8=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/1221_MW021_jennifer-causey-2000-5776fb1d3586436bb7c4d1453da299f6.jpg"
                 alt="Parsnips">
            <h4>Parsnips</h4>
            <p>£2.20 / kg</p>
            <button>Add to Cart</button>
        </div>

    </div>
</section>

<!-- FEATURED BAKERY -->
<section class="sub-featured" id="bakery">
    <h3>Bakery</h3>

    <div class="products">

        <div class="product">
            <img src="https://ichef.bbci.co.uk/food/ic/food_16x9_832/recipes/classic_christmas_cake_04076_16x9.jpg"
                 alt="Christmas Cake">
            <h4>Christmas Cake</h4>
            <p>£14.99 each</p>
            <button>Add to Cart</button>
        </div>

        <div class="product">
            <img src="https://culinaryginger.com/wp-content/uploads/Christmas-Sweet-Mince-Pies-7.jpg"
                 alt="Mince Pies">
            <h4>Mince Pies</h4>
            <p>£3.99 / 12 pack</p>
            <button>Add to Cart</button>
        </div>

        <div class="product">
            <img src="https://horizon.com/wp-content/uploads/recipe-cin-roll-hero.jpg"
                 alt="Cinnamon Rolls">
            <h4>Cinnamon Rolls</h4>
            <p>£2.99 / 4 pack</p>
            <button>Add to Cart</button>
        </div>

        <div class="product">
            <img src="https://sarahsvegankitchen.com/wp-content/uploads/2024/05/Vegan-Croissants-1.jpg"
                 alt="Croissants">
            <h4>Croissants</h4>
            <p>£1.99 / 6 pack</p>
            <button>Add to Cart</button>
        </div>

    </div>
</section>

<footer>
    <p>&copy; 2025 ShopSphere | Fresh, Local & Healthy</p>
</footer>

</body>
</html>
