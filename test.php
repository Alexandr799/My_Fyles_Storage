<?php 


$a = password_hash(123, PASSWORD_DEFAULT);
$b = password_hash(123, PASSWORD_DEFAULT);

var_dump(password_verify('qwerty', '$2y$10$u7USdpFRSMaP6i/d9DN4GuBUAIPoV3tIJljYeUMVdB.e8H5vVcFny'));