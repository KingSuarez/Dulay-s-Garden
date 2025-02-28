<?php
date_default_timezone_set('Asia/Manila');

include 'connection.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit;
}

// Initialize session variables for tracking timers
if (!isset($_SESSION['user_status'])) {
    $_SESSION['user_status'] = array();
}

// Function to update user statuses based on active and inactive orders
function updateUserStatus($conn) {
    // Fetch all users and check if they have any active orders or many cancelled/unclaimed orders
    $select_users = $conn->prepare("
        SELECT u.id, 
               (SELECT COUNT(*) FROM `orders` o WHERE o.user_id = u.id AND o.status NOT IN ('Completed', 'Cancelled', 'Unclaimed')) AS active_order_count,
               (SELECT COUNT(*) FROM `orders` o WHERE o.user_id = u.id AND o.status IN ('Cancelled', 'Unclaimed')) AS cancelled_or_unclaimed_count
        FROM `users` u 
        WHERE u.user_type = 'user'
    ");
    $select_users->execute();

    while ($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)) {
        $new_status = ($fetch_users['active_order_count'] > 0) ? 'Active' : 'Inactive';

        // If user has 3 or more canceled/unclaimed orders, set status to 'Blocked'
        if ($fetch_users['cancelled_or_unclaimed_count'] >= 3) {
            $new_status = 'Blocked';
        }

        // Update session and set status update timer
        $_SESSION['user_status'][$fetch_users['id']] = array(
            'status' => $new_status,
            'timestamp' => time()
        );

        // Update the user status in the database if necessary
        if ($new_status === 'Inactive' && (time() - $_SESSION['user_status'][$fetch_users['id']]['timestamp']) > 60) { // 1 minute
            $update_status = $conn->prepare("UPDATE `users` SET status = ? WHERE id = ?");
            $update_status->execute(['Inactive', $fetch_users['id']]);
            unset($_SESSION['user_status'][$fetch_users['id']]);
        }
    }
}

// Check if the update_status parameter is set and update user status
if (isset($_GET['update_status'])) {
    updateUserStatus($conn);
}

// Fetch users with updated status
$select_users = $conn->prepare("
    SELECT u.*, 
           (SELECT COUNT(*) FROM `orders` o WHERE o.user_id = u.id AND o.status NOT IN ('Completed', 'Cancelled', 'Unclaimed')) AS active_order_count,
           (SELECT COUNT(*) FROM `orders` o WHERE o.user_id = u.id AND o.status IN ('Cancelled', 'Unclaimed')) AS cancelled_or_unclaimed_count
    FROM `users` u 
    WHERE u.user_type = 'user' 
    ORDER BY u.id DESC
");
$select_users->execute();


if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_users = $conn->prepare("DELETE FROM `users` WHERE id = ?");
    $delete_users->execute([$delete_id]);
    header('location:user_a.php');
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Dulay's Garden Panel</title>

    <!-- CSS & JS Links -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/boxicons@2.0.9/dist/boxicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style1.css">
    <link rel="stylesheet" href="style/table-user.css">
    <link rel="stylesheet" href="modal.css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .selection-container {
            padding: 80px 10px;
        }
        button {
            outline: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            padding: 8px;
            color: #ffffff;
        }
        .table-head {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
        }
        .aproduct-btn {
            border-radius: 8px;
            outline: none;
            padding: 10px 20px;
            font-size: 20px;
            background-color: #79AC78;
            color: white;
            text-align: center;
            text-decoration: none;
        }
        .search {
            margin: 11px;
            font-size: 18px;
            background-color: #ffffff;
            border-radius: 7px;
        }
        .search .ser {
            padding: 8px 5px;
            text-align: justify;
            padding-left: 5px;
            border: none;
            background-color: #ffffff;
        }
        .search .ar {
            margin-left: 4px;
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
            <li><a href="dashboard.php"><i class='bx bx-box'></i><span class="links_name">Dashboard</span></a></li>
            <li><a href="product.php"><i class='bx bx-store'></i><span class="links_name">Product</span></a></li>
            <li><a href="info_up.php"><i class='bx bx-id-card'></i><span class="links_name">Update-profile</span></a></li>
            <li><a href="Aorder.php"><i class='bx bxs-box'></i><span class="links_name">Reserve Order</span></a></li>
            <li><a href="sales.php"><i class='bx bx-trending-up'></i><span class="links_name">Sales Report</span></a></li>
            <li><a href="user_a.php"><i class='bx bxs-user-account'></i><span class="links_name">Users</span></a></li>
            <li class="log_out"><a href="logout.php"><i class='bx bx-log-out'></i><span class="links_name">Logout</span></a></li>
        </ul>
    </div>

    <!-- Top Bar -->
    <section class="home-section" style="min-height: 160vh;">
        <nav>
            <div class="sidebar-button">
                <i class='bx bx-menu sidebarBtn'></i>
                <span class="dashboard">User Account</span>
            </div>
            <div class="profile-details">
                <?php
                $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                $select_profile->execute([$admin_id]);
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>
                <img src="img/<?= $fetch_profile['image']; ?>" alt="">
                <p style="margin-left: 50px; font-size: 20px; margin-top:10px;"><?= $fetch_profile['Fname']; ?></p>
            </div>
        </nav>

        <section class="selection-container">
            <h1 class="title" style="padding: 1rem; font-weight:bold">Customer Information</h1>

            <table id="myTable" class="display" style="width:100%;">
                <thead>
                    <tr>
                        <!-- <th>Id</th> -->
                        <th>Image</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Contact</th>
                        <th width="15%">Email</th>
                        <th>Created Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                    <tbody>
                        <?php
                        $number = 1;
                        while ($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)) {
                            // Determine user status based on active and canceled/unclaimed orders
                            $status = ($fetch_users['active_order_count'] > 0) ? 'Active' : 'Inactive';
                            
                            // If the user has 3 or more canceled/unclaimed orders, mark them as 'Blocked'
                            if ($fetch_users['cancelled_or_unclaimed_count'] >= 3) {
                                $status = 'Blocked';
                            }
                            ?>
                            <tr data-user-id="<?= $fetch_users['id'] ?>" data-status="<?= $status ?>" data-inactive-timer="<?= $status === 'Inactive' ? 60000 : null ?>">
                                <td hidden><?= $number ?></td>
                                <td><img src="img/<?= $fetch_users['image']; ?>" alt="Product Image" id="img"></td>
                                <td><?= $fetch_users['Fname'] ?></td>
                                <td><?= $fetch_users['Mname'] ?></td>
                                <td><?= $fetch_users['Lname'] ?></td>
                                <td><?= $fetch_users['contact'] ?></td>
                                <td><?= $fetch_users['email'] ?></td>
                                <td><?= date("Y-m-d", strtotime($fetch_users['created_at'])); ?></td>
                                <td style="color: <?= $status === 'Blocked' ? 'red' : ($status === 'Inactive' ? 'red' : 'green') ?>"><?= $status ?></td>
                                <td>
                                    <a href="user_a.php?delete=<?= $fetch_users['id']; ?>" onclick="return confirm('Delete this user?');">
                                        <i class="fa-solid fa-trash-can fa-xl" style="color: #D04848;"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                            $number++;
                        }
                        ?>
                    </tbody>
            </table>
        </section>
    </section>




    <script>
$(document).ready(function() {
    $('#myTable').DataTable();

    // Function to send request to update user status
    function updateUserStatus() {
        $.ajax({
            url: 'user_a.php', // PHP file to handle status update
            method: 'GET',
            data: { update_status: 1 },
            success: function(response) {
                console.log('User statuses updated.');
            },
            error: function() {
                console.error('Failed to update user statuses.');
            }
        });
    }

    // Set interval to call the function every 1 minute (60000 milliseconds)
    setInterval(updateUserStatus, 60000); // Adjust time as needed

    // Optional: Call immediately on page load
    updateUserStatus();
});
</script>

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
