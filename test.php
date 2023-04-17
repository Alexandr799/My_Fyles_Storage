<?php 


$a = password_hash(123, PASSWORD_DEFAULT);
$b = password_hash(123, PASSWORD_DEFAULT);

var_dump(password_verify(123, $b));