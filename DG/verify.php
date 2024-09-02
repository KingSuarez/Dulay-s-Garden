<?php

include 'connection.php';

// if (isset($_GET['code'])) {
//     $verification_code = $_GET['code'];
    
//     $select = $conn->prepare("SELECT * FROM `users` WHERE verification_code = ?");
//     $select->execute([$verification_code]);

//     if ($select->rowCount() > 0) {
//         $update = $conn->prepare("UPDATE `users` SET is_verified = 1 WHERE verification_code = ?");
//         $update->execute([$verification_code]);
//         echo "Email verified successfully!";
//     } else {
//         echo "Invalid verification code!";
//     }
// } else {
//     echo "No verification code provided!";
// }
$message = '';

if (isset($_POST['verify'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $otp = filter_var($_POST['otp'], FILTER_SANITIZE_STRING);

    $select = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND verification_code = ?");
    $select->execute([$email, $otp]);

    if ($select->rowCount() > 0) {
        $update = $conn->prepare("UPDATE `users` SET is_verified = 1 WHERE email = ?");
        $update->execute([$email]);
        header("location: login.php");
    } else {
        $message = "Invalid OTP or email. Please try again.";
    }
}

if (isset($_GET['email'])) {
   $email = $_GET['email'];
   $message = "Your OTP has been sent to your email. Please check your inbox.";
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
   <h2 style="text-align: center;" id="LoginHeader"  style="font-weight: 1000; font-size:30px;">Email Verification</h2>
   <?php
if(isset($message)){
      echo '
       <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   
}
?>
   <!-- <label for="email"class="LoginLabel" >Email:</label> -->
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" hidden required>
        <label for="otp" class="LoginLabel">Email OTP Code</label>
        <input type="text" name="otp" class="LoginInput" style="text-align: center;" required>
        
        <button type="submit" name="verify" class="LoginInput" id="LoginButton" style="border-radius:20px;">Verify</button>
        <br><br>
        <!-- <label class="LoginLabel">Password</label>
      <input type="password" name="pass" class="LoginInput" placeholder="Enter your password" required>

      <input type="submit" value="Login Now" class="LoginInput" id="LoginButton" name="login">
      <p>Don't have an account? <a href="registration.php">Register now</a></p> -->

      <!-- <a href="index.php" id="LoginShopDirect"><p>Shop Directly?</p></a><br> -->
   </form>
</section>
</body>
</html>
