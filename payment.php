<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_type = $_POST["payment_type"];

    // Insert payment record into the database
    $sql = "INSERT INTO payments (user_id, payment_type) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $payment_type);

    if ($stmt->execute()) {
        echo "<script>alert('Payment successful!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Payment failed. Try again!');</script>";
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
    <title>Payment</title>
    <link rel="stylesheet" href="styles/Ostyle.css">
    <style>
        .container {
            width: 50%;
            margin: auto;
            padding: 20px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #f9f9f9;
        }
        select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        #qr-container {
            display: none;
            margin-top: 15px;
        }
        img {
            width: 200px;
            height: 200px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Choose Payment Method</h2>
    
    <form method="POST">
        <label for="payment_type">Select Payment Type:</label>
        <select id="payment_type" name="payment_type" onchange="toggleQR()">
            <option value="cash">Cash</option>
            <option value="upi">UPI</option>
        </select>

        <div id="qr-container">
            <h3>Scan the QR Code to Pay</h3>
            <img src="assets/QR.png" alt="UPI QR Code">
        </div>

        <button type="submit">Proceed with Payment</button>
    </form>
</div>

<script>
    function toggleQR() {
        var paymentType = document.getElementById("payment_type").value;
        var qrContainer = document.getElementById("qr-container");

        if (paymentType === "upi") {
            qrContainer.style.display = "block";
        } else {
            qrContainer.style.display = "none";
        }
    }
</script>

</body>
</html>
