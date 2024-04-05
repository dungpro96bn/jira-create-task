<?php
function generatePassword($length = 10) {
    $characters = '!@#$%^&*()-_=+{}[];,.?~0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    $characterLength = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[mt_rand(0, $characterLength)];
    }
    return $password;
}
echo generatePassword();

