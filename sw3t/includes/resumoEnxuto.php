<?php
	/* ESSE ARQUIVO É INCLUÍDO PELO "ajaxPython.php" */
	if(empty($idProjeto)){
		echo "Sem projeto";die;
	}
	$erro = false;
	$sql = "Select input,output from depara where idProjeto = '{$idProjeto}'";
	$result = $link->query($sql);
	$rows =resultToArray($result);
	//buildar array agr para que não fique pesado no server dps
	$i=0;

	if(empty($rows[0]['input'])){
		$out = 'document.getElementById("warnigResumo").innerHTML="Warning: não há nenhum input na tabela de-para.<br>Será gerado resumo igual resumo limpo."; ';
		$out .= 'document.getElementById("enxuto").innerHTML="Gerado sem alteração"; ';
		$out .= 'document.getElementById("enxuto").className = "btn btn-danger btn-lg btn-block";';

		$erro = true;
		$rows[0]['input']="";
		$rows[0]['output']="";
	}
	//cria matriz $dePara para usar como substituição das palavras no foreach logo abaixo 
	foreach($rows as $resultado){
	 	$dePara[$i]['de']=$resultado['input'];
		$dePara[$i]['para']=$resultado['output'];
		$i++;
	}
	$sql = "Select id,resumoLimpo from resumo where idProjeto = '{$idProjeto}'";
	$result = $link->query($sql);
	$rows =resultToArray($result);
	foreach($rows as $artigo){
		$idResumo = $artigo['id'];
		$resumo = $artigo['resumoLimpo'];
		if(empty($resumo)){
			$out = 'document.getElementById("warnigResumo").innerHTML="Warning: Não há resumo limpo em ('.$idResumo.')."; ';
			$out .= 'document.getElementById("enxuto").innerHTML="Erro ao gerar Resumo Enxuto"; ';
			$out .= 'document.getElementById("enxuto").className = "btn btn-danger btn-lg btn-block";';
			//se algum Resumo Limpo estiver errado, não gera o Resumo enxuto, sai do laço
			echo $out; die;
			$erro = true;
		}

		//aqui é usado matriz $dePara
		foreach($dePara as $deParaIteracao){
			$de = $deParaIteracao['de'];
			if($de ==" "){continue;}
			$para = $deParaIteracao['para'];
			$resumo = str_replace($de,$para,$resumo);		
		}
		
		$resumo = sanitize($resumo,true);

		//faz update na coluna resumoEnxuto
		if(dadosResumo("resumoEnxuto",$idResumo) !=  $resumo){
			$sql = "UPDATE `resumo` SET `resumoEnxuto` = '{$resumo}', timeEnxuto = CURRENT_TIME() WHERE `resumo`.`id` = ".$idResumo;
			$result = $link->query($sql);
		}
	}

	if(!$erro){
		$out = 'document.getElementById("enxuto").innerHTML="Gerado Com Sucesso"; ';
		$out .= 'document.getElementById("enxuto").className = "btn btn-success btn-lg btn-block";';
		echo $out;	die;
	}
	else {
		//Será gerado mesmo assim um resumo, mas igual ao resumo limpo.
		echo $out;	die;
	}
?>