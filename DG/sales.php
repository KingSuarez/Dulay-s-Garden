<?php
// Connection to the database
include 'connection.php';
session_start();

// Check if the admin is logged in
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('Location: login.php');
    exit();
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
    /* Centering the table header and body */
    table th, table td {
        text-align: center;
        vertical-align: middle;
    }

    /* Aligning the buttons */
    .form-group {
        margin-bottom: 10px; /* Optional, to adjust spacing */
    }

    .form-group.col-md-1 {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Optional styling for buttons */
    .btn {
        margin: 0 5px;
    }

    /* Styling for print header to center text */
    #print_header {
        text-align: center;
    }

/* Ensure consistent button font size and height */
.btn {
    display: flex;
    align-items: center; /* Center items vertically */
    justify-content: center; /* Center items horizontally */
    gap: 5px; /* Space between icon and text */
    padding: 8px 12px; /* Adjust padding for consistent height */
    font-size: 0.875rem; /* Font size for the button */
    height: 38px; /* Set a fixed height to match input fields */
    line-height: 1.5; /* Ensure consistent vertical alignment */
}

.btn i {
    margin-right: 5px; /* Space between icon and text */
}

.btn-primary {
    background-color: #007bff; /* Primary button color */
    color: white;
    border: none;
    border-radius: 5px;
}

.btn-primary:hover {
    background-color: #0056b3; /* Hover color for primary button */
}

.btn-success {
    background-color: #28a745; /* Success button color */
    color: white;
    border: none;
    border-radius: 5px;
}

.btn-success:hover {
    background-color: #218838; /* Hover color for success button */
}

/* Ensure consistent styling for form controls */
.form-control {
    height: 38px; /* Match the height of the buttons */
    padding: 2px 12px; /* Adjust padding */
    font-size: 1.25rem; /* Font size for the input fields */
}

/* Flexbox layout for form row */
#filter-form .form-row {
    display: flex;
    gap: 15px;
    align-items: center; /* Align items vertically centered */
    margin-left: 20px;
}

/* Flexbox layout for form group */
#filter-form .form-group {
    display: flex;
    flex-direction: column;
}
.filter-buttons {
    display: flex; /* Align buttons horizontally */
    gap: 10px; /* Space between buttons */
    align-items: center; /* Center items vertically */
}

.filter-buttons button {
    display: flex;
    align-items: center; /* Center icon and text vertically */
    justify-content: center; /* Center icon and text horizontally */
    gap: 5px; /* Space between icon and text */
    padding: 8px 12px; /* Adjust padding for consistency */
    font-size: 1rem; /* Font size for consistency */
    height: 38px; /* Match the height of the input fields */
    line-height: 1.5; /* Ensure vertical alignment */
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.filter-buttons button i {
    margin-right: 5px; /* Space between icon and text */
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
            <span class="dashboard">Sales Report</span>
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
<br><br>
    <!-- Sales Report Section -->
    <section>
    <?php 
    // Set date filters
    $date_start = isset($_GET['date_start']) ? $_GET['date_start'] : date("Y-m-d", strtotime("-7 days"));
    $date_end = isset($_GET['date_end']) ? $_GET['date_end'] : date("Y-m-d");

    // Set category filter
    $category = isset($_GET['category']) ? $_GET['category'] : '';

    // Fetch distinct categories from the products table
    $categories = $conn->query("SELECT DISTINCT category FROM products")->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h1 class="card-title" style="font-size: large;">Sales Report</h1>
            <hr>
            <br>
        </div>
        
        <div class="card-body">
            <form id="filter-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_start">Date Start</label>
                        <input type="date" class="form-control form-control-sm" name="date_start" value="<?php echo htmlspecialchars($date_start); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="date_end">Date End</label>
                        <input type="date" class="form-control form-control-sm" name="date_end" value="<?php echo htmlspecialchars($date_end); ?>">
                    </div>

                    <!-- Category Filter -->
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select name="category" class="form-control form-control-sm">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['category']); ?>" 
                                    <?php echo ($category == $cat['category']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <div class="filter-buttons">
                            <button class="btn btn-flat btn-block btn-primary btn-sm">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                            <button class="btn btn-flat btn-block btn-success btn-sm" type="button" id="printBTN">
                                <i class="fa fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <hr>
            
            <!-- Printable Section -->
            <div id="printable">
                <div class="row row-cols-2 justify-content-center align-items-center" id="print_header" style="display:none">
                    <div class="col-7">
                        <h3 class="text-center m-0"><b>Sales Report</b></h3>
                        <p class="text-center m-0">Date Between <?php echo htmlspecialchars($date_start); ?> and <?php echo htmlspecialchars($date_end); ?></p>
                    </div>
                </div>
                <hr>

                <table class="table table-bordered">
                    <colgroup>
                        <col width="5%">
                        <col width="15%">
                        <col width="25%">
                        <col width="25%">
                        <col width="10%">
                        <col width="20%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Client</th>
                            <th>QTY</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $i = 1;
                        $total_amount = 0;  // Initialize the total amount variable

                        // Modify SQL query to include category filtering
                        $query = "
                            SELECT ol.*, o.created_at, p.pname as pname, concat(c.Fname, ' ', c.Lname) as Cname, c.email, p.category 
                            FROM `order_list` ol 
                            INNER JOIN `orders` o ON o.id = ol.id  
                            INNER JOIN `products` p ON p.id = ol.p_id  
                            INNER JOIN `users` c ON c.id = o.user_id 
                            WHERE date(o.created_at) BETWEEN :date_start AND :date_end
                        ";
                        if (!empty($category)) {
                            $query .= " AND p.category = :category";
                        }
                        $query .= " ORDER BY unix_timestamp(o.created_at) DESC";

                        // Prepare and execute query
                        $qry = $conn->prepare($query);
                        $params = [
                            ':date_start' => $date_start,
                            ':date_end' => $date_end
                        ];

                        if (!empty($category)) {
                            $params[':category'] = $category;
                        }

                        $qry->execute($params);

                        // Fetch and display results
                        while ($row = $qry->fetch(PDO::FETCH_ASSOC)) {
                            $total_amount += $row['total'];  // Add each row's total to the grand total
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $i++ ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td>
                                    <p class="m-0"><?php echo htmlspecialchars($row['pname']); ?></p>
                                    <p class="m-0"><small>Category: <?php echo htmlspecialchars($row['category']); ?></small></p>
                                </td>
                                <td>
                                    <p class="m-0"><?php echo htmlspecialchars($row['Cname']); ?></p>
                                    <p class="m-0"><small>Email: <?php echo htmlspecialchars($row['email']); ?></small></p>
                                </td>
                                <td class="text-center"><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td class="text-right"><?php echo number_format($row['total'], 2); ?></td>
                            </tr>
                        <?php 
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total Amount:</strong></td>
                            <td class="text-right"><strong><?php echo number_format($total_amount, 2); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>


<!-- Footer -->
<footer>
    <div class="footer-content">
        <p>&copy; 2024 Dulay's Garden</p>
    </div>
</footer>

<script>
$(document).ready(function() {
    $('#printBTN').click(function() {
        let printContent = document.getElementById('printable').innerHTML;
        let logoSrc = 'Images/IMG_1210 1-1.png'; // Replace with your actual logo path
        let printWindow = window.open('', '', 'height=600,width=800');

        // Modify the content to include the logo at the top
        printWindow.document.write('<html><head><title>Print Report</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('body { text-align: center; font-family: Arial, sans-serif; }');
        printWindow.document.write('table { width: 100%; border-collapse: collapse; margin: 20px 0; }');
        printWindow.document.write('table, th, td { border: 1px solid black; }');
        printWindow.document.write('th, td { padding: 10px; text-align: center; }');
        printWindow.document.write('</style>');
        printWindow.document.write('</head><body>');
        
        // Add the logo at the top before the report content
        printWindow.document.write('<div style="text-align: center;">');
        printWindow.document.write('<img src="' + logoSrc + '" alt="Company Logo" style="width: 200px; margin-bottom: 10px;">'); // Adjust size and margin as needed
        printWindow.document.write('</div>');

        // Add the report content
        printWindow.document.write(printContent);
        printWindow.document.write('</body></html>');
        
        printWindow.document.close();
        printWindow.print();
    });

    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serialize();
        window.location.href = "sales.php?" + formData;
    });
});

</script>


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
