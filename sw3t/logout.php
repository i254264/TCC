<?php 
session_start();
$_SESSION = array();
setcookie("login", "", time() - (86400 * 30));
setcookie("key", "", time() - (86400 * 30));
//print_r($_COOKIE);
header("Location:index.php");
?>