<?php
include 'connection.php';

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id > 0) {
    $order = $conn->prepare("SELECT o.*, CONCAT(c.Fname, ' ', c.Lname) AS user FROM `orders` o INNER JOIN `users` c ON c.id = o.user_id WHERE o.id = ?");
    $order->execute([$order_id]);

    if ($order->rowCount() > 0) {
        $order_details = $order->fetch(PDO::FETCH_ASSOC);
        
        // Output order details for modal
        echo "<div class='container-fluid'>
                <p><b>Customer Name: " . htmlspecialchars($order_details['user']) . "</b></p>
                <table class='table-striped table table-bordered'>
                    <thead>
                        <tr>
                            <th>QTY</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>";
        
        $olist = $conn->prepare("SELECT ol.*, p.pname FROM `order_list` ol INNER JOIN `products` p ON ol.p_id = p.id WHERE ol.o_id = ?");
        $olist->execute([$order_id]);

        while ($row = $olist->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['quantity']) . "</td>
                    <td>" . htmlspecialchars($row['pname']) . "</td>
                    <td>" . number_format($row['price'], 2) . "</td>
                    <td>" . number_format($row['total'], 2) . "</td>
                </tr>";
        }

        echo "    </tbody>
                </table>
                <p>Payment Method: " . ($order_details['payment'] == 'full' ? "<span class='badge badge-success'>Full Payment</span>" : "<span class='badge badge-light'>Downpayment</span>") . "</p>
                <p>Payment Status: " . ($order_details['payment'] == 'full' ? "<span class='badge badge-success'>Paid</span>" : "<span class='badge badge-light text-dark'>Unpaid</span>") . "</p>
                <p>Order Status: ";

        switch ($order_details['status']) {
            case 'Pending':
                echo "<span class='badge badge-light'>Pending</span>";
                break;
            case 'Processing':
                echo "<span class='badge badge-primary'>Processing</span>";
                break;
            case 'Completed':
                echo "<span class='badge badge-success'>Completed</span>";
                break;
            case 'Cancelled':
                echo "<span class='badge badge-danger'>Cancelled</span>";
                break;
            default:
                echo "<span class='badge badge-secondary'>Unclaim</span>";
                break;
        }

        echo "</p>
            </div>";
    } else {
        echo "Order ID provided is unknown.";
    }
} else {
    echo "Invalid Order ID.";
}
?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JavaScript -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
