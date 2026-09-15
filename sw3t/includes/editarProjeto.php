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
                                <strong class="card-title">Configuracoes</strong>
                            </div>
                            <div class="card-body">
                                <h2><?php echo $erro ?></h2>
                            
								<form method="post">
                                Para Excluir esse projeto digite :<b><?php echo $_SESSION['confirmacao']; ?></b><br> No campo abaixo e clique em excluir<br>
                                    <input type="text" name="verificaE"><br>
                                    <input type="submit" class="btn btn-danger btn-lg btn-block" name="Excluir"><br>
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
