<?php
session_start();
require '../INCLUDES/db_connects.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Make Claim Payment</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 30px; }
        .container { background: #fff; padding: 20px; max-width: 600px; margin: auto; border-radius: 10px; }
        h2 { color: #007BFF; }
        label { display: block; margin-top: 15px; }
        input, select { width: 100%; padding: 10px; margin-top: 5px; }
        button { margin-top: 20px; padding: 12px; background-color: #007BFF; color: white; border: none; width: 100%; }
    </style>
</head>
<body>
<div class="container">
    <h2>Make Claim Payment</h2>
    <form method="POST" action="process_payment.php">
        <label for="accident_report_id">Accident Report ID</label>
        <input type="number" name="accident_report_id" required>

        <label for="amount">Amount (KES)</label>
        <input type="number" step="0.01" name="amount" required>

        <label for="payment_method">Payment Method</label>
        <select name="payment_method" required>
            <option value="Mpesa">Mpesa</option>
            <option value="BankTransfer">Bank Transfer</option>
            <option value="Cheque">Cheque</option>
            <option value="DirectToRepair">Direct to Repair</option>
        </select>

        <label for="payment_reference">Payment Reference</label>
        <input type="text" name="payment_reference" required>

        <label for="notes">Notes (Optional)</label>
        <textarea name="notes" rows="3"></textarea>

        <button type="submit">Submit Payment</button>
    </form>
</div>
</body>
</html>
