<?php


function resultToArray($result) {
/*
Função que pega um result do mysql e transforma em array
*/
    $rows = array();
    while($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}
function erro($erro){
/* http_response_code(403);
header("content-type:application/json");
$out['error']=$erro;
echo json_encode($out); */
$erro = addslashes($erro);
$out= "document.getElementById('result').innerHTML='$erro';";
$out.= "document.getElementById('file-input').style.backgroundColor='red';";
echo $out;
die;
	
}
function tirarQuebraLinha($string){
	$string = str_replace(PHP_EOL," ",$string);
	$string = str_replace(urldecode("%0d%0a")," ",$string);
	$string = preg_replace( "/\r|\n/", " ", $string );
$string = str_replace( "
", " ", $string );
$string = str_replace( "
", " ", $string );
	$string = str_replace( "  ", " ", $string );
	
	return $string;
	
}
function checar($login,$pass){

	global $link;
	global $dbUso;
	$login = sanitize($login);
	$pass = sanitize($pass);
	$query = "SELECT id FROM {$dbUso}.`usuario` where email= '{$login}' and senha= '{$pass}'";
	$result = $link->query($query);
	$row = $result->fetch_assoc();
	if(empty($row['id'])){
	//falhou na autenticação, mandar pro login
		header("location:index.php");
		die;
	}
}

function delete_all_between($beginning, $end, $string) {
  $beginningPos = strpos($string, $beginning);
  $endPos = strpos($string, $end);
  if ($beginningPos === false || $endPos === false) {
    return $string;
  }

  $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

  return delete_all_between($beginning, $end, str_replace($textToDelete, '', $string)); // recursion to ensure all occurrences are replaced
}

function donoProjeto($idUsuario,$idProjeto, $erro = false){

	global $link;
	global $dbUso;
	$idUsuario = numbers($idUsuario);
	$idProjeto = numbers($idProjeto);
	$query = "SELECT id FROM {$dbUso}.`projeto` where idUsuario= '{$idUsuario}' and id= '{$idProjeto}' and ativo = 1";
	$result = $link->query($query);
	$row = $result->fetch_assoc();

	if(empty($row['id'])){
	//nao é o dono do projeto ou projeto inexistente
	if($erro){
		echo "Erro Critico (I)";
        die; // die adicionado em 17/02/2020 nao sei onde reflete
	}else{
		header("location:meusProjetos.php");
	}
		die;
	}
	
	
}


function dadosProjeto($oque,$idProjeto){

	global $link;
	global $dbUso;
	$oque = sanitize($oque);
	$idProjeto = numbers($idProjeto);
	$query = "SELECT {$oque} FROM {$dbUso}.`projeto` where id= '{$idProjeto}'";
	$result = $link->query($query);
	$row = $result->fetch_assoc();
	
	return ($row[$oque]);
	
}
function dadosResumo($oque,$idResumo){

	global $link;
	global $dbUso;
	$oque = sanitize($oque);
	$idResumo = numbers($idResumo);
	$query = "SELECT {$oque} FROM {$dbUso}.`resumo` where id= '{$idResumo}'";
	$result = $link->query($query);
	$row = $result->fetch_assoc();
	
	return ($row[$oque]);
	
}

function dadosMotivo($oque,$idResumo){
		
	return dadosResumo($oque,$idResumo);
	global $link;
	global $dbUso;
	$oque = sanitize($oque);
	$idResumo = numbers($idResumo);
	$query = "SELECT {$oque} FROM {$dbUso}.`motivoStatus` where idResumo= '{$idResumo}'";
	$result = $link->query($query);
	$row = $result->fetch_assoc();
	
	return $row[$oque];
	
}



function numbers($string){
	$string = preg_replace('/\D/', '', $string);
	return $string;
}
function sanitize($string,$onlyMysql=false){
	/*
	Função para sanitizar um input
	input : input, e se quer só sanitizar via mysql
	output : input sanitizado
	*/
	global $link;
	if($onlyMysql){
		$string = mysqli_real_escape_string($link,$string);
	}else{
		$string = htmlentities($string);
		$string = mysqli_real_escape_string($link,$string);
	}
	return $string;
}
function hashPassword($string,$fromCookie=false){
	/*
	Função para hashear as senha
	input $string e se o hash veio do cookie
	output Senha hasheada
	*/
	
	global $salt0;
	global $salt1;
	global $salt2;
	global $salt3;
	global $salt4;
	
	
	if($fromCookie){
		$string = hash("sha512",$salt1.$string.$salt4);
	}else{
		$string = hash("sha512",$salt0.$string);
		$string = hash("sha512",$salt4.$string.$salt0);
		$string = hash("sha512",$string.$salt3);
		$string = hash("sha512",$string.$salt2);
	}

	return $string;

}
function idUsuario($login){
	global $link;
	global $dbUso;
	$login = sanitize($login);
	$query = "SELECT id FROM {$dbUso}.`usuario` where email= '{$login}'";
	$result = $link->query($query);
	$row = $result->fetch_assoc();
	return $row['id'];
}
function enviarEmail($para,$nome,$assunto, $html){
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
$headers .= "To: $nome <$para>" . "\r\n";
$headers .= 'From: Sw3t <contato@marbilia.cf>' . "\r\n";
mail($para, $assunto, $html, $headers);
}

function limparOutputPython($output){
	$output = str_replace(", u'",", '",$output);
$i =0;
	
	while($i < 30){
	$output = str_replace("($i, '","(",$output);

	$i++;
	}
	$output = str_replace("')",")",$output);
	$output = str_replace("(","",$output);
	$output = str_replace(")","",$output);
	$output = explode(" + ",$output);
	foreach($output as $key => $outputPalavra){
		
		$outputPalavra= explode("*",$outputPalavra);
		// o 0 é o valor e o 1 é a palavra
		
		$outputTmp[$key]["valor"]=$outputPalavra[0];
		$outputTmp[$key]["palavra"]=str_replace('"',"",$outputPalavra[1]);
		
		
		
	}
	return $outputTmp;
}
function randGen($length,$type="alphanum"){
    $length = (int)$length;
    switch ($type){
        case("alphanum"):
        case("alphaNum"):{$charset = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890";break;}
        case("alpha"):{$charset = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";break;}
        case("numbers"):{$charset = "1234567890";break;}
        case("hex"):{$charset = "1234567890abcdef";break;}
        case("hex1"):{$charset = "abcdef";break;}
        default:{$charset = "01";break;}
    }
    return  substr(str_shuffle(str_repeat($charset, $length)), 0, $length);
}