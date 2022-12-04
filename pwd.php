<?php

$password="outlet";

$options = [
    'cost' => 11  
];

$hashed_password=password_hash($password, PASSWORD_BCRYPT, $options);

echo $hashed_password;

?>