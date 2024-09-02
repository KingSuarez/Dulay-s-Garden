<?php
include 'connection.php';

// Include database connection file

// // Check if an order ID is provided via GET request
// if (isset($_GET['id'])) {
//     $order_id = htmlspecialchars($_GET['id']);

//     try {
//         // Prepare the SQL statement to update the payment status and balance
//         $stmt = $conn->prepare("UPDATE orders SET payment = 'Fullpayment', balance = 0 WHERE id = :id AND payment = 'Downpayment'");
//         $stmt->execute(['id' => $order_id]);

//         // Check if the update was successful
//         if ($stmt->rowCount() > 0) {
//             // Redirect to the order completion page with a success message
            
//             header('Location: AorderCom.php');
//         } else {
//             // Redirect to the order completion page with a failure message
//             header('Location: AorderCom.php');
//         }
//     } catch (PDOException $e) {
//         // Handle any exceptions/errors and redirect with an error status
//         header('Location: AorderCom.php');
//     }
// } else {
//     // Redirect back if no order ID is provided
//     header('Location: AorderCom.php');
// }
// exit();
// Check if an order ID is provided via GET request

// Check if an order ID is provided via GET request
// Check if an order ID is provided via GET request
if (isset($_GET['id'])) {
    $order_id = htmlspecialchars($_GET['id']);

    try {
        // Prepare the SQL statement to update the payment status and balance
        $stmt = $conn->prepare("UPDATE orders SET payment = 'Fullpayment', balance = 0 WHERE id = :id AND payment = 'Downpayment'");
        $stmt->execute(['id' => $order_id]);

        // Check if the update was successful
        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Order marked as fully paid successfully!'); window.location.href='AorderCom.php';</script>";
        } else {
            echo "<script>alert('Failed to mark the order as fully paid.'); window.location.href='AorderCom.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('An error occurred: " . $e->getMessage() . "'); window.location.href='AorderCom.php';</script>";
    }
} else {
    // Redirect back if no order ID is provided
    echo "<script>alert('No order ID provided.'); window.location.href='AorderCom.php';</script>";
}

?>
