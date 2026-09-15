<?php
require_once("config.php");


//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'],$_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);

if(!isset($_POST['idArtigo'])){
erro("ERRO FATAL");
}

$idArtigo = numbers($_POST['idArtigo']);


$idProjeto = dadosResumo("idProjeto",$idArtigo);

donoProjeto($idUsuario,$idProjeto);

if(isset($_POST['salvar'])){
	$descricao = sanitize($_POST['descricao']);
	$justificativa = sanitize($_POST['justificativa']);
	// 	titulo;resumo;conteudo;topico;duplicado;area_conhecimento

	$certo = false;
	foreach($justificativasMotivo as $justificativaM){
		
		if($justificativaM == $justificativa){
			$certo = true;
		}
		
	}

	if(!$certo){
		$justificativa = "titulo";
	}
	
	$sql = "UPDATE `resumo` SET `justificativaStatus` = '{$justificativa}', `descricaoStatus` = '{$descricao}' WHERE id = '{$idArtigo}' ";
	$result = $link->query($sql);
	if($result){
	echo "Salvo Com Sucesso";
		// echo $sql;
	}else{
		echo "erro ao salvar";
		// echo $sql;
	}
	die;
	
}


if(isset($_POST['status'])){
if($_POST['status']=="excluido"){
	
	$sql = "update resumo set status='excluido' where id = '{$idArtigo}'";
}else{
	$sql = "update resumo set status='incluido' where id = '{$idArtigo}'";
	
}

$result = $link->query($sql);

die;
}

if(isset($_POST['motivo'])){
	
	 $justificativa = dadosMotivo("justificativaStatus",$idArtigo);

	$status = dadosResumo("status",$idArtigo);
	if($status =="incluido"){
		$status = "<button id = 'Astatus$idArtigo' onclick = 'excluir($idArtigo)' type='button' class='btn btn-success btn-sm'><i class='fa fa-dot-circle-o'></i> Incluido</button>";
	}else{
		$status = "<button id = 'Astatus$idArtigo' onclick = 'incluir($idArtigo)' type='button' class='btn btn-danger btn-sm'><i class='fa fa-dot-circle-o'></i> Excluido</button>";
	}
	
	$titulo = dadosResumo("tituloArtigo",$idArtigo);
	echo "<form id='formMotivo'><center>";
	echo "<b>$titulo - $status</b><br>";
	echo "<input type='hidden' value='true' name='salvar'>"; 	
	echo "<input type='hidden' value='{$idArtigo}' name='idArtigo' id='idArtigo'>"; // 	titulo;resumo;conteudo;topico;duplicado 	
	
	//como combinado com Marbilia, por padrao titulo está com checked
	echo 'Motivo Do Status :<br><div class="radio">
	<label for="titulo" class="form-check-label "><input type="radio" '.checkedMotivo($justificativa,"titulo").' id="titulo" name="justificativa" value="titulo" class="form-check-input" checked>Titulo</label>
	</div>
	<div class="radio">
	<label for="resumo" class="form-check-label "><input type="radio" '.checkedMotivo($justificativa,"resumo").' id="resumo" name="justificativa" value="resumo" class="form-check-input">Resumo</label>
	</div>
	<div class="radio">
	<label for="conteudo" class="form-check-label "><input type="radio" '.checkedMotivo($justificativa,"conteudo").' id="conteudo" name="justificativa" value="conteudo" class="form-check-input">Conteudo</label>
	</div>
	<div class="radio">
	<label for="duplicado" class="form-check-label "><input type="radio" '.checkedMotivo($justificativa,"duplicado").' id="duplicado" name="justificativa" value="duplicado" class="form-check-input">Duplicado</label>
	</div>
	';
	
	echo "<br><textarea style='min-height 400px; min-width: 350px; text-align: center;' name='descricao' placeholder='Descrição da análise'>".dadosMotivo("descricaoStatus",$idArtigo)."</textarea>";
	echo "<br><div id='resultSalvarMotivo'></div>";
	
	echo '</center></form>';
}


function checkedMotivo($justificativa,$oque){
	if($justificativa == $oque){
		return "checked";
	}else{
		return "";
	}
	
}
