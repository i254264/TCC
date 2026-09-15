<?php 
require_once("config.php");
//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'],$_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);
$idProjeto=numbers($_GET['idProjeto']);
donoProjeto($idUsuario,$idProjeto);
$nomeProjeto = sanitize(dadosProjeto("nome",$idProjeto));
$nomeProjeto = mb_substr($nomeProjeto, 0, 20);
$_SESSION['idProjeto']=$idProjeto;
$_SESSION['nomeProjeto']=$nomeProjeto;
$erro = "";

if(isset($_POST['projetoCopiar'])){
	if(isset($_POST['depara'])){

		$sql = "select input,output from depara where idProjeto = '{$idProjeto}'";
		
		$result = $link->query($sql);
		$rows = resultToArray($result);
		$arrayDepara=array();
		foreach($rows as $depara){
			$arrayDepara[]=array("input"=>$depara['input'],"output"=>$depara['output']);
		}
		foreach($_POST['projetoCopiar'] as $key => $value){
			$idProjetoAlvo = numbers($key);
			donoProjeto($idUsuario,$idProjetoAlvo,true);
			foreach($arrayDepara as $insert){
				$input = sanitize($insert['input'],true);
				$output = sanitize($insert['output'],true);
				$sql = "delete from depara where idProjeto = '{$idProjetoAlvo}' and input='{$input}'";
				$result = $link->query($sql);
				$sql = "INSERT INTO `depara` (`id`, `idProjeto`, `input`, `output`, `idusuario`) VALUES (NULL, '{$idProjetoAlvo}', '{$input}', '{$output}', NULL)";
				$result = $link->query($sql);
			}
			$erro .= "<br>DePara copiado com sucesso no projeto {$idProjetoAlvo}<br>";
		}
		
	}
	if(isset($_POST['stopwords'])){
		$sql = "select palavra from stopwords where idProjeto = '{$idProjeto}'";
		$result = $link->query($sql);
		$rows = resultToArray($result);
		$arrayStopwords=array();
		foreach($rows as $depara){
			$arrayStopwords[]=array("palavra"=>$depara['palavra']);
		}
		// print_r($arrayStopwords);
		foreach($_POST['projetoCopiar'] as $key => $value){
			$idProjetoAlvo = numbers($key);
			donoProjeto($idUsuario,$idProjetoAlvo,true);
			foreach($arrayStopwords as $insert){
				$palavra = sanitize($insert['palavra'],true);
				$sql = "delete from stopwords where idProjeto = '{$idProjetoAlvo}' and palavra='{$palavra}'";
				$result = $link->query($sql);
				$sql = "INSERT INTO `stopwords` (`id`, `idProjeto`, `palavra`) VALUES (NULL, '{$idProjetoAlvo}', '{$palavra}')";
				$result = $link->query($sql);
			}
			$erro .= "<br>StopWord copiado com sucesso no projeto {$idProjetoAlvo}<br>";
		}
		
	}
	
}

 ?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
<?php include("includes/head.php"); ?>
<title> <?php echo $title ?> - Copiar Entre Projetos </title>
</head>

<body>
    <!-- Left Panel -->
<?php $onde="copiarTabelas";include("includes/menu.php"); ?>
    <!-- Left Panel -->

    <!-- Right Panel -->

    <div id="right-panel" class="right-panel">

        <!-- Header-->
       <?php include("includes/header.php"); ?>
	   
	   <!-- /header -->
        <!-- Header-->

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Copiar Entre Projetos</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="main.php">Sw3t</a></li>
                            <li><a href="meusProjetos.php">Meus Projetos</a></li>
                           <!-- <li><a href="gerirProjeto.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>"><?php echo $_SESSION['nomeProjeto'] ?></a></li>-->
                            <li class="active">Copiar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">
            <div class="animated fadeIn">
			
                <div class="row">
					<div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Copiar</strong>
                            </div>
                            <div class="card-body">
							<?php echo $erro ?>
							<form method="post">
                                <div class="row" style="">
                                    <div class="col-5">
                                        <div class="copiarWrapper">
                                            <div class="copiarHeader">
                                                Copiar do <span class="copiarNome"><?php echo $nomeProjeto ?></span>
                                            </div>
                                            <div class="copiarContent">
                                                <div class="copiarRow">
                                                    <input type="checkbox" name="depara" id="depara" name="">
                                                    <label for="depara">De/para</label>
                                                </div>
                                                <div class="copiarRow">
                                                    <input type="checkbox" name="stopwords" id="stopwords" name="">
                                                    <label for="stopwords">Stopwords</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <i class="fas fa-long-arrow-alt-right" style="color: #e74c3c; font-size: 24px; padding-left: 42%;"></i>
                                    </div>
                                    <div class="col-5">
                                    <div class="copiarWrapper">
                                            <div class="copiarHeader" style="font-size: 16px; font-weight: 400;">
                                                Para os projeto(s)
                                            </div>
                                            <div class="copiarContent">
											<?php 
											$sql = "Select id,nome from projeto where idUsuario = '{$idUsuario}' and ativo = 1";
											$result = $link->query($sql);
											$rows =resultToArray($result);
											foreach($rows as $projeto){
												if($projeto['id'] == $idProjeto){continue;}
											?>
                                                <div class="copiarRow">
                                                    <input type="checkbox" name="projetoCopiar[<?php echo $projeto['id'] ?>]" id="projetoCopiar[<?php echo $projeto['id'] ?>]" name="">
                                                    <label for="projetoCopiar[<?php echo $projeto['id'] ?>]"><?php echo $projeto['nome'] ?></label>
                                                </div>
										<?php 
											}
										?>
                                            </div>
                                        </div>
                                    </div>									
                                </div>
                                <div class="row">
                                    <button type="submit" class="btn btn-success" style="align-self: flex-start; margin: 20px;">Copiar</button>
                                </div>
							</form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                
				
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->

  <?php include("includes/js.php"); ?>
   


</body>

</html>
