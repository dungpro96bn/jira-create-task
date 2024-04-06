<?php

function random () {
    $min = 1;
    $max = 999999999;
    $randomNumber = rand($min, $max);
    echo $randomNumber;
}

function checkLogin (){
    if (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
            header('Location: login.php');
            exit;
        }
    }
}

function title_page(){
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $currentUrl = $scheme . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $url = $currentUrl;
    $parsedUrl = parse_url($url);
    $path = $parsedUrl['path'];

    if($path == '/'){
        echo "Create Tasks On Jira";
    } elseif ($path == '/login.php'){
        echo "Login";
    } elseif ($path == '/board.php'){
        echo "Board";
    } elseif ($path == '/register.php'){
        echo "Register";
    }

}