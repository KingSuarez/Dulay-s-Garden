<?php
// Connection to the database
include 'connection.php';
session_start();

// Check if the admin is logged in
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:login.php');
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_GET['id']);
    $new_status = $_POST['order_status'];
    echo "<script>console.log('Form submitted with new status: " . $new_status . "');</script>";

    // Check if the status is being changed to Cancelled or Unclaim
    if ($new_status === 'Cancelled' || $new_status === 'Unclaim') {
        // Fetch the order items
        $order_items_stmt = $conn->prepare("SELECT p_id, quantity FROM `order_list` WHERE o_id = ?");
        $order_items_stmt->execute([$order_id]);

        // Update the stock for each product in the order
        while ($item = $order_items_stmt->fetch(PDO::FETCH_ASSOC)) {
            $update_stock_stmt = $conn->prepare("UPDATE `products` SET `stock` = `stock` + ? WHERE `id` = ?");
            $update_stock_stmt->execute([$item['quantity'], $item['p_id']]);
        }
    }

    // Update the order status
    $update_status_stmt = $conn->prepare("UPDATE `orders` SET `status` = ? WHERE `id` = ?");
    if ($update_status_stmt->execute([$new_status, $order_id])) {
        echo "<script>alert('Order status updated successfully.'); window.location.href = 'view_order.php?id={$order_id}';</script>";
    } else {
        echo "<script>alert('Failed to update order status.'); window.history.back();</script>";
    }
}
// Fetch order details
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($order_id > 0) {
    $order = $conn->prepare("SELECT o.*, CONCAT(c.Fname, ' ', c.Lname) AS user FROM `orders` o INNER JOIN `users` c ON c.id = o.user_id WHERE o.id = ?");
    $order->execute([$order_id]);

    if ($order->rowCount() > 0) {
        $order_details = $order->fetch(PDO::FETCH_ASSOC);
        $payment_method = $order_details['payment'];
        $balance = $order_details['balance']; // Fetch the balance
        $paid = $balance == 0; // Determine if the order is paid
        $status = $order_details['status'];
        $Downpayment = $order_details['amount'] * 0.25;

    } else {
        echo "<script>alert('Order not found.'); window.history.back();</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid order ID.'); window.history.back();</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Order - Dulay's Garden Panel</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style1.css">
    <link rel="stylesheet" href="style/table-user.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body>


<!-- SideBar -->
<div class="sidebar">
    <div class="logo-details">
        <img src="44-removebg-preview.png" alt="44-removebg-preview.png">
        <span class="logo_name" style="color: darkgreen;">Dulay's <span style="margin-left: 10px; color:darkgreen">Garden</span></span>
    </div>
    <ul class="nav-links">
        <li>
            <a href="dashboard.php">
                <i class='bx bx-box'></i>
                <span class="links_name">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="product.php">
                <i class='bx bx-store'></i>
                <span class="links_name">Product</span>
            </a>
        </li>
        <li>
            <a href="info_up.php">
                <i class='bx bx-id-card'></i>
                <span class="links_name">Update-profile</span>
            </a>
        </li>
        <li>
            <a href="AorderPe.php">
                <i class='bx bxs-box'></i>
                <span class="links_name">Reserve Order</span>
            </a>
        </li>
        <li>
            <a href="user_a.php">
                <i class='bx bxs-user-account'></i>
                <span class="links_name">Users</span>
            </a>
        </li>
        <li class="log_out">
            <a href="logout.php">
                <i class='bx bx-log-out'></i>
                <span class="links_name">Logout</span>
            </a>
        </li>
    </ul>
</div>


<!-- Top Bar -->
<section class="home-section" style="min-height: 160vh;">
    <nav>
        <div class="sidebar-button">
            <i class='bx bx-menu sidebarBtn'></i>
            <span class="dashboard">Manage Orders</span>
        </div>

        <div class="profile-details">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <img src="img/<?= $fetch_profile['image']; ?>" alt="Profile Image">
            <p style="margin-left: 50px; font-size: 20px;"><?= $fetch_profile['Fname']; ?></p>
        </div>
    </nav>
    <br><br><br><br> <br><br>

    <section>
    <div class="card-body">
    <div class="container-fluid">
        <h2> <b>Order Details</b></h2>
        <br>
        <h2 style="text-transform:capitalize;"><b>Customer Name: <?= htmlspecialchars($order_details['user']) ?></b></h2>
        <hr>
        <div class="row">
            <div class="col-6">
                <h4>Payment: <?= htmlspecialchars($payment_method) ?></h4>
                <h4>Payment Status: <?= $paid ? '<span class="badge badge-success">Paid</span>' : '<span class="badge badge-light text-dark">Unpaid</span>' ?></h4>
                <?php if ($balance > 0): ?>
                    


                    <h4>Total paid: ₱ <?= number_format($Downpayment, 2) ?></h4>
                   
                    <h4>Balance: ₱ <?= number_format($balance, 2) ?></h4>
                <?php endif; ?>
            </div>
            <div class="col-6 row row-cols-2">
                <div class="col-3"><h4>Order Status:</h4></div>
                <div class="col-9">
                    <?php 
                    switch($status){
                        case 'Pending':
                            echo "<span class='badge badge-light'><h5>Pending</h5></span>";
                            break;
                        case 'Processing':
                            echo "<span class='badge badge-primary-light'><h5>Processing</h5></span>";
                            break;
                            case 'Ready-To-Pick-Up':
                                echo "<span class='badge badge-primary-light'><h5>Ready-To-Pick-Up</h5></span>";
                                break;
                        case 'Completed':
                            echo "<span class='badge badge-success-light'><h5>Completed</h5></span>";
                            break;
                        case 'Cancelled':
                            echo "<span class='badge badge-danger-light'><h5>Cancelled</h5></span>";
                            break;
                        case 'Unclaim':
                            echo "<span class='badge badge-secondary-light'><h5>Unclaim</h5></span>";
                            break;
                        
                    }
                    ?>
                </div>
                <div class="col-3"></div>
                <div class="col">
                    <form method="post" id="status_form" action="view_order.php?id=<?= $order_id ?>">
                        <?php 
                        // Determine if the form should be disabled
                        $disabled = ($status == 'Completed' || $status == 'Unclaim'|| $status == 'Cancelled') ? 'disabled' : '';
                        ?>
                        <select name="order_status" class="form-control" <?= $disabled ?>>
                            <option value="Pending" <?= $status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Processing" <?= $status == 'Processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="Ready-To-Pick-Up" <?= $status == 'Ready-To-Pick-Up' ? 'selected' : '' ?>>Ready-To-Pick-Up</option>
                            <option value="Completed" <?= $status == 'Completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="Cancelled" <?= $status == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            <option value="Unclaim" <?= $status == 'Unclaim' ? 'selected' : '' ?>>Unclaim</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-sm btn-flat btn-primary" <?= $disabled ?>><h5>Update Status</h5></button>
                    </form>
                </div>
            </div>
        </div>
        <br><br>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col" style="text-align: center;">Product Name</th>
                    <th scope="col" style="text-align: center;">Quantity</th>
                    <th scope="col" style="text-align: center;">Price</th>
                    <th scope="col"style="text-align: center;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $order_items = $conn->prepare("SELECT p.pname, ol.quantity, ol.price, ol.total 
                                               FROM `order_list` ol 
                                               INNER JOIN `products` p ON p.id = ol.p_id 
                                               WHERE ol.o_id = ?");
                $order_items->execute([$order_id]);

                if ($order_items->rowCount() > 0) {
                    while ($item = $order_items->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                                <td>{$item['pname']}</td>
                                <td>{$item['quantity']}</td>
                                <td>{$item['price']}</td>
                                <td>{$item['total']}</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No items found for this order.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <h2 class="text-right" style="margin-right: 120px;">Total Amount: ₱ <?= number_format($order_details['amount'], 2) ?></h2>
    </div>
    <a href="Aorder.php"  class="btn btn-sm btn-flat btn-success" ><h4>Go Back</h4></a>
           
</div>
</section>
</section>

<script>
    let sidebar = document.querySelector(".sidebar");
    let sidebarBtn = document.querySelector(".sidebarBtn");
    sidebarBtn.onclick = function() {
        sidebar.classList.toggle("active");
        if (sidebar.classList.contains("active")) {
            sidebarBtn.classList.replace("bx-menu", "bx-menu-alt-right");
        } else {
            sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
        }
    }
</script>

</body>
</html>
