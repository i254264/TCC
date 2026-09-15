<?php
require_once("config.php");
checar($_SESSION['email'],$_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);
$idArtigo = numbers($_POST['idArtigo']);

$sql = "select idProjeto from resumo where id = '$idArtigo'";
// echo $sql;die;
$result = $link->query($sql);
$row = $result->fetch_assoc();
$idProjeto = numbers($row['idProjeto']);

donoProjeto($idUsuario,$idProjeto);

$sql = "Select tituloArtigo, resumo, anoPublicacao from resumo where id = '{$idArtigo}'";
$result = $link->query($sql);
$row = $result->fetch_assoc();

$tituloArtigo = tirarQuebraLinha(addslashes($row['tituloArtigo']));
$resumo = tirarQuebraLinha(addslashes($row['resumo']));
$anoPublicacao = tirarQuebraLinha(addslashes($row['anoPublicacao']));

echo "document.getElementById('infoTitulo').innerHTML = '$tituloArtigo'; ";
echo "document.getElementById('infoResumo').innerHTML = '$resumo'; ";
echo "document.getElementById('infoData').innerHTML = '$anoPublicacao'; ";

$sql = "Select nomeAutor from autores where idResumo = '{$idArtigo}'";
$result = $link->query($sql);
$rows = resultToArray($result);
$out="";
foreach($rows as $autor){
	
	$autor = ($autor['nomeAutor']);
	$out .= addslashes('<span class="infoboxAuthorSpan">'.$autor.'</span><br>');
}
$out= tirarQuebraLinha($out);
echo "document.getElementById('infoAutores').innerHTML = '$out'; ";