<?php
session_start();
include 'app/Config/connect.php';
include 'functions.php';

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

<?php include 'header.php'?>

<main id="login" class="register-page">
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
                    <span toggle="#password-field" class="fa-regular fa-eye-slash field-icon toggle-password"></span>
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
</main>



<?php include 'footer.php'; ?>