<?php
require_once("config.php");

set_time_limit (60); 
//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'],$_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);

if(!isset($_POST['idProjeto'])){
erro("ERRO FATAL");
}
$idProjeto = numbers($_POST['idProjeto']);

donoProjeto($idUsuario,$idProjeto);

if(isset($_POST['palavra'])){
    $palavra=sanitize($_POST['palavra']);
    $sql = "select id from stopwords where idProjeto ='{$idProjeto}' and palavra = '{$palavra}';";
    $result = $link->query($sql);
    $row = $result->fetch_assoc();
    if(empty($row['id'])){
        $sql = "insert into stopwords (idProjeto,Palavra) values ('{$idProjeto}','{$palavra}')";
    $result = $link->query($sql);
        echo "inseriu";
    }else{
        echo "ja existia";
    }
    
    die;
}

if(isset($_POST['dePara'])){
	
	$sql = "delete from depara where idProjeto = $idProjeto";
$result = $link->query($sql);
	foreach($_POST['de'] as $key=>$valor) {
		$de = strtolower(sanitize($valor));
		if(empty($de)){
			continue;
		}
		$para = strtolower(sanitize($_POST['para'][$key]));
			

				$sql = "INSERT INTO `depara` (`id`, `idProjeto`, `input`, `output`) VALUES (NULL, '{$idProjeto}', '{$de}', '{$para}')";

			$result = $link->query($sql);
		
	}
	
	
die;	
}
if(isset($_POST['isStopWord'])){
	
	$sql = "delete from stopwords where idProjeto = $idProjeto";
$result = $link->query($sql);
	foreach($_POST['stopWords'] as $stopWord) {
		$stopWord = strtolower(sanitize($stopWord));
		if(empty($stopWord)){
			continue;
		}
	
			

				$sql = "INSERT INTO `stopwords` (`id`, `idProjeto`, `palavra`) VALUES (NULL, '{$idProjeto}', '{$stopWord}')";

			$result = $link->query($sql);
		
	}
	
	
die;	
}


if(isset($_FILES['file'])){
	if (isset($_POST['area_conhecimento'])) {
		$area_conhecimento = sanitize($_POST['area_conhecimento']);
		$diretorio = "tmp/";
		if (!is_dir($diretorio) && !mkdir($diretorio, 0777, true) && !is_dir($diretorio)) {
			erro("Não foi possível criar a pasta de upload 'tmp/'. Verifique as permissões do projeto.");
		}
		if (!is_writable($diretorio)) {
			erro("A pasta de upload não tem permissão de escrita. Ajuste as permissões da pasta 'tmp/'.");
		}
		$nomeArquivo = basename($_FILES["file"]["name"]);
		$destinoArquivo = $diretorio . md5($nomeArquivo.time()).".ini";//.ini para nao ser interpretado por padrão
		$tipoDoArquivo = strtolower(pathinfo($nomeArquivo,PATHINFO_EXTENSION));
		if (!empty($_FILES['file']['error'])) {
			erro("O arquivo teve erro no upload: " . $_FILES['file']['error']);
		}
		if(($tipoDoArquivo != "xlsx")and ($tipoDoArquivo != "csv")and ($tipoDoArquivo != "bib")and ($tipoDoArquivo != "xml")){erro("Formato não permitido, Enviar somente 'xlsx','bib','xml'");}
		if (move_uploaded_file($_FILES["file"]["tmp_name"], $destinoArquivo)) {
		//enviou com sucesso
		} else {
			erro("Ocorreu um erro ao enviar o seu arquivo :<");
		}

		switch ($tipoDoArquivo){
		case "xlsx":
			erro("arquivo XLSX");
			include($sw3tLocation."includes/lerXlsx.php");
			break;
			
		case "bib":
			include($sw3tLocation."includes/lerBib.php");
			break;
		case "xml":
			include($sw3tLocation."includes/lerXml.php");
			break;
		case "csv":
			include($sw3tLocation."includes/lerCsv.php");
			break;

		default :
			erro("ERRO 201");
		
	}


	//echo $destinoArquivo;die;
	// $string = "chmod 777 $destinoArquivo";

	// exec($string);
	}
	else{
		$out = 'document.getElementById("result").innerHTML="Campo Área Conhecimento não preenchido"; ';
		echo $out;die;
	}

}



