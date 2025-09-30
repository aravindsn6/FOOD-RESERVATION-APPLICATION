<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? '');
    $password = trim($_POST["password"] ?? '');

    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $db_username, $db_password);
        $stmt->fetch();

        if (password_verify($password, $db_password)) {
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $db_username;

            // Check user role
            if ($db_username === "admin") {
                $_SESSION["is_admin"] = true;
                header("Location: admin_home.php");
                exit();
            } elseif (strpos($db_username, "canteen") !== false) {
                header("Location: canteen_home.php");
                exit();
            } else {
                header("Location: dashboard.php");
                exit();
            }
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "User does not exist!";
    }
    $stmt->close();
}
$conn->close();
?>

<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="styles/Lstyle.css">
</head>

<body>

    <div class="form-container">

        <h2>Login</h2>

        <div class="error">
            <?php echo $error ?? ''; ?>
        </div>

        <form action="login.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
        </form>

        <center>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </center>
    </div>
</body>

</html>