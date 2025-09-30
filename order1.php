<?php
session_start();
include "db.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Fetch available canteens
$canteens = [];
$sql = "SELECT username FROM users WHERE username LIKE 'canteen%'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $canteens[] = $row["username"];
    }
}

// Handle order submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION["user_id"])) {
        $error = "User not logged in.";
    } elseif (empty($_POST["canteen_staff"]) || empty($_POST["items"])) {
        $error = "Missing required order details.";
    } else {
        $user_id = $_SESSION["user_id"];
        $username = $_SESSION["username"];
        $canteen_staff = trim($_POST["canteen_staff"]);

        if (!in_array($canteen_staff, $canteens)) {
            $error = "Invalid canteen selection.";
        } else {
            $orders = json_decode($_POST["items"], true);

            if (!is_array($orders) || empty($orders) || json_last_error() !== JSON_ERROR_NONE) {
                $error = "Invalid order data.";
            } else {
                $conn->begin_transaction();
                try {
                    $stmt = $conn->prepare("INSERT INTO orders (user_id, username, food_name, quantity, total_price, canteen_staff) VALUES (?, ?, ?, ?, ?, ?)");
                    $updateStmt = $conn->prepare("UPDATE food_items SET available_quantity = available_quantity - ? WHERE food_name = ? AND available_quantity >= ?");

                    foreach ($orders as $order) {
                        if (!isset($order["food_name"], $order["quantity"], $order["total_price"])) {
                            continue;
                        }

                        $food_name = trim($order["food_name"]);
                        $quantity = (int) $order["quantity"];
                        $total_price = (float) $order["total_price"];

                        // Fetch available quantity
                        $availableStmt = $conn->prepare("SELECT available_quantity FROM food_items WHERE food_name = ?");
                        $availableStmt->bind_param("s", $food_name);
                        $availableStmt->execute();
                        $availableStmt->bind_result($available_quantity);
                        $availableStmt->fetch();
                        $availableStmt->close();

                        if ($quantity > $available_quantity) {
                            throw new Exception("Not enough stock for $food_name. Available: $available_quantity, Ordered: $quantity.");
                        }

                        // Insert order
                        $stmt->bind_param("issids", $user_id, $username, $food_name, $quantity, $total_price, $canteen_staff);
                        $stmt->execute();

                        // Update food quantity
                        $updateStmt->bind_param("isi", $quantity, $food_name, $quantity);
                        $updateStmt->execute();

                        if ($updateStmt->affected_rows === 0) {
                            throw new Exception("Not enough stock for $food_name.");
                        }
                    }

                    $stmt->close();
                    $updateStmt->close();
                    $conn->commit();

                    header("Location: payment.php");
                    exit();
                } catch (Exception $e) {
                    $conn->rollback();
                    $error = "Error placing order: " . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <link rel="stylesheet" href="styles/Ostyle.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body >

<!-- Top Right Profile & Logout -->
<div class="top-right">
    <a href="profile.php" class="profile-link"><img src="assets/profile.png" alt="Profile"></a>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<br><br><br>
<div class="container">
    <h2>Order Your Food</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>

    <form id="order-form" method="POST">
        <div class="canteen-dropdown">
            <label for="canteen">Select Canteen:</label>
            <select name="canteen_staff" id="canteen" required>
                <option value="" disabled selected>Select a canteen</option>
                <?php foreach ($canteens as $canteen): ?>
                    <option value="<?php echo htmlspecialchars($canteen); ?>"><?php echo htmlspecialchars($canteen); ?></option>
                <?php endforeach; ?>
            </select>
        </div><br>

        <div id="food-items-container" class="food-grid">
            <p>Select a canteen to view available food items.</p>
        </div>

        <h3>Total: ₹<span id="total-price">0</span></h3>
        <input type="hidden" name="items" id="items">
        <button type="submit" id="place-order">Place Order</button>
    </form>
</div>

<div class="bottom-nav">
    <ul>
        <li><a href="view_orders.php">View Orders</a></li>
    </ul>
</div>

<script>
    $(document).ready(function () {
        $("#canteen").change(function () {
            let selectedCanteen = $(this).val();

            if (selectedCanteen) {
                $.ajax({
                    url: "fetch_food_items.php",
                    type: "POST",
                    data: {canteen: selectedCanteen},
                    success: function (response) {
                        $("#food-items-container").html(response);
                        attachQuantityListeners();
                    }
                });
            } else {
                $("#food-items-container").html("<p>Select a canteen to view available food items.</p>");
            }
        });
        
        function attachQuantityListeners() {
            $(".quantity").on("input", function () {
                let total = 0;
                $(".quantity").each(function () {
                    let quantity = parseInt($(this).val()) || 0;
                    let price = parseInt($(this).closest(".food-item").attr("data-price"));
                    if(quantity)
                    total += quantity * price;
                });
                $("#total-price").text(total);
            });

        $("#order-form").on("submit", function (event) {
                let selectedItems = [];
                $(".quantity").each(function () {
                    let quantity = parseInt($(this).val()) || 0;
                    if (quantity > 0) {
                        let foodItem = $(this).closest(".food-item");
                        selectedItems.push({
                            food_name: foodItem.attr("data-name"),
                            quantity: quantity,
                            total_price: parseInt(foodItem.attr("data-price")) * quantity
                        });
                    }
                });

                if (selectedItems.length === 0) {
                    alert("Please select at least one item.");
                    event.preventDefault();
                    return;
                }
                
                if ($("#canteen").val() === "") {
                    alert("Please select a canteen.");
                    event.preventDefault();
                    return;
                }

                $("#items").val(JSON.stringify(selectedItems));
            });
        }
    });
</script>

</body>
</html>
