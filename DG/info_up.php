<?php


include 'connection.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_POST['update_profile'])){

   $Fname = $_POST['Fname'];
   $Fname = filter_var($Fname, FILTER_SANITIZE_STRING);

   $Mname = $_POST['Mname'];
   $Mname = filter_var($Mname, FILTER_SANITIZE_STRING);

   $Lname = $_POST['Lname'];
   $Lname = filter_var($Lname, FILTER_SANITIZE_STRING);

   $contact = $_POST['contact'];

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   $update_profile = $conn->prepare("UPDATE `users` SET Fname = ?, Mname = ?, Lname = ?,  contact = ?, email = ? WHERE id = ?");
   $update_profile->execute([$Fname,$Mname, $Lname, $contact, $email, $admin_id]);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'img/'.$image;
   $old_image = $_POST['old_image'];

   if(!empty($image)){
      if($image_size > 200000000){
         $message[] = 'image size is too large!';
      }else{
         $update_image = $conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
         $update_image->execute([$image, $admin_id]);
         if($update_image){
            move_uploaded_file($image_tmp_name, $image_folder);
            unlink('img/'.$old_image);
            $message[] = 'image updated successfully!';
         };
      };
   };

    // Update password if new password is provided
    $old_pass = $_POST['old_pass'];
    $update_pass = $_POST['update_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    if (!empty($update_pass) && !empty($new_pass) && !empty($confirm_pass)) {
        if ($update_pass !== $old_pass) {
            $message[] = 'Old password does not match!';
        } elseif ($new_pass !== $confirm_pass) {
            $message[] = 'New password and confirm password do not match!';
        } elseif (strlen($new_pass) < 8 || !preg_match("#[A-Z]+#", $new_pass) || !preg_match("#[a-z]+#", $new_pass) || !preg_match("#[0-9]+#", $new_pass) || !preg_match("/[\'^£$%&*()}{@#~?><>,|=_+!-]/", $new_pass)) {
            $message[] = 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.';
        } else {
            // Update the password in the database (without hashing)
            $update_pass_query = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            if ($update_pass_query->execute([$new_pass, $user_id])) {
                $message[] = 'Password updated successfully!';
            } else {
                $message[] = 'Error updating password in the database.';
            }
        }
    } else {
        $message[] = 'Please fill in all password fields.';
    }
}

?>

<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <title> Dulay's Profile Update </title>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <!-- Boxicons CDN Link -->
 <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
  <script src="https://unpkg.com/boxicons@2.0.9/dist/boxicons.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!---cdn jquery-->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- font awesome cdn link  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<!-- custom css file link  -->
<link rel="stylesheet" href="style/dashboard.css">
<link rel="stylesheet" href="style/style.css">
<link rel="stylesheet" href="style/style1.css">
<link rel="stylesheet" href="style/table-user.css">

  
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
      /*----update-profile-----*/    

      .update-profile{
         padding-top: 100px;
      }
.update-profile form{
   max-width: 70rem;
   margin: 0 auto;
   background-color: var(--white);
   box-shadow: var(--box-shadow);
   border:var(--border);
   border-radius: .5rem;
   padding:2rem;
   text-align: center;
}

.update-profile form .flex{
   display: flex;
   gap:1.5rem;
   justify-content: space-between;
}

.update-profile form .pro_img{
   height: 20rem;
   width: 20rem;
   margin-bottom: 1rem;
   border-radius: 50%;
   object-fit: cover;
}

.update-profile form .inputBox{
   text-align: left;
   width: 49%;
}

.update-profile form .inputBox span{
   display: block;
   padding-top: 1rem;
   font-size: 1.8rem;
   color:var(--light-color);
}


.update-profile form .inputBox .box{
   width: 100%;
   padding:1.2rem 1.4rem;
   font-size: 1.8rem;
   color:var(--black);
   border:var(--border);
   border-radius: .5rem;
   margin:1rem 0;
   background-color: rgb(237 200 156);
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
      <span class="dashboard">Profile Update </span>
    </div>

    <div class="profile-details">
      <!-- Profile Details -->
      <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="img/<?= $fetch_profile['image']; ?>" alt="">
         <p style="   margin-left: 50px; font-size: 20px;"><?= $fetch_profile['Fname']; ?></p>
    </div>
  </nav>
  
<section class="update-profile">

<h1 class="title" style="padding-bottom: 10px;">Update Profile</h1>

<form action="" method="POST" enctype="multipart/form-data">
<?php
// Display validation or success messages
if (!empty($message)) {
    echo '<div class="alert alert-info">';
    foreach ($message as $msg) {
        echo '<p>' . $msg . '</p>';
    }
    echo '</div>';
}
?>
   <img src="img/<?= $fetch_profile['image']; ?>" alt="" class="pro_img">
   <div class="flex">
      <div class="inputBox">
         <span>FirstName:</span>
         <input type="text" name="Fname" value="<?= $fetch_profile['Fname']; ?>" placeholder="update first" required class="box">
         <span>MiddleName</span>
         <input type="text" name="Mname" value="<?= $fetch_profile['Mname']; ?>" placeholder="update middle" required class="box">
         <span>LastName</span>
         <input type="text" name="Lname" value="<?= $fetch_profile['Lname']; ?>" placeholder="update last" required class="box">
         <span>Mobile Number</span>
         <input type="tel" name="contact" value="<?= $fetch_profile['contact']; ?>" placeholder="update mobile" required class="box">
         <span>Email :</span>
         <input type="email" name="email" value="<?= $fetch_profile['email']; ?>" placeholder="update email" required class="box">
      </div>
      <div class="inputBox">
         <input type="hidden" name="old_pass" value="<?= $fetch_profile['password']; ?>">
         <span>Old password :</span>
         <input type="password" name="update_pass" placeholder="enter previous password" class="box">
         <span>New Password :</span>
         <input type="password" name="new_pass" placeholder="enter new password" class="box">
         <span>Confirm Password :</span>
         <input type="password" name="confirm_pass" placeholder="confirm new password" class="box">
         <span>Update picture :</span>
         <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
         <input type="hidden" name="old_image" value="<?= $fetch_profile['image']; ?>">
      </div>
   </div>
   <div class="flex-btn">
      <input type="submit" class="btn" value="update profile" name="update_profile">
      <a href="dashboard.php" class="option-btn" style="text-decoration: none;">Go back</a>
   </div>
</form>

</section>

</section>

<script src="js/script.js"></script>

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
