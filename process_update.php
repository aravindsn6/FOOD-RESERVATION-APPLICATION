<?php
session_start();
include "db.php"; // Ensure this correctly connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["order_id"]) && isset($_POST["status"])) {
        $order_id = $_POST["order_id"];
        $status = $_POST["status"];

        // Update the order status in the database
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("si", $status, $order_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                // Redirect back to update_order.php with a success message
                header("Location: update_order.php?success=1");
                exit();
            } else {
                // No rows updated (possibly same status as before)
                header("Location: update_order.php?success=0");
                exit();
            }

            $stmt->close();
        } else {
            echo "Error in preparing SQL statement.";
        }
    } else {
        echo "Invalid request. Order ID or status missing.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
