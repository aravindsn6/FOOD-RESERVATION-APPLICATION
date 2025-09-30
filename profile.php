<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

// Fetch user details
$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($db_username, $email);
$stmt->fetch();
$stmt->close();

// Handle username change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_username"])) {
    $new_username = trim($_POST["new_username"]);

    if (!empty($new_username) && $new_username !== $db_username) {
        // Check if username already exists
        $check_sql = "SELECT id FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $new_username);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows === 0) {
            $update_sql = "UPDATE users SET username = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $new_username, $user_id);

            if ($update_stmt->execute()) {
                $_SESSION["username"] = $new_username;
                echo "<script>alert('Username updated successfully!'); window.location='profile.php';</script>";
            } else {
                $error = "Failed to update username.";
            }
            $update_stmt->close();
        } else {
            $error = "Username already taken.";
        }
        $check_stmt->close();
    } else {
        $error = "Invalid or same username!";
    }
}

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_password"])) {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Fetch current hashed password
    $password_sql = "SELECT password FROM users WHERE id = ?";
    $password_stmt = $conn->prepare($password_sql);
    $password_stmt->bind_param("i", $user_id);
    $password_stmt->execute();
    $password_stmt->bind_result($db_password);
    $password_stmt->fetch();
    $password_stmt->close();

    if (password_verify($current_password, $db_password)) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_pass_sql = "UPDATE users SET password = ? WHERE id = ?";
            $update_pass_stmt = $conn->prepare($update_pass_sql);
            $update_pass_stmt->bind_param("si", $hashed_password, $user_id);

            if ($update_pass_stmt->execute()) {
                echo "<script>alert('Password updated successfully!'); window.location='profile.php';</script>";
            } else {
                $error = "Failed to update password.";
            }
            $update_pass_stmt->close();
        } else {
            $error = "New passwords do not match!";
        }
    } else {
        $error = "Current password is incorrect!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles/Vstyle.css">
</head>
<body>
    <div class="container">
        <h2>Profile</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        
        <p><strong>Username:</strong> <?php echo htmlspecialchars($db_username); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>

        <h3>Change Username</h3>
        <form method="POST">
            <input type="text" name="new_username" placeholder="Enter new username" required>
            <button type="submit" name="update_username">Update Username</button>
        </form>

        <h3>Change Password</h3>
        <form method="POST">
            <input type="password" name="current_password" placeholder="Current password" required><br><br>
            <input type="password" name="new_password" placeholder="New password" required><br><br>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required><br>
            <button type="submit" name="update_password">Update Password</button>
        </form>

        <?php
	$homePage = "dashboard.php"; // Default for normal users

	if (strpos($_SESSION["username"], "canteen") === 0) {
    		$homePage = "canteen_home.php";
	} elseif ($_SESSION["username"] === "admin") {
    		$homePage = "admin_home.php";
	}
	?>

	<a class="back-btn" href="<?php echo $homePage; ?>">Back to Home</a>

    </div>
</body>
</html>
