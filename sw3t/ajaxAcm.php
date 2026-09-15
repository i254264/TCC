<?php
require_once("config.php");

set_time_limit (10); 
//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'],$_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);

if(!isset($_POST['idProjeto'])){
erro("ERRO FATAL");
}
$idProjeto = numbers($_POST['idProjeto']);

donoProjeto($idUsuario,$idProjeto);


$sql ="select tituloArtigo,idAcm from resumo where idProjeto = '{$idProjeto}' and resumo ='' order by idAcm desc limit 1";
$result = $link->query($sql);
$row = $result->fetch_assoc();
	
$tituloArtigo = $row['tituloArtigo'];
$idAcm = $row['idAcm'];
	
$url = 'https://dl.acm.org/tab_abstract.cfm?id='.$idAcm.'&usebody=tabbody&_cf_containerId=cf_layoutareaabstract&_cf_nodebug=true&_cf_nocache=true';
$url = 'https://dl.acm.org/tab_abstract.cfm?id='.$idAcm;

$ch = curl_init();
// echo $url;
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

$headers = array();
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:69.0) Gecko/20100101 Firefox/69.0';
$headers[] = 'Accept: */*';
$headers[] = 'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3';
$headers[] = 'Connection: close';
$headers[] = 'Referer: https://dl.acm.org/citation.cfm?id='.$idAcm;
$headers[] = 'Te: Trailers';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$resumo= get_string_between($result,"<p>","</p>");
if(empty($resumo)){
	$resumo= get_string_between($result,'<div style="display:inline">',"</div>");
}
$outErro="";
if(empty($resumo)){
	// echo "alert('Nao Fora possivel baixar o artigo - ".addslashes($tituloArtigo)."');";
	$resumo ="An abstract is not available.";
	
	$outErro = "document.getElementById('falhasAcm').innerHTML= document.getElementById('falhasAcm').innerHTML + '<br>Nao Fora possivel baixar o artigo - ".addslashes($tituloArtigo)." - {$url} - {$result}';";
}

$resumo = sanitize($resumo);

$sql = "update resumo set resumo = '{$resumo}' where idAcm = '{$idAcm}'";
$result = $link->query($sql);

$sql = "select count(idAcm) from resumo where idProjeto = '18' and resumo != '' and idAcm != 0";
$result = $link->query($sql);
$row = $result->fetch_assoc();
$quantosBaixados = $row['count(idAcm)'];



$sql = "select count(idAcm) from resumo where idProjeto = '18' and idAcm != 0";
$result = $link->query($sql);
$row = $result->fetch_assoc();
$quantos = $row['count(idAcm)'];


$porcentagem = (int)(($quantosBaixados/$quantos) * 100);
// die;
$out = '<div class="text-muted">Baixando ... </div><strong>'.$quantosBaixados.'/'.$quantos.' ('.$porcentagem.'%)</strong><div class="progress progress-xs mt-2" style="height: 15px;"><div class="progress-bar bg-success" role="progressbar" style="width: '.$porcentagem.'%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div></div>';
							
			$out = str_replace(PHP_EOL,"",$out);				
			$out = str_replace("
","",$out);				
			$out ="document.getElementById('resultAcm').innerHTML='{$out}';";
			/* $out .="
			$.ajax({
            type: 'POST',
            url: 'ajaxAcm.php',
		data: 'idProjeto={$idProjeto}&baixarTodos=true',
            success: function(data) {
                eval(data);
            }
        });";	 */		
$out .="baixarTodos();"	;

echo $out;
echo $outErro;
function get_string_between($string, $start, $end){
	//http://www.justin-cook.com/2006/03/31/php-parse-a-string-between-two-strings/
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);   
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}