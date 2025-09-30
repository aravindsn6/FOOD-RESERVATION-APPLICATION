<?php
session_start();
include "db.php";

// Check if the user is an admin
if (!isset($_SESSION["username"]) || $_SESSION["username"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Fetch food items and their prices
$sql = "SELECT id, food_name, canteen_name, price FROM food_items";
$result = $conn->query($sql);

// Update food price
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_price"])) {
    $food_id = $_POST["food_id"];
    $new_price = $_POST["new_price"];

    if (is_numeric($new_price) && $new_price > 0) {
        $update_sql = "UPDATE food_items SET price = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("di", $new_price, $food_id);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Price updated successfully! New Price: $new_price');
                    window.location.href = 'admin_manage_price.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('Failed to update price!');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Invalid price entered!');</script>";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Food Prices</title>
    <link rel="stylesheet" href="styles/Vstyle.css">
    <style>
        .container {
            width: 80%;
            margin: auto;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .update-btn {
            background-color: orange;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .update-btn:hover {
            background-color: #ec971f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Food Prices</h2>
        <table>
            <thead>
                <tr>
                    <th>Food Item</th>
                    <th>Canteen</th>
                    <th>Current Price</th>
                    <th>New Price</th>
                   
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['canteen_name']); ?></td>
                        <td>₹<?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="food_id" value="<?php echo $row['id']; ?>">
                                <input type="number" step="0.01" name="new_price" required>
                                <button type="submit" name="update_price" class="update-btn">Update</button>
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
