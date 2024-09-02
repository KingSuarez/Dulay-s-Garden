<?php
include 'connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (isset($_POST['forgot_password'])) {
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    $sql = "SELECT * FROM `users` WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $user_id = $user['id'];
        $expires = date("U") + 300; // 30 minutes from now

        $sql = "UPDATE `users` SET reset_expires = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([date("Y-m-d H:i:s", $expires), $email]);

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'dulaysgarden@gmail.com';
            $mail->Password = 'wazmoepjmpcwbhbg';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('dulaysgarden@gmail.com', 'Dulays Garden');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = 'Please click the link below to reset your password: <a href="http://localhost/DG/reset_password.php?user_id=' . $user_id . '">Reset Password</a>';

            $mail->send();
            $message[] = "Password reset has been sent to your email.";
        } catch (Exception $e) {
            $message[] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message[] = "No account found with that email address.";
    }
}

if (isset($_POST['back'])) {
    header('location:login.php');

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="stylecomponents.css">
    <link rel="stylesheet" href="style/u-login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body id="LoginBody">

<section class="form-container" id="LoginForm">
    
    <form action="" method="POST" id="ForgotPasswordForm">
        <h2 id="LoginHeader"  style="font-weight: 1000; font-size:30px;"> Forgot Password </h2>
        <?php
if (isset($message)) {
    foreach ($message as $message) {
        echo '<div class="message"><span>'.$message.'</span><i class="fas fa-times" onclick="this.parentElement.remove();"></i></div>';
    }
}
?>
<br>
        <label class="LoginLabel" style="font-weight: bolder;">Email</label>
        <input type="email" name="email" class="LoginInput"  placeholder="Enter your email" >
        <input type="submit" value="Submit" class="LoginInput" id="LoginButton" name="forgot_password">

        <input type="submit" value="Back" class="LoginInput" id="LoginButton" name="back">
        

    </form>
</section>
</body>
</html>
