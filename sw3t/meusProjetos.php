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
<title> <?php echo $title ?> - Meus Projetos </title>
</head>

<body>
    <!-- Left Panel -->
<?php $onde="meusProjetos"; include("includes/menu.php"); ?>
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
                        <h1>Meus Projetos</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="main.php">Sw3t</a></li>
                            <li class="active">Meus Projetos</li>
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
                            <div class="card-header d-flex justify-content-between align-middle">
                                <strong class="card-title" style="margin: 3px 0px 0px 0px;">Projetos</strong>
                                <a href="novoProjeto.php"><button type="button" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i></button></a>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Nome</th>
                                            <th scope="col">Descrição</th>
                                            <th scope="col">Fim Projeto</th>
											<th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
										$sql = "Select id,nome,descricao,dataFim from projeto where idUsuario = '{$idUsuario}' and ativo = 1";
				$result = $link->query($sql);
				$rows =resultToArray($result);
					
					$i=0;
					echo "<div style='margin-top: inherit;' class='row'>";
				foreach ($rows as $projeto){
					$idProjeto = $projeto['id'];
					$nomeProjeto = $projeto['nome'];
					$descricaoProjeto = $projeto['descricao'];
					$dataFimProjeto = $projeto['dataFim'];
					
					$dataFimProjeto = strtotime($dataFimProjeto);
					if($dataFimProjeto > 10000){
					$dataFimProjeto = date("d/m/Y",$dataFimProjeto);
					}else{
						$dataFimProjeto = "Sem Fim";
					}
					?>
                                        <tr>
                                            <th scope="row"><?php echo $idProjeto ?></th>
                                            <td><?php echo $nomeProjeto ?></td>
                                            <td><?php echo $descricaoProjeto ?></td>
                                            <td><?php echo $dataFimProjeto ?></td>
                                            <td>
											<a href="gerirProjeto.php?idProjeto=<?php echo $idProjeto ?>"><button type="button" class="btn btn-primary btn-sm"><i class="fa fa-cog"></i> Gerenciar</button></a>
											<a href="editarProjeto.php?idProjeto=<?php echo $idProjeto ?>" class="btn btn-success btn-sm"><i class="fa fa-cog"></i> Dados do Projeto</a>
											</td>
                                        </tr>
                                        <?php 
										
				} ?>
                                    </tbody>
                                </table>

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
