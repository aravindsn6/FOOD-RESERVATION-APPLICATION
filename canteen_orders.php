<?php
session_start();
include "db.php";

// Ensure only canteen staff can access
if (!isset($_SESSION["username"]) || strpos($_SESSION["username"], "canteen") !== 0) {
    header("Location: login.php");
    exit();
}

$canteenName = $_SESSION["username"]; // Assuming the username starts with 'canteen'

// Fetch orders related to the logged-in canteen
$query = "SELECT * FROM orders WHERE canteen_staff = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $canteenName);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Canteen Orders</title>
    <link rel="stylesheet" href="styles/Vstyle.css">
</head>
<body>
    <h2>Orders for <?= htmlspecialchars($canteenName) ?></h2>

    <table border="1">
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Food Item</th>
            <th>Quantity</th>
            <th>Order Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row["id"]; ?></td>
            <td><?= $row["user_id"]; ?></td>
            <td><?= $row["food_name"]; ?></td>
            <td><?= $row["quantity"]; ?></td>
            <td><?= $row["status"]; ?></td>
            <td>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
