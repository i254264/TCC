<?php 
require_once("config.php");
//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'],$_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);


$idProjeto = numbers($_GET['idProjeto']);
donoProjeto($idUsuario,$idProjeto);
$nomeProjeto = sanitize(dadosProjeto("nome",$idProjeto));
$_SESSION['nomeProjetoInteiro'] = $nomeProjeto;
$nomeProjeto = mb_substr($nomeProjeto, 0, 20);
$_SESSION['idProjeto']=$idProjeto;
$_SESSION['nomeProjeto']=$nomeProjeto;

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
<title> <?php echo $title ?> - Gerenciar Projeto </title>
</head>
<style type="text/css">
    #botoes button{
        font-size: 0.9rem;
    }
    .colunas a{
        width: fit-content;
    }
</style>

<body>
    <!-- Left Panel -->
<?php $onde="gerirProjeto";include("includes/menu.php"); ?>
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
                        <h1><!--<?php echo $_SESSION['nomeProjetoInteiro'] ?>--></h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="main.php">Sw3t</a></li>
                            <li><a href="meusProjetos.php">Meus Projetos</a></li>
                            <li class="active"><?php echo $_SESSION['nomeProjetoInteiro'] ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">
					<div class="col-lg-12">
                    <div class="card" id = "botoes">
                            <div class="card-header">
                                <strong class="card-title">Gerenciar Projeto</strong>
                            </div>
                            <div class=" row flex card-body">
							    <div class="col colunas">
									<a href= "adicionarArtigos.php?idProjeto=<?php echo $idProjeto ?>"><button type="button" class="btn btn-success btn-lg btn-block">Adicionar Resumo</button></a>
									<a href= "verificarDuplicados.php?idProjeto=<?php echo $idProjeto ?>"><button type="button" class="btn btn-success btn-lg btn-block">Verificar Duplicados</button></a>
									<a href= "gerirArtigos.php?idProjeto=<?php echo $idProjeto ?>"><button type="button" class="btn btn-success btn-lg btn-block">Todos os Resumos</button></a>
									<a href= "pesquisarArtigos.php?idProjeto=<?php echo $idProjeto ?>"><button type="button" class="btn btn-success btn-lg btn-block">Pesquisar Resumos</button></a>
								</div>
								<div class="col  colunas">
									<a href= "bancosUsuario.php?idProjeto=<?php echo $idProjeto ?>"><button type="button" class="btn btn-warning btn-lg btn-block">Atualizar Lista de Base de Dados</button></a>
									<a href= "gerirDePara.php?idProjeto=<?php echo $idProjeto ?>"><button type="button" class="btn btn-warning btn-lg btn-block">Atualizar Lista de Termos De/Para</button></a>
									<a href= "gerirStopWords.php?idProjeto=<?php echo $idProjeto ?>"><button type="button" class="btn btn-warning btn-lg btn-block">Atualizar Lista de StopWords</button></a>
								</div>
								<div class="col colunas">
									<a href= "editarProjeto.php?idProjeto=<?php echo $idProjeto ?>"><button type="button" class="btn btn-danger btn-lg btn-block">Atualizar Informações do Projeto</button></a>
									<a href= "analisar.php?idProjeto=<?php echo $idProjeto ?>"><button type="button" class="btn btn-danger btn-lg btn-block">Fazer Análise Resumo e Definir Tópicos</button></a>
									<a href= "analises.php?idProjeto=<?php echo $idProjeto ?>"><button type="button" class="btn btn-danger btn-lg btn-block">Análises Concluídas</button></a>
									<a href= "resultadoProjeto.php?idProjeto=<?php echo $idProjeto ?>"><button type="button" class="btn btn-danger btn-lg btn-block">Total de Resumos Utilizados na Última Análise</button></a>
                                    
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
