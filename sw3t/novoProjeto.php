<?php 
require_once("config.php");
//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'],$_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);



$erro="";

if((isset($_POST['nome']))and (isset($_POST['dataFim'])) ){
	
	$nome = sanitize($_POST['nome']);
	$dataFim = sanitize($_POST['dataFim']);
	$descricao = sanitize($_POST['descricao']);
	
$sql = "INSERT INTO `projeto` (`id`, `idUsuario`, `nome`, `ativo`, `dataInicio`, `dataFim`, `descricao`) VALUES (NULL, '$idUsuario', '$nome', '1', CURRENT_TIME(), '$dataFim', '$descricao')";
	$result = $link->query($sql);
	$idProjeto = mysqli_insert_id($link);
if(empty($idProjeto)){
	$erro = "Ocorreu um erro ao cadastrar esse projeto :<";
}else{
	header("location: gerirProjeto.php?idProjeto=".$idProjeto);
	die;
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
<title> <?php echo $title ?> - Novo Projeto </title>
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
                        <h1>Novo Projeto</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="main.php">Sw3t</a></li>
                            <li><a href="meusProjetos.php">Meus Projetos</a></li>
                            <li class="active">Novo Projeto</li>
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
                                <strong class="card-title">Criar Novo Projeto</strong>
                            </div>
                            <div class="card-body">
								<form method="post">
								<?php echo $erro ?><br>
								
								<div class="row form-group">
								<div class="col col-md-3"><label for="text-input" class=" form-control-label">Nome do Projeto</label></div>
								<div class="col-12 col-md-9"><input type="text" id="text-input" name="nome" placeholder="Nome" class="form-control" ><small class="form-text text-muted">Insira um nome para o seu projeto</small></div>
								</div>
									
								<div class="row form-group">
								<div class="col col-md-3"><label for="text-input" class=" form-control-label">Insira uma data de fim para o projeto</label></div>
								<div class="col-12 col-md-9"><input type="date" id="text-input" name="dataFim" class="form-control" ><small class="form-text text-muted">Insira uma data para tornar o projeto inativo</small></div>
								</div>
								
								<div class="row form-group">
								<div class="col col-md-3"><label for="text-input" class=" form-control-label">Descrição do projeto</label></div>
								<div class="col-12 col-md-9"><textarea name="textarea-input" id="descricao" rows="9" placeholder="Descrição..." class="form-control"></textarea></div>
								</div>
									
								
								
                            </div>
							<div class="card-footer">
							<button type="submit" class="btn btn-primary btn-sm">
								<i class="fa fa-dot-circle-o"></i> Cadastrar
							</button>
							<button type="reset" class="btn btn-danger btn-sm">
								<i class="fa fa-ban"></i> Resetar
							</button>
							</div>
													</form>
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
