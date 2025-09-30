<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$canteen_staff = $_SESSION["username"];
$sql = "SELECT * FROM orders WHERE canteen_staff = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $canteen_staff);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Orders</title>
    <link rel="stylesheet" href="styles/Vstyle.css">
    <style>
        .status {
            font-weight: bold;
        }
        .pending, .cancelled {  color: red; }
        .preparing {  color: orange; }
        .completed {  color: green; }
	.cancelled{ color:red};
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Orders</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Username</th>
                    <th>Food Name</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                    $statusClass = strtolower($row['status']);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td>₹<?php echo htmlspecialchars($row['total_price']); ?></td>
                        <td>
                            <form method="POST" action="process_update.php">
                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                <select name="status">
                                    <option class="pending" value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option class="preparing" value="Preparing" <?php echo ($row['status'] == 'Preparing') ? 'selected' : ''; ?>>Preparing</option>
                                    <option class="completed" value="Completed" <?php echo ($row['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option class="cancelled" value="Cancelled" <?php echo ($row['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit">Save</button>
                            </form>
                        </td>
                        <td class="status <?php echo $statusClass; ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </td>
                    </tr>  
                <?php endwhile; ?>
            </tbody>
        </table>
	<a class="back-btn" href="canteen_home.php">Back to Home</a>
    </div>
</body>
</html>
