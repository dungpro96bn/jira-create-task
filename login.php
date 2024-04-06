<?php
session_start();
include 'app/Config/connect.php';
require 'functions.php';

if (isset($_COOKIE['user_id'])) {
    // Log the user in using the user_id from the cookie
    $user_id = $_COOKIE['user_id'];
    // You may want to verify the user_id in your database before logging them in
    // $_SESSION['user_id'] = $user_id; // Set a session variable to keep the user logged in
    header("Location: /");
    exit;
} else{
    if (isset($_POST["Login"])) {

        $username = $_POST['username'];
        $password = $_POST['password'];
        $remember = isset($_POST['remember']) ? $_POST['remember'] : false;

        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Check if the password matches the one in the users array
            if (password_verify($password, $user['password'])) {
                $user_id = $user['id'];
                // Authentication successful
                $_SESSION['username'] = $user['username'];
                $_SESSION['authenticated'] = true;
                if($remember) {
                    setcookie('user_id', $user_id, time() + (3 * 24 * 60 * 60), '/');
                }
                header("Location: /");
                exit;
            } else {
                // Invalid password
                $error = 'Invalid username or password';
            }
        } else {
            // Username not found
            $error = 'Invalid username or password';
        }


    }
}

// Check if the form was submitted


$conn->close();

?>

<?php include 'header.php'; ?>

<main id="login">
    <div class="login-main">
        <h2>Login</h2>
        <form action="login.php" method="post">
            <div class="group-field">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="group-field field-pass">
                <label for="password">Password:</label>
                <div class="container-input">
                    <input id="password-field" type="password" name="password" required>
                    <span toggle="#password-field" class="fa-regular fa-eye-slash field-icon toggle-password"></span>
                </div>
            </div>
            <div class="remember-me">
                <label>
                    <input type="checkbox"value="yes" name="remember"> Remember me
                </label>
            </div>
            <div class="submit-form">
                <input class="login-btn" type="submit" name="Login" value="Login">
            </div>
        </form>
        <div class="link-page">
            <a class="btn-form" href="/register.php">Register</a>
        </div>
        <?php if (isset($error)): ?>
            <p class="note"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</main>


<?php include 'footer.php'; ?>