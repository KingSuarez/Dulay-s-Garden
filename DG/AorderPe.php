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

// Update order status if POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    try {
        // Update the status of the order in the database
        $update_query = $conn->prepare("UPDATE uorders SET status = ? WHERE o_id = ?");
        $update_query->execute([$status, $order_id]);

        // Check if the status is 'Cancelled'
        if ($status === 'Cancelled') {
            // Retrieve the quantity and product details
            $order_query = $conn->prepare("SELECT pname, quantity FROM uorders WHERE o_id = ?");
            $order_query->execute([$order_id]);
            $order = $order_query->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                $product_name = $order['pname'];
                $quantity = $order['quantity'];

                // Update the stock quantity of the product
                $stock_update_query = $conn->prepare("UPDATE products SET stock = stock + ? WHERE pname = ?");
                $stock_update_query->execute([$quantity, $product_name]);

                // Mark the order for deletion with a timestamp
                // $delete_query = $conn->prepare("UPDATE uorders SET deleted_at = NOW()-INTERVAL 1 WEEK WHERE o_id = ?");
                // $delete_query->execute([$order_id]);
            }
        }

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit();
    } catch (PDOException $e) {
        // Handle database error
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        exit();
    }
}

// Retrieve search query and date filter if they exist
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$filter_date = isset($_GET['filterDate']) ? $_GET['filterDate'] : '';

try {
    // Start building the SQL query
    $sql = "SELECT u.id AS user_id, u.Fname AS Fname, u.Lname AS Lname, o.o_id, o.pname, o.quantity, o.Tprice, o.status, o.date, o.expiration_date 
            FROM uorders o
            JOIN users u ON o.user_id = u.id
            WHERE (u.Fname LIKE ? OR u.Lname LIKE ?)
            AND o.status = 'Pending'";

    // Add date filter to the SQL query if provided
    if (!empty($filter_date)) {
        $sql .= " AND DATE(o.date) = ?";
    }

    $sql .= " ORDER BY o.date DESC, u.id";

    $orders_query = $conn->prepare($sql);

    // Execute the query with parameters
    if (!empty($filter_date)) {
        $orders_query->execute(['%' . $search_query . '%', '%' . $search_query . '%', $filter_date]);
    } else {
        $orders_query->execute(['%' . $search_query . '%', '%' . $search_query . '%']);
    }

    $orders = $orders_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database error
    die("Database error: " . $e->getMessage());
}
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
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>

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

        .filter-buttons input[type="text"],
        .filter-buttons input[type="date"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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
    </style>
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
    <br><br><br><br>

    <section>
        <div class="orders-container">
            <div class="filter-buttons">
               <div class="buttons">
                <!-- <button onclick="window.location.href='all_orders.php'">All</button> -->
                <button onclick="window.location.href='AorderPe.php'">Pending</button>
                <button onclick="window.location.href='AorderP.php'">Processing</button>
                <button onclick="window.location.href='AorderCom.php'">Completed</button>
                <button onclick="window.location.href='AorderCan.php'">Cancelled</button> 
                </div> 
                <!-- Date Filter Form -->
                <form method="GET" action="AorderPe.php" style="margin-bottom: 10px;">
                    <input type="date" name="filterDate" id="filterDate" value="<?= htmlspecialchars($filter_date) ?>">
                    <button type="submit" style="padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Filter by Date</button>
                </form>

                 <!-- Search Form -->
                 <form method="GET" action="AorderPe.php">
                      <input type="text" name="search" placeholder="Search by name or last name" value="<?= htmlspecialchars($search_query) ?>">
                      <button type="submit" style="padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">Search</button>
                 </form>
            </div>


            <?php if (empty($orders)) : ?>
            <p>No records found.</p>
            <?php else: ?>

            <?php
                    $current_user_id = null;

                    foreach ($orders as $order) {
                        // Check if the order status is 'Pending'
                        if ($order['status'] === 'Pending') {
                            if ($order['user_id'] !== $current_user_id) {
                                // Close the previous user section if it exists
                                if ($current_user_id !== null) {
                                    echo "</tbody></table></div>";
                                }
                                // Start a new user section
                                $current_user_id = $order['user_id'];
                                echo "<div class='user-section' data-user='{$current_user_id}'>";
                                echo "<h3>" . htmlspecialchars($order['Fname']) . " " . htmlspecialchars($order['Lname']) . "</h3>";
                                echo "<table id='myTable'>
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                                <th>Purchase Date</th>
                                                <th>Expiry Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>";
                            }

                            // Output the order details
                            echo "<tr data-status='" . htmlspecialchars($order['status']) . "'>
                                <td>" . htmlspecialchars($order['pname']) . "</td>
                                <td>" . htmlspecialchars($order['quantity']) . "</td>
                                <td>" . htmlspecialchars($order['Tprice']) . "</td>
                                <td>" . htmlspecialchars($order['status']) . "</td>
                                <td>" . htmlspecialchars($order['date']) . "</td>
                                <td>" . htmlspecialchars($order ['expiration_date']) . "</td>
                                <td>
                                    <form method='POST' class='status-form'>
                                        <input type='hidden' name='order_id' value='" . htmlspecialchars($order['o_id']) . "'>
                                        <select name='status' onchange='updateOrderStatus(this)'>
                                            <option value='Pending'" . ($order['status'] == 'Pending' ? ' selected' : '') . ">Pending</option>
                                            <option value='Processing'" . ($order['status'] == 'Processing' ? ' selected' : '') . ">Processing</option>
                                            <option value='Completed'" . ($order['status'] == 'Completed' ? ' selected' : '') . ">Completed</option>
                                            <option value='Cancelled'" . ($order['status'] == 'Cancelled' ? ' selected' : '') . ">Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>";
                        }
                    }

                    // Close the last user section if necessary
                    if ($current_user_id !== null) {
                        echo "</tbody></table></div>";
                    }
                    ?>
                    <?php endif; ?>

        </div>
    </section>
</section>
    <!-- Initialize DataTables -->
    <script>
        $(document).ready(function() {
            // Initialize DataTable for each user section
            $('.user-section table').each(function() {
                $(this).DataTable();
            });
        });

        function updateOrderStatus(selectElement) {
            var form = $(selectElement).closest('form');
            $.post('', form.serialize(), function(response) {
                if (response.success) {
                } else {
                }
            }, 'json');
        }
    </script>

</body>
</html>
