<?php

include 'connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendVerificationEmail($email, $verification_code) {
    $mail = new PHPMailer(true);

    try {
        // Enable verbose debug output
        // $mail->SMTPDebug = 2; // You can set this to 3 for even more detailed output 

        // Server settings
        $mail->isSMTP(); 
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true; 
        $mail->Username   = 'dulaysgarden@gmail.com';
        $mail->Password   = 'wazmoepjmpcwbhbg';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('dulaysgarden@gmail.com', 'Dulays-Garden'); // Replace with your email and name
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body    = 'Your verification code is: ' . "<b> $verification_code </b>";

        $mail->send();
        echo "Verification email has been sent.";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if (isset($_POST['register'])) {
    $Fname = filter_var($_POST['Fname'], FILTER_SANITIZE_STRING);
    $Mname = filter_var($_POST['Mname'], FILTER_SANITIZE_STRING);
    $Lname = filter_var($_POST['Lname'], FILTER_SANITIZE_STRING);
    $contact = $_POST['contact'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
    $cpass = filter_var($_POST['cpass'], FILTER_SANITIZE_STRING);
    $image = filter_var($_FILES['image']['name'], FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'img/' . $image;

    $select = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $select->execute([$email]);

    if ($select->rowCount() > 0) {
        $message[] = 'User email already exists!';
    } else {
        if ($pass != $cpass) {
            $message[] = 'Confirm password does not match!';
        } else {
            $verification_code = bin2hex(random_bytes(3)); // Generate a random verification code

            $insert = $conn->prepare("INSERT INTO `users` (Fname, Mname, Lname, contact, email, password, image, verification_code, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
            $insert->execute([$Fname, $Mname, $Lname, $contact, $email, $pass, $image, $verification_code]);

            if ($insert) {
                if ($image_size > 20000000) {
                    $message[] = 'Image size is too large!';
                } else {
                    move_uploaded_file($image_tmp_name, $image_folder);
                    sendVerificationEmail($email, $verification_code);
                    header("Location: verify.php?email=$email");
                    $message[] = 'Registered successfully! Please verify your email.';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
           .message{
      color: red;
      text-align: center;
   }

    </style>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="style/register.css">
</head>
<body id="RegisterBody">

<section class="form-container">
    <form action="" enctype="multipart/form-data" method="POST" id="RegisterForm">
        <h3 id="RegisterHeader"  style="font-weight: 1000; font-size:30px;">Registration</h3>
        
<?php
if (isset($message)) {
    foreach ($message as $message) {
        echo '
        <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>
<br>
        <label for="Reg FirstName" class="RegisterLabel">FirstName</label>
        <input type="text" name="Fname" class="RegisterInput" placeholder="Enter your first name" required>

        <label for="Reg FirstName" class="RegisterLabel">MiddleName</label>
        <input type="text" name="Mname" class="RegisterInput" placeholder="Enter your middle name" required>

        <label for="Reg FirstName" class="RegisterLabel">LastName</label>
        <input type="text" name="Lname" class="RegisterInput" placeholder="Enter your last name" required>

        <label for="Reg FirstName" class="RegisterLabel">Contact</label>
        <input type="tel" name="contact" class="RegisterInput" placeholder="09xxxxxxxxx" maxlength="12" required>

        <label for="Reg FirstName" class="RegisterLabel">Email</label>
        <input type="email" name="email" class="RegisterInput" placeholder="Enter your email" required>

        <label for="Reg FirstName" class="RegisterLabel">Password</label>
        <input type="password" name="pass" class="RegisterInput" placeholder="Enter your password" required>

        <label for="Reg FirstName" class="RegisterLabel">Confirm Password</label>
        <input type="password" name="cpass" class="RegisterInput" placeholder="Confirm your password" required>

        <label for="Reg FirstName" class="RegisterLabel">Image</label>
        <input type="file" name="image" class="RegisterInput" required accept="image/jpg, image/jpeg, image/png">
        
        <button type="submit" name="register" class="ConfirmButton" style=" cursor:pointer;">Confirm</button>
        <button type="submit" name="ConfirmButton" class="ConfirmButton" style=" cursor:pointer;"><a href="Login.php">Login</a></button> <br><br>
    </form>
</section>

</body>
</html>
