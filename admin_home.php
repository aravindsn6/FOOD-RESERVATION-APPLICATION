<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles/AHstyle.css">
</head>
<body>
    <h2>Welcome <?php echo $_SESSION["username"]; ?>!</h2>
     <div class="top-right">
        <a href="profile.php" class="profile-link">
           <img src="assets/profile.png" alt="Profile">
        </a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div><hr><br>
    <div valign="center">
    <center><div  class="btn">
	<a href="admin_manage_users.php">Manage Users</a>
    </div></center>
    <center><div class="btn">
	<a href="admin_manage_price.php">Manage Price</a>
    </div></center>
    </div>
</body>
</html>
