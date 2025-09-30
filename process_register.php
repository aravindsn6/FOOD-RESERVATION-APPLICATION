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

    // Check if passwords match
    if ($password !== $confirm_password) {
        $errorMessage = "Passwords do not match!";
    } else {
        // Check if username or email exists
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
    <title>Processing Registration</title>
    <link rel="stylesheet" href="styles/Lstyle.css">
</head>
<body>
    <div class="container">
        <h2>Registration Status</h2>

        <?php if (!empty($errorMessage)): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <?php if (!empty($successMessage)): ?>
            <div class="form-container">
                <center><p><?php echo $successMessage; ?></p></center>
                <center><a href="login.php" class="login-btn">Login Here</a></center>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
