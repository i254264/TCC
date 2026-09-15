<?php 
require_once("config.php");
//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'],$_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);
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
<title> <?php echo $title ?> - Blank </title>
</head>

<body>
    <!-- Left Panel -->
<?php $onde="main";include("includes/menu.php"); ?>
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
                        <h1>TITULO</h1>
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
                                <strong class="card-title">TITULO</strong>
                            </div>
                            <div class="card-body">
								BODY
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
