<?php

include 'connection.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

$message = []; // Array to store validation and success messages

if(isset($_POST['update_profile'])){
    // Fetch form inputs and sanitize
    $Fname = filter_var($_POST['Fname'], FILTER_SANITIZE_STRING);
    $Mname = filter_var($_POST['Mname'], FILTER_SANITIZE_STRING);
    $Lname = filter_var($_POST['Lname'], FILTER_SANITIZE_STRING);
    $contact = $_POST['contact'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);

    // Update profile details in the database
    $update_profile = $conn->prepare("UPDATE `users` SET Fname = ?, Mname = ?, Lname = ?, contact = ?, email = ? WHERE id = ?");
    $update_profile->execute([$Fname, $Mname, $Lname, $contact, $email, $user_id]);

    // Update profile image
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'img/'.$image;
    $old_image = $_POST['old_image'];

    if (!empty($image)) {
        if ($image_size > 2000000) { // 2 MB limit
            $message[] = 'Image size is too large!';
        } else {
            $update_image = $conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
            if ($update_image->execute([$image, $user_id])) {
                if (move_uploaded_file($image_tmp_name, $image_folder)) {
                    if (file_exists('img/' . $old_image)) {
                        unlink('img/' . $old_image);
                    }
                    $message[] = 'Image updated successfully!';
                } else {
                    $message[] = 'Error moving uploaded file.';
                }
            } else {
                $message[] = 'Error updating image in the database.';
            }
        }
    }

    // Update password if new password is provided
    $old_pass = $_POST['old_pass'];
    $update_pass = filter_var($_POST['update_pass'], FILTER_SANITIZE_STRING);
    $new_pass = filter_var($_POST['new_pass'], FILTER_SANITIZE_STRING);
    $confirm_pass = filter_var($_POST['confirm_pass'], FILTER_SANITIZE_STRING);

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Style Sheet for the specific page -->
    <link rel="stylesheet" type="text/css" href="Ahomecss/Categories-Style.css">
    <!-- Style sheet for the upper part of the page that is global for all page -->
    <link rel="stylesheet" type="text/css" href="Ahomecss/All-Style.css">
<link rel="stylesheet" href="style/style1.css">
<link rel="stylesheet" href="style/dashboard.css">
    <!-- Title of the Homepage -->
    <title>Dulay's Product Shop</title>
    <style>
       input {
    transition: none;
    transform: none;

}
input[type="text"]{
    transform-origin: right;
    transform: none;
}
/* Optional: Reset hover styles */
input:hover {
    background-color: initial; /* Reset background on hover */
    border-color: initial; /* Reset border color on hover */
    /* Reset other properties as needed */
}
      .update{
         padding-top: 40px;
         
      }
.update form{
   max-width: 70rem;
   margin: 0 auto;
   background-color: #A79277;
   box-shadow: var(--box-shadow);
   border:var(--border);
   border-radius: .5rem;
   padding:2rem;
   text-align: center;
}

.update form .flex{
   display: flex;
   gap:1.5rem;
   justify-content: space-between;
}

.update form .pro_img{
   height: 20rem;
   width: 20rem;
   margin-bottom: 1rem;
   border-radius: 50%;
   object-fit: cover;
}

.update form .inputBox{
   text-align: left;
   width: 49%;
}

.update form .inputBox span{
   display: block;
   padding-top: 1rem;
   font-size: 1.8rem;
   color:var(--light-color);
}


.update form .inputBox .box{
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

 <!-- all contents are within this body Id Pagebody -->
<body>
    
<div id="PageBody">

<!-- div Class Allup contains div Classes such as Container, Homebar and HomebarBottom 1 & 2 -->
<div class="allUp">
<!-- div Class class contains div Classes such as Box1 with Id HomePanelUp  and Box2 with Id HomeProfile -->
<div class=container>
    <div class="box1" id="HomePanelUp">
    <a href="Ahome.php"><img style="margin-top: 10px;" src="Images/IMG_1210 1-1.png" width="190px"></a>
    </div>

    <div class="box2" id="HomeProfile" style="text-decoration: none;"> 
    <div class="carts">
    <li id='shoppingcart' style="list-style-type:none; font-size:large; margin-top:5px;margin-right:15px; "><a href="Acart.php" style="color: black;"><span class="glyphicon glyphicon-shopping-cart "></span>Cart</a></li>
    </div>
    <div id="Profile">
                 
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                $select_profile->execute([$user_id]);
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>
                <img  size="100px"  onclick="toggleMenu()" src="img/<?= $fetch_profile['image']; ?>" alt="img" >
            </div>
                               
            <div class="Profile-Sub-Menu-Wrap1" id="subMenu1">
            <div class="Sub-Menu1">
            <div class="user-info">
            <?php
                $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
                $select_profile->execute([$user_id]);
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>
                <img  size="100px"  onclick="toggleMenu()" src="img/<?= $fetch_profile['image']; ?>" alt="img"  >
                <h2 style=" margin-top: 5px;  font-size: 20px; font-weight:300;"><?=$fetch_profile['Fname'],'  ', $fetch_profile['Mname'], '  ', $fetch_profile['Lname'] ; ?></h2>    
                                </div>
                                <hr>
                            <a href="A_user_up.php" class="Sub-Menu-Link1" >
                                <img src="Homepage/Images/Profile.png" alt="">
                                <p>Edit Profile</p>
                                <span>></span>
                            </a>

                            <a href="ureserve.php" class="Sub-Menu-Link1" >
                                <img src="Homepage/Images/Profile.png" alt="">
                                <p>Reserve Order</p>
                                <span>></span>
                            </a>

                            <a href="logout.php" class="Sub-Menu-Link1" >
                                <img src="Homepage/Images/Logout.png" alt="">
                                <p>Log-out</p>
                                <span>></span>
                            </a>
            
                 </div>
                </div>
             </div>
       </div>



<div class="HomeBar">
<ul>
        <!-- list Class Active containing a link -->
        <li><a href="Ahome.php">HOME</a>

        </li>
                        <!-- lists Containing a link -->
        <!-- <li class="active"><a href="Ahomeshop.php">SHOP</a>
            <div class="Sub-1">
                <ul>
                    <li><a href="Aplants.php"class=a2>Plants</a></li>
                    <li><a href="Asoils.php"class=a2>Soils</a></li>
                    <li><a href="Apots.php"class=a2>Potters</a></li>
                    <li><a href="#"class=a2>Others</a></li>
                </ul>
            </div>
        </li>

        <li><a href="Best-seller.php">BEST-SELLERS</a>
        </li> -->
       
</ul>
<br>


</div>
</div>

<section class="update" style="background-color: #d6b796;">
<h4 style="font-family: Tangerine, cursive; color: black; font-size: 55px; text-align:center; font-weight:900;">Update-Profile</h4>     
<form action="" method="POST" enctype="multipart/form-data">
    
<?php
// Display validation or success messages
if (!empty($message)) {
    echo '<div class="alert alert-info" >';
    foreach ($message as $msg) {
        echo '<p style="text-align:center";>' . $msg . '</p>';
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
         <input type="hidden" name="old_pass" style="transform: none;" value="<?= $fetch_profile['password']; ?>">
         <span>Old password :</span>
         <input type="password" name="update_pass" style="transform: none;" placeholder="enter previous password" class="box">
         <span>New Password :</span>
         <input type="password" name="new_pass" style="transform: none;" placeholder="enter new password" class="box">
         <span>Confirm Password :</span>
         <input type="password" name="confirm_pass" style="transform: none;" placeholder="confirm new password" class="box">
         <span>Update picture :</span>
         <input type="file" style="transform: none;" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
         <input type="hidden" style="transform: none;" style="transform: none;" name="old_image" value="<?= $fetch_profile['image']; ?>">
      </div>
   </div>
   <div class="flex-btn">
      <input type="submit" class="btn" value="update profile" name="update_profile">
      <a href="Ahome.php" class="option-btn" style="text-decoration: none;">Go back</a>
   </div>
</form>
</section>

</div>  
 
<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('#PageBody').classList.add('activepage');
    });

    let subMenu = document.getElementById("subMenu1");
    function toggleMenu(){
        subMenu.classList.toggle("open-menu1");
    };
    </script>

</body>


    
  
    </html>