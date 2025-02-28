<?php
include 'connection.php';

// Read the webhook payload from PayMongo
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

// Check if the webhook is for a successful payment
if (isset($data['data']['attributes']['status']) && $data['data']['attributes']['status'] == 'paid') {
    $order_id = $data['data']['attributes']['description']; // Assuming order_id was passed as description

    try {
        // Start transaction
        $conn->beginTransaction();

        // Update the order status to 'Completed'
        $update_order = $conn->prepare("UPDATE `orders` SET status = 'Completed' WHERE id = ?");
        $update_order->execute([$order_id]);

        if ($update_order->rowCount() > 0) {
            // Fetch the cart data for the user
            $cart = $conn->prepare("SELECT c.*, p.id as pid, p.price FROM `carts` c INNER JOIN `products` p ON p.id = c.p_id WHERE c.user_id = (SELECT user_id FROM `orders` WHERE id = ?)");
            $cart->execute([$order_id]);

            while ($row = $cart->fetch(PDO::FETCH_ASSOC)) {
                // Deduct product stock
                $update_stock = $conn->prepare("UPDATE `products` SET stock = stock - ? WHERE id = ?");
                $update_stock->execute([$row['quantity'], $row['pid']]);

                // Insert finalized order details into `order_list`
                $total = $row['price'] * $row['quantity'];
                $order_list_sql = "INSERT INTO `order_list` (o_id, p_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)";
                $conn->prepare($order_list_sql)->execute([$order_id, $row['pid'], $row['quantity'], $row['price'], $total]);
            }

            // Clear the user's cart after successful payment
            $empty_cart = $conn->prepare("DELETE FROM `carts` WHERE user_id = (SELECT user_id FROM `orders` WHERE id = ?)");
            $empty_cart->execute([$order_id]);

            // Commit the transaction
            $conn->commit();
        } else {
            // Rollback the transaction if order update fails
            $conn->rollBack();
        }
    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }

} else {
    // Handle other payment statuses (e.g., failed, expired)
    http_response_code(400);
    echo "Payment not successful or invalid payload.";
}
?>
