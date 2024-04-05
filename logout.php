<?php
session_start();

setcookie('user_id', '', time() - 3600, '/');

// Unset all of the session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the login page
header('Location: login.php');
exit;