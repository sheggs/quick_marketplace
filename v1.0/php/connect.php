<?php
DEFINE('USER','root');
DEFINE('PASSWORD','');
DEFINE('HOST','localhost');
DEFINE('DB','marketplace4');
$connect = @mysqli_connect(HOST,USER,PASSWORD,DB)
OR die('Databse connection error. Here is the error code!'.mysqli_connect_error());

?>
