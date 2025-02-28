<?php
include 'connection.php';

session_start();

// if (isset($_POST['login'])) {
//    $email = $_POST['email'];
//    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
//    $pass = $_POST['pass'];
//    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

//    $sql = "SELECT * FROM `users` WHERE email = ? AND password = ?";
//    $stmt = $conn->prepare($sql);
//    $stmt->execute([$email, $pass]);
//    $rowCount = $stmt->rowCount();

//    $row = $stmt->fetch(PDO::FETCH_ASSOC);

//    if ($rowCount > 0) {
//        if ($row['is_verified'] == 1) {
//            // Redirect verified user to the user home page
//            $_SESSION['user_id'] = $row['id'];
//            header('Location: Ahome.php');
//        } elseif ($row['is_verified'] == 2) {
//            // Redirect verified admin to the admin dashboard
//            $_SESSION['admin_id'] = $row['id'];
//            header('Location: dashboard.php');
//        } else {
//             $message[] = 'Please verify your email before logging in.';
//        }
//    } else {
//        $message[] = 'Incorrect email or password!';
//    }
// }

if (isset($_POST['login'])) {
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_EMAIL);
   $pass = $_POST['pass'];
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   // Fetch user by email and password
   $sql = "SELECT * FROM `users` WHERE email = ? AND password = ?";
   $stmt = $conn->prepare($sql);
   $stmt->execute([$email, $pass]);
   $rowCount = $stmt->rowCount();

   // Fetch the user record
   $row = $stmt->fetch(PDO::FETCH_ASSOC);

   if ($rowCount > 0) {
       // Check if the user has 3 or more cancelled or unclaimed orders
       $user_id = $row['id'];
       $check_orders = $conn->prepare("
           SELECT COUNT(*) AS cancelled_or_unclaimed_count 
           FROM `orders` 
           WHERE user_id = ? 
           AND status IN ('Cancelled', 'Unclaimed')
       ");
       $check_orders->execute([$user_id]);
       $order_count = $check_orders->fetch(PDO::FETCH_ASSOC)['cancelled_or_unclaimed_count'];

       // If the user has 3 or more cancelled/unclaimed orders, block the user
       if ($order_count >= 3) {
           $message[] = 'Your account has been blocked due to multiple cancelled or unclaimed orders.';
       } elseif ($row['is_verified'] == 1) {
           // Redirect verified user to the user home page
           $_SESSION['user_id'] = $row['id'];
           header('Location: Ahome.php');
           exit();
       } elseif ($row['is_verified'] == 2) {
           // Redirect verified admin to the admin dashboard
           $_SESSION['admin_id'] = $row['id'];
           header('Location: dashboard.php');
           exit();
       } else {
           $message[] = 'Please verify your email before logging in.';
       }
   } else {
       $message[] = 'Incorrect email or password!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <style>
   .message{
      color: red;
      background: #f2dede;
    color: #a94442;
    padding: 10px;
    width: 95%;
    border-radius: 5px;
   }
   
   </style>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
      <!-- custom css file link  -->
   <link rel="stylesheet" href="stylecomponents.css">
   <link rel="stylesheet" href="style/u-login.css">
</head>
<body id="LoginBody">


   
<section class="form-container">
   <form action="" method="POST" id="LoginForm">
   <h2 id="LoginHeader" style="font-weight: 1000; font-size:30px;">LOGIN</h2>
      
   <?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         <span>'.$message.'</span>
      </div>
      <br>';
   }
}
?>
      <label class="LoginLabel">Email</label>
      <input type="email" name="email" class="LoginInput" placeholder="Enter your email" required>

      <label class="LoginLabel">Password</label>
      <input type="password" name="pass" class="LoginInput" placeholder="Enter your password" required>

      <input type="submit" value="Login Now" class="LoginInput" id="LoginButton" name="login">
      
      <p><a href="forgot_password.php" class="forgot">Forgot your password?</a></p>
      <div class="re"><p>Don't have an account? <br><a href="registration.php" class="reg">Register now</a></p></div>
           <div style="margin-top: 20px;"><p class="shop"><a href="index.php" class="LoginShopDirect" >Shop Directly?</a></p></div>    
   </form>
</section>
</body>
</html>
