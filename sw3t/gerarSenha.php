<?php
require_once("config.php");
if(isset($_POST['senha'])){
	$senha = hashPassword($_POST['senha']);
	$senha = hashPassword($senha,true);
	echo $senha;
}
?>
<br>
<form method="post">
<input type="password" name="senha">
<input type="submit">