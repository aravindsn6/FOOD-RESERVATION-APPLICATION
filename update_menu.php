<?php
session_start();
include "db.php";

// Ensure only canteen staff can access
if (!isset($_SESSION["username"]) || strpos($_SESSION["username"], "canteen") !== 0) {
    die("Access Denied");
}

$canteenName = $_SESSION["username"];

// **Handle Adding New Food Item**
if (isset($_POST["add_food"])) {
    $food_name = $_POST["food_name"];
    $available_quantity = $_POST["available_quantity"];
    $price = $_POST["price"];

    $insertQuery = "INSERT INTO food_items (food_name, available_quantity, price, canteen_name) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sdis", $food_name, $available_quantity, $price, $canteenName);
    
    if ($stmt->execute()) {
        echo "<script>alert('Food item added successfully!'); window.location.href = 'updateS_menu.php';</script>";
    } else {
        echo "<script>alert('Failed to add item.');</script>";
    }
    $stmt->close();
}

// **Handle Updating Food Quantity**
if (isset($_POST["update_food"])) {
    $food_id = $_POST["food_id"];
    $available_quantity = $_POST["available_quantity"];

    $updateQuery = "UPDATE food_items SET available_quantity = ? WHERE id = ? AND canteen_name = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("iis", $available_quantity, $food_id, $canteenName);
    
    if ($stmt->execute()) {
        echo "<script>alert('Food quantity updated successfully!'); window.location.href = 'update_menu.php';</script>";
    } else {
        echo "<script>alert('Failed to update quantity.');</script>";
    }
    $stmt->close();
}

// Fetch food items added by this canteen only
$query = "SELECT id, food_name, available_quantity, price FROM food_items WHERE canteen_name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $canteenName);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Menu</title>
    <link rel="stylesheet" href="styles/Ustyle.css">
</head>
<body>

<div class="container">
    <h2>Order Menu for <?= htmlspecialchars($canteenName) ?></h2>

    <!-- **Form to Add Food Item** -->
    <form method="POST">
        <input type="text" name="food_name" placeholder="Food Name" required>
        <input type="number" name="available_quantity" placeholder="Available Quantity" required>
        <input type="number" name="price" placeholder="Price" step="0.01" required><br><br>
        <button type="submit" name="add_food">Add Item</button>
    </form>

    <br>

    <table border="1">
        <tr>
            <th>Food Item</th>
            <th>Available Quantity</th>
            <th>Price</th>
            <th>Update Quantity</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row["food_name"]); ?></td>
            <td><?= (int)$row["available_quantity"]; ?></td>
            <td>₹<?= number_format((float)$row["price"], 2); ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="food_id" value="<?= $row['id']; ?>">
                    <input type="number" name="available_quantity" value="<?= $row['available_quantity']; ?>" required>
                    <button type="submit" name="update_food">Update</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="canteen_home.php" class="back-btn">Back to Home</a>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
