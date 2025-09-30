<?php
include "db.php";

$errorMessage = "";
$successMessage = "";

// Handle registration logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Password validation
   if (strlen($password) < 8 || !preg_match('/[\W]/', $password)) {
        $errorMessage = "Password must be at least 8 characters long and contain at least one special character!";
    } else
    if ($password !== $confirm_password) {
        $errorMessage = "Passwords do not match!";
    } else {
        // Check if username or email already exists
        $checkUserQuery = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($checkUserQuery);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errorMessage = "Username or Email is already taken. Please choose another one.";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $successMessage = "Registration successful!";
            } else {
                $errorMessage = "Error: " . $stmt->error;
            }
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles/Lstyle.css">
</head>
<body>
    <div class="form-container">
        <h2>Register</h2>

        <?php if (!empty($errorMessage)): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <?php if (!empty($successMessage)): ?>
            <div class="success-message">
                <p><?php echo $successMessage; ?></p>
                <a href="login.php" class="login-btn">Login Here</a>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <input type="submit" value="Register">
        </form>

        <p align="center">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
