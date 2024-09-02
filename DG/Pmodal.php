<?php

include 'connection.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_POST['UpdateData'])){

  $products_id = $_GET['id'];  

   $pname = $_POST['pname'];
   $pname = filter_var($pname, FILTER_SANITIZE_STRING);
   
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
  //  if (isset($_POST['category'])) {
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
// } else {
    // Handle the case where 'category' is not set in $_POST
//     $category = ''; // or set a default value
// }
   
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
   $old_image = $_POST['old_image'];

   
  $image2 = $_FILES['image2']['name'];
  $image2 = filter_var($image2, FILTER_SANITIZE_STRING);
  $image2_tmp_name = $_FILES['image2']['tmp_name'];
  $image2_folder = 'prod/'.$image2;
  $old_image2 = $_POST['old_image2'];

  $image3 = $_FILES['image3']['name'];
  $image3 = filter_var($image3, FILTER_SANITIZE_STRING);
  $image3_tmp_name = $_FILES['image3']['tmp_name'];
  $image3_folder = 'prod/'.$image3;
  $old_image3 = $_POST['old_image3'];

   $Update_product = $conn->prepare("UPDATE `products` Set pname = ?, price = ?, category = ?, detail = ?, careP = ?, stock = ? WHERE id = ?");
   $Update_product->execute([$pname, $price, $category, $detail, $careP, $stock, $products_id]);
   
   $message[] = 'product updated successfully!';
   
   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'image size is too large!';
      }else{
   
         $Update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
         $Update_image->execute([$image, $products_id]);
   
         if($Update_image){
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('prod/'.$old_image);
            $message[] = 'image updated successfully!';
         }
      }
   }
   
if(!empty($image2)){
  if($_FILES['image2']['size'] > 2000000){
      $message[] = 'image size is too large!';
  } else {
      $Update_image2 = $conn->prepare("UPDATE `products` SET image2 = ? WHERE id = ?");
      $Update_image2->execute([$image2, $products_id]);
      if($Update_image2){
          move_uploaded_file($image2_tmp_name, $image2_folder);
          if (file_exists('prod/'.$old_image2)) {
              unlink('prod/'.$old_image2);
          }
          $message[] = 'image 2 updated successfully!';
      }
  }
}

if(!empty($image3)){
  if($_FILES['image3']['size'] > 2000000){
      $message[] = 'image size is too large!';
  } else {
      $Update_image3 = $conn->prepare("UPDATE `products` SET image3 = ? WHERE id = ?");
      $Update_image3->execute([$image3, $products_id]);
      if($Update_image3){
          move_uploaded_file($image3_tmp_name, $image3_folder);
          if (file_exists('prod/'.$old_image3)) {
              unlink('prod/'.$old_image3);
          }
          $message[] = 'image 3 updated successfully!';
      }
  }
}
header('location:product.php');

   }
// if(isset($_POST['Update_product'])){
//   $product_id = $_GET['id'];

//   $pname = $_POST['pname'];
//   $pname = filter_var($pname, FILTER_SANITIZE_STRING);

//   $price = $_POST['price'];
//   $price = filter_var($price, FILTER_SANITIZE_STRING);

//   $category = $_POST['category'];
//   $category = filter_var($category, FILTER_SANITIZE_STRING);

//   $detail = $_POST['detail'];
//   $detail = filter_var($detail, FILTER_SANITIZE_STRING);

//   $stock = $_POST['stock'];
//   $stock = filter_var($stock, FILTER_SANITIZE_STRING);

//   $image = $_FILES['image']['name'];
//   $image = filter_var($image, FILTER_SANITIZE_STRING);
//   $image_size = $_FILES['image']['size'];
//   $image_tmp_name = $_FILES['image']['tmp_name'];
//   $image_folder = 'prod/'.$image;
//   $old_image = $_POST['old_image'];

//   $Update_product = $conn->prepare("UPDATE `products` SET pname = ?, price = ?, category = ?, detail = ?, stock = ? WHERE id = ?");
//   $Update_product->execute([$pname, $price, $category, $detail, $stock, $product_id]);

//   $message[] = 'product updated successfully!';

//   if(!empty($image)){
//      if($image_size > 2000000){
//         $message[] = 'image size is too large!';
//      }else{

//         $Update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
//         $Update_image->execute([$image, $product_id]);

//         if($Update_image){
//            move_uploaded_file($image_tmp_name, $image_folder);
//            unlink('prod/'.$old_image);
//            $message[] = 'image updated successfully!';
//         }
//      }
//   }

// }

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
    font-size: 1.3rem;
    border-radius: .5rem;
    background-color: rgb(233 213 191);
    border:var(--border);
   text-align: center;
   
   }
   .prod_img{
    height: 180px;
    width: 180px;
    object-fit: cover;
    border-radius: 5px;
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
      <span class="dashboard">Profile Update</span>
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

<h1 class="title">Update Product</h1>
 
<?php
          $post_id = $_GET['id'];
          
            $show_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
            $show_products->execute([$post_id]);
            if ($show_products->rowCount() > 0) {
              while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {

        ?>
<form action="" method="POST" enctype="multipart/form-data" >
   <input type="hidden" name="product_id" value="<?= $fetch_products['id']; ?>">
   <img src="prod/<?= $fetch_products['image']; ?>" alt="" class="prod_img" width="25px" height="15px">
   <img src="prod/<?= $fetch_products['image2']; ?>" alt="" class="prod_img" width="25px" height="15px">
   <img src="prod/<?= $fetch_products['image3']; ?>" alt="" class="prod_img" width="25px" height="15px">


   <div class="flex">
    <input type="hidden" name="product_id" value="<?$fetch_products['id']?>">
      <div class="inputBox" >
      <input type="text" name="pname" class="box" value="<?=$fetch_products['pname']; ?>" required placeholder="enter product name">

      <select name="category" class="box" value="<?=$fetch_products['category']; ?>" required>
         <option selected disabled> <?=$fetch_products['category']; ?></option>
            <option value="Plant"<?php if ($fetch_products['category'] == 'Plant') echo 'selected'; ?>>Plant</option>
            <option value="Soil"<?php if ($fetch_products['category'] == 'Soil') echo 'selected'; ?>>Soil</option>
            <option value="Fertilizer"<?php if ($fetch_products['category'] == 'Fertilizer') echo 'selected'; ?>>Fertilizer</option>
            <option value="Pot"<?php if ($fetch_products['category'] == 'Pot') echo 'selected'; ?>>Pot</option>
      </select>
      <input type="file" name="image2"  class="box" accept="image/jpg, image/jpeg, image/png, video/mp4">
      <input type="hidden" name="old_image2" value="<?= $fetch_products['image2']; ?>">

      </div> 


      <div class="inputBox">
      <input type="number" min="0" name="price" class="box" id="price" value="<?=$fetch_products['price'];?>" required placeholder="enter product price">
      <input type="file" name="image"  class="box" accept="image/jpg, image/jpeg, image/png, video/mp4">
      <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">

      <input type="file" name="image3"  class="box" accept="image/jpg, image/jpeg, image/png, video/mp4">
      <input type="hidden" name="old_image3" value="<?= $fetch_products['image3']; ?>">



      
      </div>

      <div class="stockBox">
          <input type="number" name="stock" required  placeholder="enter stock" class="stock" maxlength="5" value="<?=$fetch_products['stock'];?>">
      </div>

   </div>
   
   <textarea type="text" name="detail" class="box" maxlength="500" required placeholder="enter product details" onInput="handleInput(event)" cols="30" rows="10" value="<?= $fetch_products['detail'];?>" ><?=$fetch_products['detail'];?></textarea>
   <textarea type="text" name="careP" class="box" maxlength="500" required placeholder="Enter Care Product" onInput="handleInput(event)" cols="30" rows="10" value="<?= $fetch_products['careP'];?>" ><?=$fetch_products['careP'];?></textarea>
   <a><input type="submit" class="btn" value="Update " name="UpdateData" style="background-color:  rgb(208 173 91);"></a>
</form>
<?php
                  }
                } else {
                    echo '<p class="empty"> No Found Product!</p>';
                }
                ?>

</section>

</section>

<script src="js/script.js"></script>

<script>
  // bullet for textarea
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
