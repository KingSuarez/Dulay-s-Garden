<?php
include 'connection.php';

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // SQL query to fetch the order details
    $qry = $conn->prepare("SELECT o.*, p.pname, p.price, ol.quantity, ol.total
                           FROM `order_list` ol
                           INNER JOIN `products` p ON ol.p_id = p.id
                           INNER JOIN `orders` o ON ol.o_id = o.id
                           WHERE o.id = ?");
    $qry->execute([$order_id]);

    $totalAmount = 0; 
    $downpayment = 0; 
    $balance = 0;
    $paymentMethod = '';

    if ($qry->rowCount() > 0) {
        echo '<table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
        echo '<tr>';
        echo '<th>Product Name</th>';
        echo '<th>Quantity</th>';
        echo '<th>Price</th>';
        echo '<th>Total</th>';
        echo '</tr>';

        // Fetch and display each row of data
        while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';
            echo '<td style="text-transform:capitalize;">' . htmlspecialchars($row['pname']) . '</td>';
            echo '<td>' . htmlspecialchars($row['quantity']) . '</td>';
            echo '<td>₱ ' . number_format($row['price'], 2) . '</td>';
            echo '<td>₱ ' . number_format($row['total'], 2) . '</td>';
            echo '</tr>';

            $totalAmount += $row['total']; 
            $paymentMethod = $row['payment']; // Capture the payment method
            $balance = $row['balance']; // Capture the balance
        }

        echo '</table>';

        echo '<h5 style="text-align: right; margin-right:30px; font-weight:bold;">Total Amount: ₱ ' . number_format($totalAmount, 2) . '</h5>';

        // Check if the payment method is "Downpayment"
        if ($paymentMethod === 'Downpayment') {
            $downpayment = $totalAmount * 0.25;
            $balance = $totalAmount - $downpayment;
            echo '<h5 style="text-align: right; margin-right:30px; font-weight:bold;">Downpayment: ₱ ' . number_format($downpayment, 2) . '</h5>';
            echo '<h5 style="text-align: right; margin-right:30px; font-weight:bold;">Balance: ₱ ' . number_format($balance, 2) . '</h5>';
        } elseif ($paymentMethod === 'Fullpayment') {
            echo '<h5 style="text-align: right; margin-right:30px; font-weight:bold; color:green;">Paid</h5>';
        } else {
            echo '<h5 style="text-align: right; margin-right:30px; font-weight:bold;">Balance: ₱ ' . number_format($balance, 2) . '</h5>';
        }

    } else {
        echo '<p>No order details found.</p>';
    }
} else {
    echo '<p>Invalid order ID.</p>';
}
?>
