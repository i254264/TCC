<?php 
require_once("config.php");
//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'],$_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);

if(isset($_POST['funcao']) or isset($_POST['empresa']) or isset($_POST['telefone'])){
	$funcao = sanitize($_POST['funcao']);
	$empresa = sanitize($_POST['empresa']);
	$telefone = sanitize($_POST['telefone']);
	$sql = "UPDATE `usuario` SET `tel` = '{$telefone}', `funcao` = '{$funcao}', `empresa` = '{$empresa}' WHERE `usuario`.`id` = {$idUsuario} ";
	// echo $sql;die;
	$result = $link->query($sql);
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
<title> <?php echo $title ?> - Perfil </title>
</head>

<body>
    <!-- Left Panel -->
<?php $onde="perfil";include("includes/menu.php"); ?>
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
                        <h1>Perfil</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="main.php">Sw3t</a></li>
                            <li><a href="perfil.php">Perfil</a></li>
                          
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
                                <strong class="card-title">Detalhes Perfil</strong>
                            </div>
							<form method="post">
                            <div class="card-body">
							
								<?php 
								$sql = "select email,funcao,empresa,tel from usuario where id = '{$idUsuario}'";
								$result = $link->query($sql);
								$row = $result->fetch_assoc();
								?>
								<div class="row form-group">
								<div class="col col-md-3"><label for="text-input" class=" form-control-label">Email</label></div>
								<div class="col-12 col-md-9"><input type="text" disabled value="<?php echo $row['email'] ?>" class="form-control" ><small class="form-text text-muted">Email Cadastrado</small></div>
								</div>
								
								<div class="row form-group">
								<div class="col col-md-3"><label for="text-input" class=" form-control-label">Função</label></div>
								<div class="col-12 col-md-9"><input type="text" name="funcao" value="<?php echo $row['funcao'] ?>" class="form-control" ><small class="form-text text-muted">Função Cadastrada</small></div>
								</div>
								
								<div class="row form-group">
								<div class="col col-md-3"><label for="text-input" class=" form-control-label">Empresa</label></div>
								<div class="col-12 col-md-9"><input type="text" name="empresa" value="<?php echo $row['empresa'] ?>" class="form-control" ><small class="form-text text-muted">Empresa Cadastrada</small></div>
								</div>
								
								<div class="row form-group">
								<div class="col col-md-3"><label for="text-input" class=" form-control-label">Telefone</label></div>
								<div class="col-12 col-md-9"><input type="text" name="telefone" value="<?php echo $row['tel'] ?>" class="form-control" ><small class="form-text text-muted">Telefone Cadastrado</small></div>
								</div>
						
								
                            </div>
							<div class="card-footer">
							<button type="submit" class="btn btn-success btn-sm">
								<i class="fa fa-dot-circle-o"></i> Salvar
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
