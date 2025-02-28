<?php
// delete_order.php

include 'connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['id']);

    if ($order_id > 0) {
        try {
            $conn->beginTransaction();

            // Delete order from order_list table
            $delete_order_list = $conn->prepare("DELETE FROM `order_list` WHERE o_id = ?");
            $delete_order_list->execute([$order_id]);

            // Delete order from orders table
            $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
            $delete_order->execute([$order_id]);

            // Optionally, delete related records from the sales table
            $delete_sales = $conn->prepare("DELETE FROM `sales` WHERE order_id = ?");
            $delete_sales->execute([$order_id]);

            $conn->commit();
            echo 'success';
        } catch (Exception $e) {
            $conn->rollBack();
            echo 'error: ' . $e->getMessage();
        }
    } else {
        echo 'error: Invalid order ID';
    }
} else {
    echo 'error: Invalid request method';
}
?>
