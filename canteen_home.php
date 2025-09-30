<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canteen Staff Home</title>
    <link rel="stylesheet" href="styles/CHstyle.css"> <!-- Using same style as view_order page -->
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
    }
    $canteen_staff = $_SESSION["username"];
    ?>

   <div class="top-right">
        <a href="profile.php" class="profile-link">
           <img src="assets/profile.png" alt="Profile">
        </a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>


    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($canteen_staff); ?>!</h2>
    </div>
        <div class="buttons">
            <a href="update_order.php" class="btn">View/Update Orders</a>
	</div>
	
    	<div class="buttons">
	    <a href="user_management.php" class="btn">Manage Customers</a>
        </div>
	<div class="buttons">
	    <a href="update_menu.php" class="btn">Update Menu</a>
        </div>
</body>
</html>
