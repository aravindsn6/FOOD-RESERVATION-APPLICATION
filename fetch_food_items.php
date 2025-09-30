<?php
include "db.php";

if (isset($_POST["canteen"])) {
    $canteen = $_POST["canteen"];
    $stmt = $conn->prepare("SELECT food_name, price, available_quantity FROM food_items WHERE available_quantity > 0 AND canteen_name = ?");
    $stmt->bind_param("s", $canteen);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $additionalMessage = $row["available_quantity"] < 5 ? ' <span style="color: red;font-weight: bold;"> Only ' . htmlspecialchars($row["available_quantity"]) . ' left!</span>' : "";
            echo '<div class="food-item" data-name="' . htmlspecialchars($row["food_name"]) . '" data-price="' . (int)$row["price"] . '">
                    <img src="assets/' . htmlspecialchars($row["food_name"]) . '">
                    <p>' . htmlspecialchars($row["food_name"]) . ' - ₹' . (int)$row["price"] ."<br>". $additionalMessage . '</p>
                    <input type="number" min="0" value="0" class="quantity">
                  </div>';
        }
    } else {
        echo "<p>No food items available for this canteen.</p>";
    }

    $stmt->close();
}
?>
