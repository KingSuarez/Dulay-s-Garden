<?php

include 'connection.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

// THIS IS INSERTING PRODUCT TO ARCHIVE!!!!!!!!!!!!!!!
// if(isset($_GET['delete'])){

//   $delete_id = $_GET['delete'];
  
//   // Fetch the product details before deleting
//   $fetch_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
//   $fetch_product->execute([$delete_id]);
//   $product = $fetch_product->fetch(PDO::FETCH_ASSOC);
  
//   if ($product) {
//       // Archive the product
//       $archive_product = $conn->prepare("INSERT INTO `archived_products` (pname, price, stock, category, image) VALUES (?, ?, ?, ?, ?)");
//       $archive_product->execute([
//           $product['pname'],
//           $product['price'],
//           $product['stock'],
//           $product['category'],
//           $product['image']
//       ]);
      
//       // Delete the product
//       $delete_products = $conn->prepare("DELETE FROM `products` WHERE id = ?");
//       $delete_products->execute([$delete_id]);
//   }

//   header('location:product.php');
// }

if(isset($_GET['delete'])){

    $delete_id = $_GET['delete'];
    $delete_products = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    $delete_products->execute([$delete_id]);
 
    header('location:product.php');
 }


 $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';




?>

<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <title> Dulay's Garden Panel </title>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- jquery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include DataTables CSS and JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>


  <!-- Boxicons CDN Link -->
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
  <script src="https://unpkg.com/boxicons@2.0.9/dist/boxicons.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script
  src="https://code.jquery.com/jquery-3.7.1.min.js"
  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
  crossorigin="anonymous"></script>

<!-- font awesome cdn link  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<!-- custom css file link  -->
<link rel="stylesheet" href="style/dashboard.css">
<link rel="stylesheet" href="style/style.css">
<link rel="stylesheet" href="style/style1.css">
<link rel="stylesheet" href="style/table-product.css">
<link rel="stylesheet" href="style/update-product.css">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- css link in bootstap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" >
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>


  <style>

    #filter {
  margin: 10px; /* Adjust margin as needed */
  display: flex;
}

.box {
  padding: 10px;
  font-size: 18px;
  border: 1px solid #ccc;
  border-radius: 5px;
  background-color: #fff;
  color: #333;
  cursor: pointer;
  outline: none;
  width: 200px; /* Set the width as needed */
}

/* Style for the dropdown arrow */
.box::after {
  content: '\25BC'; /* Unicode character for a downward arrow */
  position: absolute;
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
}

/* Style for the selected option */
.box option[selected] {
  font-weight: bold;
  color: #555;
}

   
   .selection-container{
    padding: 80px 10px;
    

}
    button{
        outline: none;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        padding: 8px;
        color: #ffffff;
    }

 
    .table-head .aproduct-btn #filter{
      justify-content: space-between;
    }



    .table-head{
        width: 100%;
        display: flex;
        align-items: center;
        padding: 20px;
        
    }

    .aproduct-btn{
        border-radius: 8px;
        outline: none;
        padding: 10px 20px;
        font-size: 20px;
        background-color: #79AC78;
        color: white;
        text-align: center;
        text-decoration: none;
    }
    /* .search{
        font-size: 18px;
        background-color: #ffffff;
        border-radius: 7px;
        padding-right: 40px;
        
      }
    .search  .ser{
      justify-items: center;
      padding: 8px 5px ;
        text-align:justify;
        padding-left: 10px;
        border: none;
        background-color: #ffffff;
        
    }
    .search .ar{
        margin-left: 4px;
    } */

     input[type="text"]{
      padding: 8px 15px;
    border-radius: 20px;
    border: 2px solid #966E44;
    font-size: 16px;
    width: 150px;
    margin-right: 5px;
    color: #2c1205;
     }
     
    
    /* ----------modal--------- */
    .modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
  background-color: #fefefe;
  margin: 10% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}

  </style>

</head>


<body style="font-size: 14px;">

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
      <span class="dashboard" >Product</span>
    </div>

    <div class="profile-details">
      <!-- Profile Details -->
      <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="img/<?= $fetch_profile['image']; ?>" alt="">
         <p style=" margin-left: 50px; font-size: 20px; margin-top:10px;"><?= $fetch_profile['Fname']; ?></p>
    </div>
  </nav>
  


<section class="selection-container" >

        <h1 class="title" style="padding: 1rem; font-family:Poppins, sans-serif; font-weight: bold;"> Products</h1>
        
        <div class="table-head">
            <a href="aproduct.php" class="aproduct-btn" >Add Product</a>
            
              <div id="filter" class="cat">
                <select name="category" class="box" id="category">
                <option selected disabled>Select Category</option>
                <option value="Plant">Plant</option>
                <option value="Soil">Soil</option>
                <option value="Fertilizer">Fertilizer</option>
                <option value="Pot">Pot</option>
                </select>
              </div>
          <div style="text-align:right;">
              <form id="searchForm" method="GET" action="product.php">
            <input type="text" name="search" style="width: 210px;" placeholder="Search Product" value="<?= htmlspecialchars($search_query) ?>">
            <button type="submit" style="margin-right: 10px; padding: 10px; background-color: #914F1E; color: white; border: none; border-radius: 5px; cursor: pointer;">Search</button>
        </form>
        </div>
        </div>


        <table  id="table-container" class="display" style="width:100%;">
       
          <thead>
          <tr style="text-align: center;">
              <th>Product</th>
              <th>Name</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Category</th>
              <th>Action</th>
          </tr>
         </thead>
         <tbody>

         <?php
             $sql = "SELECT * FROM `products`";
             if (!empty($search_query)) {
                 $sql .= " WHERE pname LIKE :search OR price LIKE :search";
             }
             $sql .= " ORDER BY id DESC";
     
             $show_products = $conn->prepare($sql);
             if (!empty($search_query)) {
                 $search_term = '%' . $search_query . '%';
                 $show_products->bindParam(':search', $search_term, PDO::PARAM_STR);
             }
             $show_products->execute();
        if ($show_products->rowCount() > 0) {
        while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <tr onclick="openEditModal(<?= $fetch_products['id']; ?>)">
                <td><img src="prod/<?= $fetch_products['image']; ?>" alt="Product Image"></td>
                <td><?= $fetch_products['pname'] ?></td>
                <td>₱<?= $fetch_products['price'] ?></td>
                <td><?= $fetch_products['stock'] ?></td>
                <td><?= $fetch_products['category'] ?></td>
                <td>
                    <a href="product.php?delete=<?= $fetch_products['id']; ?>" onclick="return confirm('Delete this product?');">
                        <i class="fa-solid fa-trash-can fa-lg" style="color: #D04848;"></i>
                    </a>
                    <a href="Pmodal.php?id=<?=$fetch_products['id']; ?>">
                      <i class="fa-solid fa-edit fa-lg" style="color: #7EA1FF;"></i></a>
                
                </td>
            </tr>
            <?php
        }
    } else {
        echo '<p class="empty"> No Found Product!</p>';
    }
    ?>
         <tbody>
         </table>



</section>

</section>

<script>
    $(document).ready(function () {
        $('#table-container').DataTable();
    });
</script> 
<!-- <script src="js/script.js"></script>
<script type="text/javascript">
//    function reloadDiv() {
//   // Use jQuery to load the div's content from the server
//   $('#container').reload('dashboard.php #container');
// }

// Call the reloadDiv function every 5000 milliseconds (5 seconds)
//setInterval(reloadDiv, 5000);
   
</script> --> 



<script>
let sidebar = document.querySelector(".sidebar");
let sidebarBtn = document.querySelector(".sidebarBtn");
sidebarBtn.onclick = function() {
  sidebar.classList.toggle("active");
  if(sidebar.classList.contains("active")){
    sidebarBtn.classList.replace("bx-menu" ,"bx-menu-alt-right");
  }else
  sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
}
</script>

<script>
  $(document).ready(function(){
    $("#category").on('change',function(){
      var value = $(this).val();

      $.ajax({
        url:"filter.php",
        type:"POST",
        data:'request=' + value,
        beforeSend:function(){
          $('#table-container').html("<span>Loading</span>")
        },
        success:function(data){
          $("#table-container").html(data);
        }
      });
    });
  });
</script>

<!-- <script>
   // Get the modal element
var modal = document.getElementById('editModal');

// Get the close button (x) element
var closeBtn = modal.querySelector('.close');

// Open the modal when clicking on the edit button
function openEditModal() {
  modal.style.display = 'block';
}

// Close the modal when clicking on the close button or outside the modal
window.onclick = function(event) {
  if (event.target === modal) {
    modal.style.display = 'none';
  }
};

// Close the modal when clicking on the close button (x)
closeBtn.onclick = function() {
  modal.style.display = 'none';
};

// Submit the form (you can handle form submission with AJAX for updating the product)
var editForm = document.getElementById('editForm');
editForm.onsubmit = function(event) {
  event.preventDefault();
  // Handle form submission (e.g., update product details)
  // Close the modal after form submission
  modal.style.display = 'none';
};


</script> -->

  </body>
</html>
