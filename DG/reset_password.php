<?php
include 'connection.php';
$message = '';

if (isset($_POST['reset_password'])) {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($user_id) && !empty($password) && !empty($confirm_password) && $password === $confirm_password) {
        // Fetch the expiration time from the database
        $sql = "SELECT reset_expires FROM `users` WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $expiration_time = strtotime($row['reset_expires']);
            $current_time = time();

            if ($current_time <= $expiration_time) {
                // Update the user's password without hashing
                $sql = "UPDATE `users` SET password = ?, reset_expires = NULL WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$password, $user_id]);

                if ($stmt->rowCount() > 0) {
                    $message = "Your password has been reset. You can now <a href='login.php'>login</a>.";
                } else {
                    $message = "Failed to reset your password. Please try again.";
                }
            } else {
                $message = "This password reset link has expired. Please request a new one.";
            }
        } else {
            $message = "Invalid user ID.";
        }
    } else {
        $message = "All fields are required and passwords must match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="stylecomponents.css">
    <link rel="stylesheet" href="style/u-login.css">
</head>
<body id="LoginBody">

<?php echo $message; ?>

<form action="" method="POST" id="LoginForm" onsubmit="return validatePassword()">
    <h2 id="LoginHeader" style="font-weight: 1000; font-size:30px;">New Password</h2>

    <input type="hidden" name="user_id" value="<?php echo isset($_GET['user_id']) ? htmlspecialchars($_GET['user_id'], ENT_QUOTES, 'UTF-8') : ''; ?>">
    
    <label for="password" class="LoginLabel">New Password:</label>
    <input type="password" name="password" class="LoginInput" required>
    <br>
    
    <label for="confirm_password" class="LoginLabel">Confirm Password:</label>
    <input type="password" name="confirm_password" class="LoginInput" required>
    <br>

    <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
    <label for="showPassword">Show Password</label>
    <br>

    <span id="message" style="color:red;"></span>
    <br>
    
    <button type="submit" name="reset_password" id="LoginButton">Reset Password</button>
</form>

<script>
    function validatePassword() {
        var password = document.getElementsByName("password")[0].value;
        var confirm_password = document.getElementsByName("confirm_password")[0].value;
        var message = document.getElementById("message");

        var minLength = 8;
        var hasUpperCase = /[A-Z]/.test(password);
        var hasLowerCase = /[a-z]/.test(password);
        var hasDigit = /\d/.test(password);
        var hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        if (password.length < minLength || !hasUpperCase || !hasLowerCase || !hasDigit || !hasSpecialChar) {
            message.textContent = "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one digit, and one special character.";
            return false;
        }

        if (password !== confirm_password) {
            message.textContent = "Passwords do not match.";
            return false;
        }

        message.textContent = "";
        return true;
    }

    function togglePasswordVisibility() {
        var passwordField = document.getElementsByName("password")[0];
        var confirmPasswordField = document.getElementsByName("confirm_password")[0];
        var type = passwordField.type === "password" ? "text" : "password";
        passwordField.type = type;
        confirmPasswordField.type = type;
    }
</script>

</body>
</html>
