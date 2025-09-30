<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles/Hstyle.css">
</head>
<body>

<div class="container">
    <!-- Welcome Message -->
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>

    <!-- Top Right Section: Profile & Logout -->
    <div class="top-right">
        <a href="profile.php" class="profile-link">
            <img src="assets/profile.png" alt="Profile">
        </a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div><br>
</div><br>
<div class="container">
    <!-- Food Order Ads -->
<h2 align='left'>Order now!</p>
    <div class="food-ads">
        <a href="order1.php?food=Puffs">
            <img src="assets/Puffs.png" alt="Puffs">
            <p>Puffs</p>
        </a>
        <a href="order1.php?food=burger">
            <img src="assets/Burger.jpg" alt="Burger">
            <p>Burger</p>
        </a>
        <a href="order1.php?food=Rolls">
            <img src="assets/Rolls.jpg" alt="Rolls">
            <p>Rolls</p>
        </a>
        <a href="order1.php?food=Meals">
            <img src="assets/Meals.jpg" alt="Meals">
            <p>Meals</p>
        </a>
        <a href="order1.php?food=Donut">
            <img src="assets/Donut.jpg" alt="Donut">
            <p>Donut</p>
        </a>
    </div><br><br>
    <div class="food-ads">
        <a href="order1.php?food=Icecream">
            <img src="assets/Ice cream.jpg" alt="Icecreams">
            <p>Icecreams</p>
        </a>
        <a href="order1.php?food=lemonsoda">
            <img src="assets/lemon soda.jpg" alt="lemonsoda">
            <p>Lemon Soda</p>
        </a>
        <a href="order1.php?food=Samosa">
            <img src="assets/Samosa.jpg" alt="Samosa">
            <p>Samosa</p>
        </a>
        <a href="order1.php?food=CB">
            <img src="assets/Chicken Biriyani.jpg" alt="CB">
            <p>Chicken Biriyani</p>
        </a>
        <a href="order1.php?food=Vada">
            <img src="assets/uzhunnuvada.jpg" alt="Vada">
            <p>Uzhunnu Vada</p>
        </a>
    </div>
</div>
    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <ul>
            <li><a href="order1.php">Place an Order</a></li>
            <li><a href="view_orders.php">View Orders</a></li>
        </ul>
    </div>
</div>

</body>
</html>
