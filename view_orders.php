<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="styles/Vstyle.css">
</head>
<body>

<div class="container">
    <h2>My Orders</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Food Item</th>
                    <th>Quantity</th>
                    <th>Total Price (₹)</th>
                    <th>Status</th>
                    <th>Order Time</th>
		    <th>Canteen</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["food_name"]); ?></td>
                        <td><?php echo (int) $row["quantity"]; ?></td>
                        <td><?php echo number_format((float) $row["total_price"], 2); ?></td>
                        <td class="<?php echo strtolower($row["status"]); ?>"><?php echo ucfirst($row["status"]); ?></td>
                        <td><?php echo $row["order_time"]; ?></td>
			<td><?php echo $row["canteen_staff"];?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <center><p>You have not placed any orders yet.</p></center>
    <?php endif; ?>

    <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
