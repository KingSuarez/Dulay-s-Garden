<?php

include 'connection.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_POST['add_product'])){

  $pname = $_POST['pname'];
  $pname = filter_var($pname, FILTER_SANITIZE_STRING);

  $price = $_POST['price'];
  $price = filter_var($price, FILTER_SANITIZE_STRING);

  $category = $_POST['category'];
  $category = filter_var($category, FILTER_SANITIZE_STRING);

  $detail = $_POST['detail'];
  $detail = filter_var($detail, FILTER_SANITIZE_STRING);
  
  $careP = $_POST['careP'];
  $careP = filter_var($careP, FILTER_SANITIZE_STRING);


  $stock = $_POST['stock'];
  $stock = filter_var($stock, FILTER_SANITIZE_STRING);

  $image = $_FILES['image']['name'];
  $image = filter_var($image, FILTER_SANITIZE_STRING);
  $image_size = $_FILES['image']['size'];
  $image_tmp_name = $_FILES['image']['tmp_name'];
  $image_folder = 'prod/'.$image;

  $image2 = $_FILES['image2']['name'];
  $image2 = filter_var($image2, FILTER_SANITIZE_STRING);
  $image2_tmp_name = $_FILES['image2']['tmp_name'];
  $image2_folder = 'prod/'.$image2;

  $image3 = $_FILES['image3']['name'];
  $image3 = filter_var($image3, FILTER_SANITIZE_STRING);
  $image3_tmp_name = $_FILES['image3']['tmp_name'];
  $image3_folder = 'prod/'.$image3;

  $select_products = $conn->prepare("SELECT * FROM `products` WHERE pname = ?");
  $select_products->execute([$pname]);

  if($select_products->rowCount() > 0){
     $message[] = 'product name already exist!';
  } else {
     $insert_products = $conn->prepare("INSERT INTO `products`(pname, category, stock, detail, careP, price, image, image2, image3) VALUES(?,?,?,?,?,?,?,?,?)");
     $insert_products->execute([$pname, $category, $stock, $detail, $careP, $price, $image, $image2, $image3]);

     if($insert_products){
        if($image_size > 500000000){
           $message[] = 'image size is too large!';
        } else {
           move_uploaded_file($image_tmp_name, $image_folder);
           if($image2) {
              move_uploaded_file($image2_tmp_name, $image2_folder);
           }
           if($image3) {
              move_uploaded_file($image3_tmp_name, $image3_folder);
           }
           $message[] = 'new product added!';
        }
      //   if (!empty($_POST['category'])) {
      //     $category = $_POST['category'];
      //     // Process the category (e.g., save to database, etc.)
      //     echo "Selected category: " . htmlspecialchars($category);
      // } else {
      //     // Handle the error if the category is not selected
      //     echo "<p style='color: red;'>Please select a category.</p>";
      // }
     }
  }
  header('location:product.php');
};


?>

<html lang="en" dir="ltr">
<head>
  
  <meta charset="UTF-8">
  <title> Add Product </title>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!--bootstap cdn link--->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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

  <!-- custom css file link  -->
  <link rel="stylesheet" href="style/dashboard.css">
  <link rel="stylesheet" href="style/style.css">
  <link rel="stylesheet" href="style/style1.css">
  <link rel="stylesheet" href="style/table-product.css">

  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /*---------------------add product----------------------- */
    .alist{
        padding: 80px 10px;

    }
    .add-products{
        padding: 80px 10px;

    }
        .add-products form{
        max-width: 70rem;
        padding:2rem;
        margin:0 auto;
        text-align: center;
        box-shadow: var(--box-shadow);
        background-color: #79b967;
        border-radius: .5rem;
        }

        .add-products form .flex{
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        }

        .add-products form .flex .inputBox{
        width: 49%;
        }

        .add-products form .box{
        width: 100%;
        margin:1rem 0;
        padding:1.2rem 1.4rem;
        font-size: 1.3rem;
        border-radius: .5rem;
        background-color:rgb(233 213 191);
        border:var(--border);
        }

        .add-products form textarea{
        height: 20rem;
        resize: none;
        }
      
        .stockBox{
          width: 100%;
        }
      .stockBox .stock{
      width: 50%;
      padding: 1.2rem 1.4rem;
        font-size: 1.8rem;
        border-radius: .5rem;
        background-color: rgb(233 213 191);
        border:var(--border);
      text-align: center;
      
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
      <i class='bx bx-box'></i>
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
        <i class='bx bxs-user-account'></i>
        <span class="links_name">Users-Info</span>
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
      <span class="dashboard">Add Product</span>
    </div>

    <div class="profile-details">
      <!-- Profile Details -->
      <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="img/<?=$fetch_profile['image']; ?>" alt="">
         <p style="margin-left: 50px; font-size: 20px;"><?=$fetch_profile['Fname']; ?></p>
    </div>
  </nav>
  
  <section class="add-products">

<h1 class="title">Add New Product</h1>

<form action="" method="POST" enctype="multipart/form-data">
   <div class="flex">
      <div class="inputBox">
         <input type="text" name="pname" class="box" required placeholder="Enter Product Name">

         <select name="category" class="box" required>
            <option value="" selected disabled>Select Category</option>
            <option value="Plant">Plant</option>
            <option value="Soil">Soil</option>
            <option value="Fertilizer">Fertilizer</option>
            <option value="Pot">Pot</option>
         </select>
         <input type="file" name="image2" required class="box" accept="image/jpg, image/jpeg, image/png, video/mp4">

      </div>

      <div class="inputBox">
         <input type="number" min="0"  name="price" class="box" id="price" required placeholder="Enter Product Price">
         <input type="file" name="image" required class="box" accept="image/jpg, image/jpeg, image/png, video/mp4">
         <input type="file" name="image3"required class="box" accept="image/jpg, image/jpeg, image/png, video/mp4">
      </div>

      <div class="stockBox">
         <input type="number" name="stock" required placeholder="Enter Stock" class="stock">
      </div>
   </div>
   
   <textarea type="text" name="detail" class="box"  maxlength="500" required placeholder="Enter Product Details" onInput="handleInput(event)" cols="30" rows="10"></textarea>
   <textarea type="text" name="careP" class="box" maxlength="500" required placeholder="Enter Care Product" onInput="handleInput(event)" cols="30" rows="10"></textarea>
   <input type="submit" class="btn" value="add product" name="add_product" style="background-color: rgb(208 173 91);">
</form>


</section>
<!-- 
<section class="alist">
<h1 class="title" style="padding: 1rem ;"> Products</h1>
<div class="table-selection">

      <table>
         <thead>
         <tr>
            <th>No.</th>
            <th>Product</th>
            <th>Name</th>
            <th>Price </th>
            <th>Stock</th>
            <th>Category</th>
            <th>Details</th>


         </tr>
      </thead>
      <?php
$show_products = $conn->prepare("SELECT * FROM `products` ORDER BY `id` DESC");
$show_products->execute();
$number = 1;

if ($show_products->rowCount() > 0) {
    while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <tr>
            <td><?= $number ?></td>
            <td>
                <img src="prod/<?= htmlspecialchars($fetch_products['image']); ?>" alt="Product Image">
                <?php if (!empty($fetch_products['image2'])) { ?>
                    <img src="prod/<?= htmlspecialchars($fetch_products['image2']); ?>" alt="Product Image 2">
                <?php } ?>
                <?php if (!empty($fetch_products['image3'])) { ?>
                    <img src="prod/<?= htmlspecialchars($fetch_products['image3']); ?>" alt="Product Image 3">
                <?php } ?>
            </td>
            <td><?= htmlspecialchars($fetch_products['pname']) ?></td>
            <td>₱<?= htmlspecialchars($fetch_products['price']) ?></td>
            <td><?= htmlspecialchars($fetch_products['stock']) ?></td>
            <td><?= htmlspecialchars($fetch_products['category']) ?></td>
            <td><?= htmlspecialchars($fetch_products['detail']) ?></td>
        </tr>
        <?php   
        $number++;
    }
} else {
    echo '<p class="empty">No products found!</p>';
}
?>
      </table>

      
   </div>
</section> -->

</section>

<script src="js/script.js"></script>

<script>
  let previousLength = 0;

const handleInput = (event) => {
  const bullet = "\u2022";
  const newLength = event.target.value.length;
  const characterCode = event.target.value.substr(-1).charCodeAt(0);

  if (newLength > previousLength) {
    if (characterCode === 10) {
      event.target.value = `${event.target.value}${bullet} `;
    } else if (newLength === 1) {
      event.target.value = `${bullet} ${event.target.value}`;
    }
  }
  
  previousLength = newLength;
}
</script>

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

</body>
</html>
