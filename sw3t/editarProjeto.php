<?php 
require_once("config.php");
//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'],$_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);

$idProjeto = numbers($_GET['idProjeto']);

donoProjeto($idUsuario,$idProjeto);

$erro ="";

if(empty($_SESSION['confirmacao'])){
    $_SESSION['confirmacao'] = rand(10000,99999);
}

if(isset($_POST['descricao'])){
//    $sql = "select dataInicio,dataFim,descricao,modeloPico,questoesDePesquisa,objetivoGeral,proposicoes,descricaoTermosBusca,relacaoBasesConsultadas from projeto where id ='{$idProjeto}'";
	$descricao = sanitize($_POST['descricao']);
	$modeloPico = sanitize($_POST['modeloPico']);
	$questoesDePesquisa = sanitize($_POST['questoesDePesquisa']);
	$objetivoGeral = sanitize($_POST['objetivoGeral']);
	$proposicoes = sanitize($_POST['proposicoes']);
	$descricaoTermosBusca = sanitize($_POST['descricaoTermosBusca']);
	$relacaoBasesConsultadas = sanitize($_POST['relacaoBasesConsultadas']);
	$sql = "update projeto set descricao = '{$descricao}',modeloPico = '{$modeloPico}',questoesDePesquisa = '{$questoesDePesquisa}',objetivoGeral = '{$objetivoGeral}',proposicoes = '{$proposicoes}',descricaoTermosBusca = '{$descricaoTermosBusca}',relacaoBasesConsultadas = '{$relacaoBasesConsultadas}' where id = '{$idProjeto}'";
    $result = $link->query($sql);
	
}

if(isset($_POST['verificaE'])){
    $verifica = $_POST["verificaE"];
    if($verifica != $_SESSION['confirmacao']){
      
        $erro = "confirmacao invalida";
    }else{
        //esconder projeto
        $sql = "update projeto set ativo = 0 where id = '{$idProjeto}'";
        $result = $link->query($sql);
        unset($_SESSION['idProjeto']);
        unset($_SESSION['nomeProjeto']);
        header("location: {$sw3tUrl}/meusProjetos.php");die;
    }
    
}
if(isset($_REQUEST['print'])){
    header('Content-Disposition: attachment; filename="projeto_'.$idProjeto.'.tsv"');
    header("Content-Type: text/tab-separated-values");
    $sql = "select dataInicio,dataFim,descricao,modeloPico,questoesDePesquisa,objetivoGeral,proposicoes,descricaoTermosBusca,relacaoBasesConsultadas from projeto where id ='{$idProjeto}'";
    $result = $link->query($sql);
    $row = $result->fetch_assoc();
    foreach($row as $value => $item){
        $item = str_replace("\t",' ',$item);
        echo $value."\t".$item.PHP_EOL;
    }


    die;
}


  $_SESSION['confirmacao'] = rand(10000,99999);

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
<title> <?php echo $title ?> - Editar Projeto </title>
</head>

<body>
    <!-- Left Panel -->
<?php $onde="editarProjeto";include("includes/menu.php"); ?>
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
                        <h1>Editar Projeto</h1>
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
                            <li class="active">Pagina</li>
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
                                <strong class="card-title">Configurações</strong>
                            </div>
                            <div class="card-body">
                                <h2><?php echo $erro ?></h2>
								<?php
								$sql = "select dataInicio,dataFim,descricao,modeloPico,questoesDePesquisa,objetivoGeral,proposicoes,descricaoTermosBusca,relacaoBasesConsultadas from projeto where id ='{$idProjeto}'";

								$result = $link->query($sql);
								$row = $result->fetch_assoc();
								$descricao = $row['descricao'];
								?>
								
								<form method="post">
								Data Inicio : <?php echo $row['dataInicio'] ?> <br>
								Data Fim : <?php echo $row['dataFim'] ?> </br>
									<div class="row my-2">
										<div class="col-md-3">Descrição:</div><br><br>
                                        <div class="col-md-9"><textarea name="descricao" style="width:100%"><?php echo $descricao ?></textarea></div>
									</div>
									<div class="row my-2">
										<div class="col-md-3">Modelo Pico:</div><br><br>
                                        <div class="col-md-9"><textarea name="modeloPico" style="width:100%"><?php echo $row['modeloPico'] ?></textarea></div>
									</div>
									<div class="row my-2">
										<div class="col-md-3">Questoes De Pesquisa:</div><br><br>
                                        <div class="col-md-9"><textarea name="questoesDePesquisa" style="width:100%"><?php echo $row['questoesDePesquisa'] ?></textarea></div>
									</div>
									<div class="row my-2">
										<div class="col-md-3">Objetivo Geral:</div><br><br>
                                        <div class="col-md-9"><textarea name="objetivoGeral" style="width:100%"><?php echo $row['objetivoGeral'] ?></textarea></div>
									</div>
									<div class="row my-2">
										<div class="col-md-3">Proposições ou Hipóteses:</div><br><br>
                                        <div class="col-md-9"><textarea name="proposicoes" style="width:100%"><?php echo $row['proposicoes'] ?></textarea></div>
									</div>
									<div class="row my-2">
										<div class="col-md-3">Descrição dos Termos de busca:</div><br><br>
                                        <div class="col-md-9"><textarea name="descricaoTermosBusca" style="width:100%"><?php echo $row['descricaoTermosBusca'] ?></textarea></div>
									</div>
									<div class="row my-2">
										<div class="col-md-3">Relação de Bases de Dados consultadas:</div><br><br>
                                        <div class="col-md-9"><textarea name="relacaoBasesConsultadas" style="width:100%"><?php echo $row['relacaoBasesConsultadas'] ?></textarea></div>
									</div>




                                    <br>
                                    <div class="row">
                                        <div class="col-6"><input type="submit" class="btn btn-success" value="Salvar" name="Salvar"></div>
                                        <div class="col-6"><a href="?idProjeto=<?php echo $idProjeto ?>&print=1" class="btn btn-info" target="_blank">Imprimir dados do projeto</a></div>
                                    </div>
									<br>
								</form>
								<br>
								<br>
								<form method="post">
                                Para Excluir esse projeto digite: <b><?php echo $_SESSION['confirmacao']; ?></b> no campo abaixo e clique em excluir:<br>
								
                                    <input type="text" name="verificaE"><br><br>
                                    <input type="submit" class="btn btn-danger" value="Excluir" name="Excluir"><br>
                                </form>
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
