<?php
session_start();
include "db.php";

if (!isset($_SESSION["is_admin"]) || !$_SESSION["is_admin"]) {
    header("Location: login.php");
    exit();
}

// Fetch all users except admin
$sql = "SELECT id, username, email, blocked FROM users WHERE username != 'admin'";
$result = $conn->query($sql);

// Handle blocking/unblocking users
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    $user_id = $_POST["user_id"];
    $action = $_POST["action"];

    if ($action === "block") {
        $update_sql = "UPDATE users SET blocked = 1 WHERE id = ?";
    } elseif ($action === "unblock") {
        $update_sql = "UPDATE users SET blocked = 0 WHERE id = ?";
    } elseif ($action === "delete") {
        $update_sql = "DELETE FROM users WHERE id = ?";
    }

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        header("Location: admin_manage_users.php"); 
        exit();
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
        <h2>Manage Users</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
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
                                <?php if ($row['blocked']): ?>
                                    <button type="submit" name="action" value="unblock">Unblock</button>
                                <?php else: ?>
                                    <button type="submit" name="action" value="block">Block</button>
                                <?php endif; ?>
                                <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
	<a href="admin_home.php" class="back-btn">Back to Home</a>
    </div>
</body>
</html>
