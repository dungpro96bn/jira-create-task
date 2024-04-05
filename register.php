<?php
session_start();
include 'app/Config/connect.php';

// add verification
//===============================
//$verification = "@admin123@";
//$sql2 = "SELECT * FROM verification WHERE password='$verification'";
//$result = $conn->query($sql2);
//$sql_insert = "INSERT INTO verification (password) VALUES ('$verification')";
//if ($conn->query($sql_insert) === TRUE) {
//    $error = "New record created successfully";
//} else {
//    $error = "Error";
//}
//===============================
// end add verification


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // User data
    $username = $_POST['username'];
    $pass = $_POST['password'];
    $password = password_hash($pass, PASSWORD_DEFAULT);
    $verification = $_POST['verification'];

    $sql_check_username = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql_check_username);

    $sql_check_verification = "SELECT * FROM verification WHERE password='$verification'";
    $resultVerification = $conn->query($sql_check_verification);

    if ($result->num_rows > 0) {
        $error = 'Username already exists';
    } else{
        if($resultVerification->num_rows > 0){
            // Insert user into the database
            $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
            if ($conn->query($sql) === TRUE) {
                $error = 'New record created successfully';
            }
        } else{
            $error = 'Verification is not accurate';
        }
    }

}

// Close connection
$conn->close();



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" media="all" href="./assets/css/style.css?ver=<?php echo rand(); ?>">
    <link rel="stylesheet" media="all" href="./assets/css/main.css?ver=<?php echo rand(); ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <style>
        h2{
            display: block;
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
        }
        .login-container {
            width: 300px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 14px;
        }
        .toggle-password{
            position: absolute;
            right: 0;
            z-index: 1;
            width: 40px;
            height: 40px;
            bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .toggle-password svg{
            width: 20px;
        }
        label{
            margin-bottom: 6px;
            display: block;
        }
        .login-main{
            width: 100%;
            max-width: 300px;
        }
        .group-field{
            position: relative;
            margin-bottom: 30px;
        }
        .toggle-password{
            position: absolute;
            right: 81px;
            top: 0px;
            z-index: 1;
            width: 40px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: #ccc;
        }
        .toggle-password svg{
            width: 20px;
        }
        label{
            margin-bottom: 6px;
            display: block;
        }
        .login-main{
            width: 100%;
            max-width: 350px;
        }
        .btn-form{
            text-align: center;
            font-size: 14px;
            color: #222;
            width: 100%;
            display: block;
            padding-top: 20px;
        }
        .note{
            text-align: center;
            font-size: 14px;
            color: red;
        }
        .container-input{
            position: relative;
        }
        .container-input #generateBtn{
            position: absolute;
            width: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            cursor: pointer;
            background: #ccc;
            font-size: 14px;
            top: 0px;
            right: 0px;
            border-radius: 0 3px 3px 0;
        }
    </style>
</head>

<body>
<div id="login">
    <div class="login-main">
        <h2>Register</h2>
        <form action="register.php" method="post">
            <div class="group-field">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="group-field">
                <label for="password">Password:</label>
                <div class="container-input">
                    <input type="password" id="password-field" name="password" required>
                    <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg></span>
                    <span id="generateBtn" onclick="generatePassword()">Generate</span>
                </div>
            </div>
            <div class="group-field">
                <label for="username">Verification code from admin:</label>
                <input type="text" id="Verification" name="verification" required>
            </div>
            <div class="submit-form">
                <input class="login-btn" type="submit" value="Register">
            </div>
        </form>
        <div class="link-page">
            <a class="btn-form" href="/login.php">Login</a>
        </div>
        <?php if (isset($error)): ?>
            <p class="note"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</div>


<script>

    function generatePassword() {
        var length = 10;
        var charset = '!@#$%^&*()-_=+{}[];,.?~0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var password = '';
        for (var i = 0; i < length; i++) {
            var randomIndex = Math.floor(Math.random() * charset.length);
            password += charset[randomIndex];
        }
        $('#password-field').attr("type", "text");
        document.getElementById('password-field').value = password;
    }

    $(".toggle-password").click(function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

</script>

</body>
</html>