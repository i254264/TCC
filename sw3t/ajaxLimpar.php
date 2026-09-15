<?php
	#####CÓDIGOS DE CONFIGURAÇÃO#####
	require_once("config.php");
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	set_time_limit (60); 
	//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
	checar($_SESSION['email'],$_SESSION['key']);
	$idUsuario = idUsuario($_SESSION['email']);

	if(!isset($_POST['idProjeto'])){
	erro("ERRO FATAL");
	}
	$idProjeto = numbers($_POST['idProjeto']);
	donoProjeto($idUsuario,$idProjeto);
	#################################

	if(isset($_POST['limpar'])){
		$sql = "SELECT count(id) FROM `resumo` WHERE idProjeto = '{$idProjeto}'";
		$result = $link->query($sql);
		$row = $result->fetch_assoc();

		$countArtigos = $row['count(id)'];
		if($countArtigos == "0" ){
			$out = 'document.getElementById("artigosLimpos").innerHTML="Não há artigos cadastrados"; ';
			$out .= 'document.getElementById("artigosLimpos").className = "btn btn-danger btn-lg btn-block";';
			
			echo $out;die;
		}
		$sql = "Select id,resumo from resumo where idProjeto = '{$idProjeto}'";
		$result = $link->query($sql);
		$rows =resultToArray($result);
		foreach($rows as $artigo){
			$idResumo = $artigo['id'];
			$resumo = $artigo['resumo'];
			$resumoLimpo = sanitize($resumo,true);
			//tira aspas simples
			$resumoLimpo = str_replace('&#039',"",$resumoLimpo);
			$resumoLimpo = str_replace("\r\n"," ",$resumoLimpo);
			//tira todas as pontuações
			$resumoLimpo =  preg_replace('/[[:punct:]]+/'," ",$resumoLimpo);

			//salva no banco de dados os resumos sem pontuação
			$sql = "UPDATE `resumo` SET `resumoLimpo` = '{$resumoLimpo}' WHERE `resumo`.`id` = ".$idResumo;
			$result = $link->query($sql);
		}

		$out = 'document.getElementById("artigosLimpos").innerHTML="Gerado Com Sucesso"; ';
		$out .= 'document.getElementById("artigosLimpos").className = "btn btn-info btn-lg btn-block";';
		echo $out;	die;
	}
?>

