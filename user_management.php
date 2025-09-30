<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$canteen_staff = $_SESSION["username"];

// Fetch only users who have ordered from this canteen staff
$sql = "SELECT DISTINCT u.* FROM users u 
        JOIN orders o ON u.id = o.user_id OR u.username = o.username
        WHERE o.canteen_staff = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $canteen_staff);
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["request_block"])) {
    $user_id = $_POST["user_id"];
    
    // Update the users table to set blocked status
    $update_sql = "UPDATE users SET blocked = 1 WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $user_id);
    if ($update_stmt->execute()) {
        header("Location: user_management.php");
        exit(); // Ensure script stops execution after redirection
    } else {
        echo "<script>alert('Failed to block user!');</script>";
    }
    $update_stmt->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="styles/Vstyle.css">
    <style>
        .blocked {
            background-color: red;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Management</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="<?php echo $row['blocked'] ? 'blocked' : ''; ?>">
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo $row['blocked'] ? 'Blocked' : 'Active'; ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="request_block" <?php echo $row['blocked'] ? 'disabled' : ''; ?>>
                                    <?php echo $row['blocked'] ? 'Blocked' : 'Block User'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
	<a class="back-btn" href="canteen_home.php">Back to Home</a>
    </div>
</body>
</html>
