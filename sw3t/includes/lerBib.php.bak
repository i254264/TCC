<?php


// if(empty($idProjeto)){ echo "Oops";die; } //acessado diretamente

/* Tem que dar include nessa ordem pois ele é feito para autoload  */

// echo "1";

require_once($sw3tLocation."vendors/liteBibLibParser.php");
// include("../vendors/bib/bibtexParser.php");

// $bib = fopen($destinoArquivo, "r") or erro("BIB ERROR 1");
// $bibtex = fread($bib,filesize($destinoArquivo));
// $destinoArquivo = "tmp/6ea3a88b5a81acdbb776e71490ba0b59.ini";

$bib = parseBib($sw3tLocation.$destinoArquivo);
//echo "<pre>";
// print_r($bib);
$quantidadeArtigosAcm = 0;
$quantidadeArtigos = 0;
$quantidadeFalhas = 0;
$idsAcm="";
// print_r($bib);die;

$fonte=numbers($_POST['fonte']);
foreach ($bib as $artigo){
	// print_r($artigo);
	// die;
$artigo = array_change_key_case ($artigo,CASE_LOWER);
	if(empty($artigo['abstract'])){
		
		if(isset($artigo['acmid']) and !empty($artigo['author'])){
			
			$acm[]=$artigo;
			$quantidadeArtigosAcm++;
			continue;
		}else{
			
		
		$falhas[]=$artigo;
		$quantidadeFalhas++;
		
		}
		// print_r($artigo);die;
	}else{
		
		if(empty($artigo['author'])){
			// print_r($artigo);
			// die;
		$falhas[]=$artigo;
		$quantidadeFalhas++;
			continue;
			
		}
		
		
	$artigo['title'] = str_replace("   ","",$artigo['title']);
	$artigo['abstract'] = str_replace("   ","",$artigo['abstract']);	
	
	$tipoArtigo = (sanitize($artigo['type']));
	$tituloArtigo = (sanitize (tirarQuebraLinha($artigo['title'])));
	$journal = (sanitize (tirarQuebraLinha($artigo['journal'])));
	$anoArtigo = (sanitize($artigo['year']));
	$autoresArtigo = (sanitize($artigo['author']));
	// $resumoArtigo = html_entity_decode($artigo['abstract']);
	$resumoArtigo = ($artigo['abstract']);
	$resumoArtigo = (sanitize( tirarQuebraLinha($resumoArtigo)));
	
	$resumoArtigo = str_replace("(C)","&copy;",$resumoArtigo);
	

	/* REMOVER CODIGO ABAIXO CASO ESTEJA DANDO ERROS DE VELOCIDADE */


	$resumoArtigo = str_replace("("," (",$resumoArtigo);
	$resumoArtigo = str_replace("  "," ",$resumoArtigo);
	// $resumoArtigo = str_replace("  "," ",$resumoArtigo);
	/* ////\\\\ */
	// print_r($artigo);die;


	$resumoArtigo = str_replace('&nbsp'," ",$resumoArtigo);
	// $autorArtigo = str_replace("-","",$autorArtigo);
	
	$bibBackup = sanitize(base64_encode($artigo['backup']));
	
	$quantidadeArtigos ++;
	$sql = "INSERT INTO `resumo` (`id`, `idProjeto`, `tituloArtigo`, `resumo`, `resumoLimpo`, `resumoEnxuto`, `anoPublicacao`, `idFonte`, `journal`, resumoLematizado, bibBackup) VALUES (NULL, '$idProjeto', lower('$tituloArtigo'), lower('$resumoArtigo'),'', '', '$anoArtigo','$fonte','$journal','','{$bibBackup}')";
	// echo $sql;die;
	$result = $link->query($sql);
	$idResumo = mysqli_insert_id($link);
	
	
	$autoresArtigo = explode("and",$autoresArtigo);
		foreach ($autoresArtigo as $autorArtigo){
		
		$sql = "INSERT INTO `autores` (`id`, `idProjeto`, `idResumo`, `nomeAutor`) VALUES (NULL, '{$idProjeto}', '{$idResumo}', lower('{$autorArtigo}'))";
		$result = $link->query($sql);
		
		}
	
	
	}
}
if($quantidadeFalhas == 0 ){
	$out= "document.getElementById('result').innerHTML='$quantidadeArtigos artigo(s) Foram enviados<br>";
}else{
$out= "document.getElementById('result').innerHTML='$quantidadeArtigos artigo(s) Foram enviados<br> $quantidadeFalhas Não foram cadastrados ( artigos sem resumo ou autor )";
foreach ($falhas as $falha){
	if( empty ($falha['title'])){
		$out .= "<br>FALHOU e não tinha um titulo";
	}else{
	$out .= "<br>FALHOU : ".addslashes(sanitize($falha['title']));
	}
}



}


if($quantidadeArtigosAcm != 0 ){
	
	$out .="<h2>Fora detectado {$quantidadeArtigosAcm} Artigos da acm, deseja baixar ? <button onclick=\'baixarTodos()\' id=\'botaoTudo\' style=\'color:white;\' class=\'btn btn-success\'>Baixar Tudo</button></h2><br><div id=\'resultAcm\'></div><br><div id=\'falhasAcm\'></div>";
	// print_r($acm);
	// echo $quantidadeArtigosAcm;
	foreach($acm as $artigo){
		

	$tituloArtigo = sanitize($artigo['title']);
	
	if(empty($artigo['year'])){
		
		// print_r($artigo);die;
	}
	$anoArtigo = sanitize($artigo['year']);
	
	if(empty($artigo['author'])){
		
		// print_r($artigo);die;
	}
	$autorArtigo = sanitize($artigo['author']);
	
	$idAcm = sanitize($artigo['acmid']);
	$resumoArtigo = "";

	
	$quantidadeArtigos ++;
	$sql = "INSERT INTO `resumo` (`id`, `idProjeto`, `tituloArtigo`, `resumo`, `resumoEnxuto`, `anoPublicacao`, `idAcm`) VALUES (NULL, '$idProjeto', lower('$tituloArtigo'), lower('$resumoArtigo'), '', '$anoArtigo', '$idAcm')";
	// echo $sql;die;
	$result = $link->query($sql);
	$idResumo = mysqli_insert_id($link);
	$sql = "INSERT INTO `autores` (`id`, `idProjeto`, `idResumo`, `nomeAutor`) VALUES (NULL, '{$idProjeto}', '{$idResumo}', lower('{$autorArtigo}'))";
	$result = $link->query($sql);

	
	// $out .= "<br>".addslashes($tituloArtigo)." -  <button onclick='baixar({$idInsertAcm})' id='botao{$idInsertAcm}' style='color:white;' class='btn btn-success'>Baixar</button>";
	
	}
	
	
}
$out .= "';";


// $out.= "document.getElementById('file-input').style.background-color='red';";
$out.= "document.getElementById('file-input').style.cssText='background-color: #28a745; color: #fff;';";

// print_r($falhas);
echo $out;
die;
// erro("1");