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

// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Pay'])) {
//     if (!isset($_POST['terms'])) {
//         echo "<script>alert('You need to check the box before proceeding.'); window.history.back();</script>";
//         exit();
//     }

//     if (!isset($_POST['payment_option']) || !in_array($_POST['payment_option'], ['Fullpayment', 'Downpayment'])) {
//         echo "<script>alert('Invalid payment option.'); window.history.back();</script>";
//         exit();
//     }

//     $payment_option = $_POST['payment_option'];
//     $overallTotal = 0;

//     if (isset($_SESSION['checkout_items']) && !empty($_SESSION['checkout_items'])) {
//         foreach ($_SESSION['checkout_items'] as $item_id) {
//             $show_products = $conn->prepare("SELECT * FROM `carts` WHERE id = ? AND user_id = ?");
//             $show_products->execute([$item_id, $user_id]);

//             if ($show_products->rowCount() > 0) {
//                 $fetch_products = $show_products->fetch(PDO::FETCH_ASSOC);
//                 $quantity = $fetch_products['quantity'];
//                 $price = $fetch_products['price'];
//                 $total = $price * $quantity;
//                 $overallTotal += $total;
//             }
//         }
//     }

//     $downpayment = $payment_option == 'Fullpayment' ? $overallTotal : $overallTotal * 0.25;
//     $balance = $payment_option == 'Fullpayment' ? 0 : $overallTotal - $downpayment;

//     echo "<script>alert('Paying with Downpayment: ₱$downpayment');</script>";

//     $created_at = date('Y-m-d H:i:s');
//     $expiration = date('Y-m-d H:i:s', strtotime('+1 month'));

//     try {
//         $conn->beginTransaction();

//         // Insert into orders
//         $order_sql = "INSERT INTO `orders` (user_id, payment, amount, balance, status, created_at, expiration) VALUES (?, ?, ?, ?, 'Pending', ?, ?)";
//         $stmt = $conn->prepare($order_sql);
//         $stmt->execute([$user_id, $payment_option, $overallTotal, $balance, $created_at, $expiration]);

//         if ($stmt) {
//             $order_id = $conn->lastInsertId();
//             $data = [];

//             $cart = $conn->prepare("SELECT c.*, p.id as pid, p.price FROM `carts` c INNER JOIN `products` p ON p.id = c.p_id WHERE c.user_id = ?");
//             $cart->execute([$user_id]);

//             while ($row = $cart->fetch(PDO::FETCH_ASSOC)) {
//                 $total = $row['price'] * $row['quantity'];
//                 $data[] = "('{$order_id}', '{$row['pid']}', '{$row['quantity']}', '{$row['price']}', '{$total}')";
//             }

//             if ($data) {
//                 $list_sql = "INSERT INTO `order_list` (o_id, p_id, quantity, price, total) VALUES " . implode(', ', $data);
//                 $save_olist = $conn->exec($list_sql);

//                 if ($save_olist) {
//                     $empty_cart = $conn->prepare("DELETE FROM `carts` WHERE user_id = ?");
//                     $empty_cart->execute([$user_id]);

//                     // Update sales table
//                     $sales_sql = "INSERT INTO `sales` (order_id, total_amount) VALUES (?, ?)";
//                     $sales_stmt = $conn->prepare($sales_sql);
//                     $sales_stmt->execute([$order_id, $overallTotal]);

//                     $conn->commit();
//                     echo json_encode(['status' => 'success']);
//                     header("location: Acart.php");
//                     exit();
//                 } else {
//                     $conn->rollBack();
//                     echo json_encode(['status' => 'failed', 'error' => 'Failed to save order list']);
//                 }
//             } else {
//                 $conn->rollBack();
//                 echo json_encode(['status' => 'failed', 'error' => 'No cart items found']);
//             }
//         } else {
//             $conn->rollBack();
//             echo json_encode(['status' => 'failed', 'error' => 'Failed to insert order']);
//         }
//     } catch (Exception $e) {
//         $conn->rollBack();
//         echo json_encode(['status' => 'failed', 'error' => $e->getMessage()]);
//     }
// }
// Fetch orders based on the selected status
$status = isset($_GET['status']) ? $_GET['status'] : 'Pending';
$qry = $conn->prepare("SELECT o.*, CONCAT(c.Fname, ' ', c.Lname) AS users FROM `orders` o INNER JOIN users c ON c.id = o.user_id WHERE o.status = ? ORDER BY unix_timestamp(o.created_at) DESC");
$qry->execute([$status]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders - Dulay's Garden Panel</title>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style1.css">
    <link rel="stylesheet" href="style/table-user.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .orders-container {
            padding: 20px;
        }

        .filter-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filter-buttons .buttons {
            display: flex;
        }

        .filter-buttons .buttons button {
            margin-right: 10px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .filter-buttons .buttons button:hover {
            background-color: #0056b3;
        }

        .filter-buttons form {
            display: flex;
            align-items: center;
        }

        .filter-buttons input[type="text"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }

        .status-form {
            display: inline;
        }

        .user-section {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
        }

        .dropdown-menu {
            border-radius: 0; /* Optional: Remove border radius for a sharper look */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Optional: Add shadow for better visibility */
            margin-top: 0; /* Align dropdown menu with the button */
        }
        
        .btn-flat.btn-default {
            background-color: #f8f9fa; /* Light background color for button */
            border-color: #ced4da; /* Border color */
        }
        
        .btn-flat.btn-default:hover {
            background-color: #e2e6ea; /* Change background on hover */
            border-color: #dae0e5; /* Change border color on hover */
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa; /* Background color of dropdown items on hover */
            color: #333; /* Text color on hover */
        }
        
        .dropdown-menu .dropdown-item {
            padding: 8px 16px; /* Add padding for better spacing */
        }
        
        .dropdown-divider {
            margin: 0; /* Remove margin to align the divider with items */
        }

        .card {
            margin: 20px;
        }
        .buttons {
            margin-bottom: 20px;
        }
        .buttons button {
            margin-right: 10px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .buttons button:hover {
            background-color: #0056b3;
        }

        .tablist {
    display: flex;
    justify-content: space-around;
    border-bottom: 2px solid #f2f2f2;
    margin-bottom: 20px;
}

.tab-button {
    background-color: transparent;
    border: none;
    outline: none;
    padding: 10px 20px;
    cursor: pointer;
    font-size: 16px;
    color: #555;
    position: relative;
}

.tab-button:hover {
    color: #387F39; /* Change to your theme color */
}

.tab-button:focus {
    color: #387F39;
    border-bottom: 2px solid #387F39;
}

.tab-button:active {
    color: #387F39;
    border-bottom: 2px solid #387F39;
}

.tab-button.active {
    color: #387F39;
    border-bottom: 2px solid #387F39;
}

.tab-button::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background-color: transparent;
    transition: background-color 0.3s ease;
}

.tab-button:hover::after,
.tab-button:focus::after,
.tab-button:active::after,
.tab-button.active::after {
    background-color: #387F39;
}

    </style>
</head>
<body>
<!-- SideBar -->
<div class="sidebar">
    <div class="logo-details">
        <img src="44-removebg-preview.png" alt="Logo">
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
            <a href="Aorder.php">
                <i class='bx bxs-box'></i>
                <span class="links_name">Reserve Order</span>
            </a>
        </li>
        <li>
            <a href="sales.php">
                <i class='bx bx-trending-up'></i>
                <span class="links_name">Sales Report</span>
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
            <span class="dashboard">Reserve Orders</span>
        </div>
        <div class="profile-details">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <img src="img/<?= htmlspecialchars($fetch_profile['image']); ?>" alt="Profile Image">
            <p style="margin-left: 50px; font-size: 20px;"><?= htmlspecialchars($fetch_profile['Fname']); ?></p>
        </div>
    </nav>
    <br><br><br><br>

<section>
    <br> <br>
    <div class="card-body" style="background-color: white;">
<div class="tablist">
    <button class="tab-button active" onclick="window.location.href='Aorder.php'">Pending</button>
    <button class="tab-button" onclick="window.location.href='AorderP.php'">Processing</button>
    <button class="tab-button" onclick="window.location.href='AorderRTPU.php'">Ready To Pick Up</button>
    <button class="tab-button" onclick="window.location.href='AorderCom.php'">Completed</button>
    <button class="tab-button" onclick="window.location.href='AorderCan.php'">Cancelled</button>
    <button class="tab-button" onclick="window.location.href='AorderUnc.php'">Unclaimed</button>
</div>

<div class="card card-outline card-primary">

        <div class="card-body">
            <div class="container mt-4">
           
                    <h1 class="card-title">List of Orders </h1>
            
                <table id="orderTable" class="display table table-bordered table-stripped">
                    <colgroup>
                        <col width="10%">
                        <col width="15%">
                        <col width="22%">
                        <col width="18%">
                        <col width="16%">
                        <col width="19%">
                        <col width="18%">
                        <col width="20%">
                        <col width="15%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date Order</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Balance</th>
                            <th>expiration</th>
                            <th>Payment</th>
                            <th >Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        // $qry = $conn->query("SELECT o.*, CONCAT(c.Fname, ' ', c.Lname) AS users FROM `orders` o INNER JOIN users c ON c.id = o.user_id ORDER BY unix_timestamp(o.created_at) DESC");
                        while ($row = $qry->fetch(PDO::FETCH_ASSOC)):
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $i++; ?></td>
                            <td><?php echo date("Y-m-d", strtotime($row['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($row['users']); ?></td>
                            <td class="text-center"><?php echo number_format($row['amount']); ?></td>
                            <td class="text-center"><?php echo ($row['balance']); ?></td>
                            <td><?php echo date("Y-m-d", strtotime($row['expiration'])); ?></td>

                            <td class="text-center">
                                <?php if ($row['payment'] == 'Fullpayment'): ?>
                                    <span class="badge badge-success">Full payment</span>
                                <?php else: ?>
                                    <span class="badge badge-light">Downpayment</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php
                                switch ($row['status']) {
                                    case 'Pending':
                                        echo "<span class='badge badge-light'>Pending</span>";
                                        break;
                                    case 'Processing':
                                        echo "<span class='badge badge-primary'>Processing</span>";
                                        break;
                                    case 'Ready-To-Pick-Up':
                                        echo "<span class='badge badge-primary'>Ready-To-Pick-Up</span>";
                                        break;
                                    case 'Completed':
                                        echo "<span class='badge badge-success'>Completed</span>";
                                        break;
                                    case 'Cancelled':
                                        echo "<span class='badge badge-danger'>Cancelled</span>";
                                        break;
                                    case 'Unclaim':
                                        echo "<span class='badge badge-secondary'>Unclaim</span>";
                                        break;
                                }
                                ?>
                            </td>
                            <td align="center">
                            <a class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item" href="view_order.php?id=<?= htmlspecialchars($row['id']) ?>">View Order</a>
                                <?php if ($row['status'] == 'Pending'): ?> 
                                    <!-- <a class="dropdown-item pay_order" href="javascript:void(0)" data-id="<?= htmlspecialchars($row['id']); ?>">Mark as Paid</a> -->
                                <?php endif; ?>
                                <!-- <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?= htmlspecialchars($row['id']); ?>">
                                    <span class="fa fa-trash text-danger"></span> Delete
                                </a> -->
                            </div>

                        </td>

                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>

    </div>

    <!-- view-modal -->

</section>
</section>



<script>

      $(document).ready(function() {
        $('#orderTable').DataTable();
    });
</script>

<script>
$(document).ready(function() {
    $('#orderTable').DataTable();

    $('.view_order').click(function() {
        var orderId = $(this).data('id');

        $.ajax({
            url: 'get_order_details.php',
            method: 'GET',
            data: { id: orderId },
            success: function(response) {
                $('#viewOrderModal .modal-body').html(response);
                $('#viewOrderModal').modal('show');
            }
        });
    });
});
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete_data');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-id');

            if (confirm('Are you sure you want to delete this order?')) {
                fetch('delete_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${orderId}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'success') {
                        alert('Order deleted successfully.');
                        location.reload(); // Refresh the page to update the order list
                    } else {
                        alert('Failed to delete the order. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the order.');
                });
            }
        });
    });
});

</script>

<script>
    const tabs = document.querySelectorAll('.tab-button');

tabs.forEach(tab => {
    tab.addEventListener('click', function() {
        tabs.forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
    });
});

</script>
<!-- <script>
$(document).ready(function() {
    // Attach click event to the "View Order" links
    $('.view-order').on('click', function(e) {
        e.preventDefault();

        var orderId = $(this).data('id');

        $.ajax({
            url: 'fetch_order_details.php',
            type: 'GET',
            data: { id: orderId },
            success: function(response) {
                // Assuming the response is HTML content for the modal
                $('#orderDetails').html(response);
                $('#orderModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + error);
            }
        });
    });
});
</script> -->
  <!-- JavaScript for DataTables and Sidebar Toggle -->
  <script>
        $(document).ready(function() {
            $('#myTable').DataTable();

            // Sidebar toggle function
            let sidebar = document.querySelector(".sidebar");
            let sidebarBtn = document.querySelector(".sidebarBtn");
            sidebarBtn.onclick = function() {
                sidebar.classList.toggle("active");
                if (sidebar.classList.contains("active")) {
                    sidebarBtn.classList.replace("bx-menu", "bx-menu-alt-right");
                } else {
                    sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
                }
            };
        });
    </script>

</body>
</html>
