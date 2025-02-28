<?php

include 'connection.php';
date_default_timezone_set('Asia/Manila');

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
}

// Handle AJAX request
if(isset($_POST['action']) && $_POST['action'] == 'fetchCounts'){
    // Fetch users count
    $select_users = $conn->prepare("SELECT * FROM `users` WHERE user_type = ?");
    $select_users->execute(['user']);
    $number_of_users = $select_users->rowCount();
 
    // Fetch products count
    $select_product = $conn->prepare("SELECT * FROM `products`");
    $select_product->execute();
    $number_of_products = $select_product->rowCount();
 
    // Fetch pending orders count
    $select_order = $conn->prepare("SELECT * FROM `uorders` WHERE status = ?");
    $select_order->execute(['pending']);
    $number_of_orders = $select_order->rowCount();
 
    // Return the counts in JSON format
    echo json_encode([
       'users' => $number_of_users,
       'products' => $number_of_products,
       'orders' => $number_of_orders
    ]);
    exit;
 }

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <title> Dulay's Garden Panel </title>
  <!-- Boxicons CDN Link -->
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
  <script src="https://unpkg.com/boxicons@2.0.9/dist/boxicons.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!---cdn jquery-->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- font awesome cdn link  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<!-- custom css file link  -->
<link rel="stylesheet" href="style/dashboard.css">
<link rel="stylesheet" href="style/style.css">
<link rel="stylesheet" href="style/style1.css">
<link rel="stylesheet" href="style/table-user.css">

  
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
         
   
          .box-container{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(27rem, 1fr));
            gap:1.5rem;
            align-items: flex-start;
            padding: 80px 10px;
            padding-bottom: 40px;

         }
         /* boddy in Dashboard Homepage*/
          .box-container .box{
            padding:1.5rem;
            text-align: center;
            box-shadow: var(--box-shadow);
            background-color: #99BC85;
            border-radius: 1rem;
         }
      
          .box-container .box h3{
            font-size: 3.5rem;
            color:var(--black);
            font-variant-numeric: normal;
         }
      
          .box-container .box p{
            font-size: 2rem;
            background-color: #994D1C;
            color:var(--light-color);
            padding:1.5rem;
            margin:1rem 0;
            border-radius: .5rem;
            color: #fff;;
         }
   
         .chart-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* 3 columns */
    grid-gap: 25px; /* Gap between grid items */
}

.chart {
    border: 1px solid #ccc; /* Border around each chart */
}

canvas {
    width: 100%;
    height: 100%;
}
@media screen and (max-width: 600px) {
    .chart-container {
        grid-template-columns: repeat(auto-fit, minmax(50px, 1fr)); /* Adjust for smaller screens */
    }
}
         
.btn {
    display: flex;
    align-items: center; /* Center items vertically */
    justify-content: center; /* Center items horizontally */
    gap: 5px; /* Space between icon and text */
    padding: 8px 12px; /* Adjust padding for consistent height */
    font-size: 1.2em; /* Font size for the button */
    height: 38px; /* Set a fixed height to match input fields */
    line-height: 1; /* Ensure consistent vertical alignment */
    height: 35px; /* Match the height of the buttons */
    width: 70px;
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


      </style>

</head>


<body>

<!-- SideBar -->
<div class="sidebar">
  <div class="logo-details">
      <!-- <i class='bx bxl-flutter'></i> -->
      <img src="44-removebg-preview.png" alt="44-removebg-preview.png">
      <span class="logo_name" style="color: darkgreen;">Dulay's <span style="margin-left: 10px; color:darkgreen">Garden</span></span>
  </div>
  <ul class="nav-links">
    <li>
      <a href="dashboard.php">
        <i class='bx bx-box' ></i>
        <span class="links_name">Dashboard</span>
      </a>
    </li>
    <li>
      <a href="product.php">
        <i class='bx bx-store' ></i>
        <span class="links_name">Product</span>
      </a>
    </li>
    <li>
      <a href="info_up.php">
        <i class='bx bx-id-card' ></i>
        <span class="links_name">Update-profile</span>
      </a>
    </li>
    <li>
      <a href="Aorder.php">
        <i class='bx bxs-box' ></i>
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
        <i class='bx bxs-user-account' ></i>
        <span class="links_name">Users</span>
      </a>
    </li>
   
    
    <form method="post">
    <li class="log_out">
      <a href="logout.php">
        <i class='bx bx-log-out' ></i>
        <span class="links_name">Logout</span>
      </a>
    </li>
    </form>

  </ul>
</div>
  
<!-- Top Bar -->
<section class="home-section" style="min-height: 160vh;">
  <nav>
    <div class="sidebar-button">
      <i class='bx bx-menu sidebarBtn'></i>
      <span class="dashboard">Dashboard</span>
    </div>

    <div class="profile-details">
      <!-- Profile Details -->
      <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="img/<?= $fetch_profile['image']; ?>" alt="img">
         <p style="   margin-left: 50px; font-size: 20px;"><?= $fetch_profile['Fname']; ?></p>
    </div>
  </nav>

   <div class="box-container" id="container">
      <div class="box">
         <h3 id="userCount"></h3>
         <p>User Accounts</p>
      </div>

      <div class="box">
         <h3 id="productCount"></h3>
         <p>Products</p>
      </div>

      <div class="box">
         <h3 id="orderCount"></h3>
         <p>Orders</p>
      </div>
   </div>

 <!-- Sales Section -->
 <div style="display: block; box-sizing: border-box; border: 1px solid #ccc;" width = 100%; height = 25%;>
    <h2 style="font-size: 30px; text-align:center; "> Sales Chart</h2>

 <div>

        <label style="margin-left: 10px;" for="startDate">Start Date:</label>
        <input style="margin-left: 5px;" type="date" id="startDate" name="startDate" value="<?php echo date('Y-m-01'); ?>">

        <label style="margin-left: 10px;" for="endDate">End Date:</label>
        <input style="margin-left: 5px;" type="date" id="endDate" name="endDate" value="<?php echo date('Y-m-d'); ?>">

        <label style="margin-left: 5px;" for="sortType">Sort By:</label>
        <select style="margin-left: 5px;" id="sortType" name="sortType">
            <option value="day" selected>Day</option>
            <option value="week">Week</option>
            <option value="year">Year</option>
        </select>

        <button style="display: unset; margin-left: 10px;" id="filterButton" class="btn btn-flat btn-block btn-primary btn-sm"> <i class="fa fa-filter"></i>Filter</button>
    </div>

    <!-- Sales Chart -->
    <canvas id="salesChart" width="800" height="200"></canvas>

    </div>
<br> <br> <br>

    <?php include 'load_dashboard.php'; ?>

<div class="chart-container">
    <!-- <div class="chart" id="chartContainer1">
        <canvas id="myChart1" width="100%" height="50%"></canvas>
    </div> -->
    <div style="border: 1px solid #ccc; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" padding: 10px; margin: 10px;> 
<?php
// Fetch the current threshold from the database
$stmt = $conn->prepare("SELECT critical_stock FROM critical WHERE id = 1");
$stmt->execute();
$setting = $stmt->fetch(PDO::FETCH_ASSOC);

// Set the current threshold from the database, default to 20 if not set
$threshold = $setting['critical_stock'] ?? 20;
?>

<!-- Display the critical stock threshold with editable input -->
<h2 style="text-align: center; font-size:18px;">
    Critical Stock (Threshold: 
    <span id="thresholdValue" contenteditable="true" style="border-bottom: 1px dashed; display: inline-block; min-width: 30px; text-align: center;">
        <?php echo $threshold; ?>
    </span>)
</h2>

<p id="saveStatus" style="color: green; text-align: center;"></p>

<!-- Table to display products below the critical threshold -->
<table style="width: 100%; border-collapse: collapse;" id="productTable">
    <thead>
        <tr style="border-bottom: 1px solid #ddd;">
            <th style="background-color:#ACE1AF; padding: 8px; text-align: center;">Product</th>
            <th style="background-color:#ACE1AF; padding: 8px; text-align: center;">Stock</th>
        </tr>
    </thead>
    <tbody id="productData">
        <?php
        // SQL query to fetch products below the critical threshold
        $criticalS = $conn->prepare("SELECT pname, stock FROM products WHERE stock < :threshold");
        $criticalS->bindParam(':threshold', $threshold, PDO::PARAM_INT);
        $criticalS->execute();
        $results = $criticalS->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $row) {
            echo '<tr>';
            echo '<td style="padding: 8px; border-bottom: 1px solid #ddd; text-transform: capitalize;">' . htmlspecialchars($row["pname"]) . '</td>';
            echo '<td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($row["stock"]) . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>
</div>

<!--     
    <div class="chart" id="chartContainer2">
        <canvas id="myChart2" width="100%" height="60%"></canvas>
    </div> -->

    <div class="chart" id="chartContainer3">
        <canvas id="myChart3" width="100%" height="60%"></canvas>
    </div>

    <div class="chart" id="chartContainer4">
        <canvas id="myChart4" width="100%" height="60%"></canvas>
    </div>

    <div class="chart" id="chartContainer5">
        <canvas id="myChart5" width="100%" height="60%"></canvas>
    </div>

    <div class="chart" id="chartContainer6">
        <canvas id="myChart6" width="100%" height="60%"></canvas>
    </div>

    <div class="chart" id="chartContainer7">
        <canvas id="myChart7" width="100%" height="60%"></canvas>
    </div>


</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    // Prepare data for Chart.js
    const data = <?php echo json_encode($data); ?>;


  // Chart 3
const ctx3 = document.getElementById('myChart3').getContext('2d');
const myChart3 = new Chart(ctx3, {
    type: 'doughnut',
    data: {
        labels: ['Plants', 'Soils', 'Pots', 'Fertilizers'],
        datasets: [{
            label: 'Category stock',
            data: [
                data.plants,
                data.soils,
                data.pots,
                data.fertilizers
            ],
            backgroundColor: [
                'rgba(60, 179, 113, 0.3)',
                'rgba(153, 102, 255, 0.3)',
                'rgba(255, 206, 86, 0.3)',
                'rgba(75, 192, 192, 0.3)'
            ],
            borderColor: [
                'rgba(60, 179, 113, 0.3)',
                'rgba(153, 102, 255, 0.3)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 1,
            borderRadius: 10 
        }]
    },
    options: {
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            datalabels: {
                color: 'black',
                font: {
                    weight: 'bold'
                },
                formatter: (value, context) => {
                    return value;
                },
                outside: true, // Position labels outside the doughnut chart
            }
        },
    },
    plugins: [ChartDataLabels] // Include the plugin
});



// Chart 4
const ctx4 = document.getElementById('myChart4').getContext('2d');
const myChart4 = new Chart(ctx4, {
    type: 'bar', 
    data: {
        labels: <?php echo json_encode($data['plant_names']); ?>, 
        datasets: [{
            label: 'Plant Stock', 
            data: <?php echo json_encode($data['plant_stock']); ?>,
            backgroundColor: 'rgba(60, 179, 113, 0.3)',
            borderColor: 'rgba(60, 179, 113, 1)', 
            borderWidth: 1,
        }]
    },
    options: {
        indexAxis: 'y', // This ensures the bars are vertical
        scales: {
            x: {
                beginAtZero: true,
               
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            datalabels: {
                display: true,
                anchor: 'end',
                align: 'end',
                color: 'black',
                font: {
                    weight: 'bold'
                },
                formatter: (value, context) => {
                    return value;
                }
            }
        },
        layout: {
            padding: {
                left: 20,
                right: 20,
                top: 20,
                bottom: 20
            }
        },
        responsive: true, // Make the chart responsive
        maintainAspectRatio: false, // Allow chart to adjust its aspect ratio
    },
    plugins: [ChartDataLabels] // Include the plugin
});

// Chart 5
const ctx5 = document.getElementById('myChart5').getContext('2d');
const myChart5 = new Chart(ctx5, {
    type: 'bar', // Changed from 'doughnut' to 'bar' for displaying plant data as bars
    data: {
        labels: <?php echo json_encode($data['soil_names']); ?>, // Use plant names as labels
        datasets: [{
            label: 'Soil Stock', // Changed the label to 'Plant Stock'
            data: <?php echo json_encode($data['soil_stock']); ?>, // Use plant stock data
            backgroundColor: 'rgba(153, 102, 255, 0.3)', // Green color for bars
            borderColor: 'rgba(153, 102, 255, 0.3)', // Border color for bars
            borderWidth: 1,
        }]
    },
    options: {
        indexAxis: 'y', // This ensures the bars are vertical
        scales: {
            x: {
                beginAtZero: true,
               
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            datalabels: {
                display: true,
                anchor: 'end',
                align: 'end',
                color: 'black',
                font: {
                    weight: 'bold'
                },
                formatter: (value, context) => {
                    return value;
                }
            }
        },
        layout: {
            padding: {
                left: 20,
                right: 20,
                top: 20,
                bottom: 20
            }
        },
        responsive: true, // Make the chart responsive
        maintainAspectRatio: false, // Allow chart to adjust its aspect ratio
    },
    plugins: [ChartDataLabels] // Include the plugin
});

//Chart 6
const ctx6 = document.getElementById('myChart6').getContext('2d');
const myChart6 = new Chart(ctx6, {
    type: 'bar', // Changed from 'doughnut' to 'bar' for displaying plant data as bars
    data: {
        labels: <?php echo json_encode($data['pot_names']); ?>, // Use plant names as labels
        datasets: [{
            label: 'Pots Stock', // Changed the label to 'Plant Stock'
            data: <?php echo json_encode($data['pot_stock']); ?>, // Use plant stock data
            backgroundColor: 'rgba(255, 206, 86, 0.3)', // Green color for bars
            borderColor: 'rgba(255, 206, 86, 0.3)', // Border color for bars
            borderWidth: 1,
        }]
    },
    options: {
        indexAxis: 'y', // This ensures the bars are vertical
        scales: {
            x: {
                beginAtZero: true,
             
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            datalabels: {
                display: true,
                anchor: 'end',
                align: 'end',
                color: 'black',
                font: {
                    weight: 'bold'
                },
                formatter: (value, context) => {
                    return value;
                }
            }
        },
        layout: {
            padding: {
                left: 20,
                right: 20,
                top: 20,
                bottom: 20
            }
        },
        responsive: true, // Make the chart responsive
        maintainAspectRatio: false, // Allow chart to adjust its aspect ratio
    },
    plugins: [ChartDataLabels] // Include the plugin
});

//Chart 7
const ctx7 = document.getElementById('myChart7').getContext('2d');
const myChart7 = new Chart(ctx7, {
    type: 'bar', // Changed from 'doughnut' to 'bar' for displaying plant data as bars
    data: {
        labels: <?php echo json_encode($data['fertilizer_names']); ?>, // Use plant names as labels
        datasets: [{
            label: 'Fertilizer Stock', // Changed the label to 'Plant Stock'
            data: <?php echo json_encode($data['fertilizer_stock']); ?>, // Use plant stock data
            backgroundColor: 'rgba(75, 192, 192, 0.3)', // Green color for bars
            borderColor: 'rgba(75, 192, 192, 0.3)', // Border color for bars
            borderWidth: 1,
        }]
    },
    options: {
        indexAxis: 'y', // This ensures the bars are vertical
        scales: {
            x: {
                beginAtZero: true,
             
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            datalabels: {
                display: true,
                anchor: 'end',
                align: 'end',
                color: 'black',
                font: {
                    weight: 'bold'
                },
                formatter: (value, context) => {
                    return value;
                }
            }
        },
        layout: {
            padding: {
                left: 20,
                right: 20,
                top: 20,
                bottom: 20
            }
        },
        responsive: true, // Make the chart responsive
        maintainAspectRatio: false, // Allow chart to adjust its aspect ratio
    },
    plugins: [ChartDataLabels] // Include the plugin
});

</script>




</section>
<!-- box container -->
<script>
      $(document).ready(function(){
         // Function to fetch counts via AJAX
         function fetchCounts() {
            $.ajax({
               url: 'dashboard.php',
               type: 'POST',
               data: { action: 'fetchCounts' },
               dataType: 'json',
               success: function(response) {
                  $('#userCount').text(response.users);
                  $('#productCount').text(response.products);
                  $('#orderCount').text(response.orders);
               }
            });
         }

         // Fetch counts on page load
         fetchCounts();
         setInterval(fetchCounts, 10000);

      });
   </script>

<script>
        const ctx = document.getElementById('salesChart').getContext('2d');

        let salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Sales Amount (₱)',
                    data: [], 
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(200, 200, 200, 0.1)'
                        }
                    }
                }
            }
        });
        function updateChart(startDate, endDate, sortType) {
    fetch(`sales_chart.php?startDate=${startDate}&endDate=${endDate}&sortType=${sortType}`)
        .then(response => response.json())
        .then(data => {
            salesChart.data.labels = data.labels;
            salesChart.data.datasets[0].data = data.sales;
            salesChart.update();
        });
}
        function updateChart() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const sortType = document.getElementById('sortType').value;

            fetch(`sales_chart.php?startDate=${startDate}&endDate=${endDate}&sortType=${sortType}`)
                .then(response => response.json())
                .then(data => {
                    salesChart.data.labels = data.dates || data.labels || [];
                    salesChart.data.datasets[0].data = data.sales || [];
                    salesChart.update();
                });
        }

        document.getElementById('filterButton').addEventListener('click', updateChart);

        updateChart();
    </script>

<script>
 $(document).ready(function () {
        // Function to load and update the dashboard content
        function loadDashboard() {
            $.ajax({
                url: 'load_dashboard.php',
                type: 'GET',
                success: function (response) {
                    $('#myChart').html(response);
                },
                error: function (xhr, status, error) {
                    console.error('Error loading dashboard:', error);
                }
            });
        }

        // Initial load
        loadDashboard();

        // Set interval to refresh every 10 seconds (adjust as needed)
        setInterval(loadDashboard, 5000);
    });
   
         
</script>

<!-- JavaScript to handle the threshold update and auto-refresh the product list -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // When the threshold value is changed
        $('#thresholdValue').on('blur', function() {
            var newThreshold = $(this).text().trim();

            // Send the new threshold to the server using AJAX
            $.ajax({
                url: 'update_and_fetch.php', // Single PHP file for both update and fetch
                method: 'POST',
                data: { threshold: newThreshold },
                success: function(data) {
                    $('#saveStatus').text('Threshold updated successfully!').fadeIn().delay(2000).fadeOut();

                    // Update the product table with the new data
                    $('#productData').html(data);
                },
                error: function() {
                    $('#saveStatus').text('Error updating threshold!').css('color', 'red').fadeIn().delay(2000).fadeOut();
                }
            });
        });
    });
</script>


<script>
let sidebar = document.querySelector(".sidebar");
let sidebarBtn = document.querySelector(".sidebarBtn");
sidebarBtn.onclick = function() {
  sidebar.classList.toggle("active");
  if(sidebar.classList.contains("active")){
    sidebarBtn.classList.replace("bx-menu" ,"bx-menu-alt-right");
  }else
  sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu")
};
</script>

</body>
</html>
