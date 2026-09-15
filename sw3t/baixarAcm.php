<?php 
require_once("config.php");

checar($_SESSION['email'],$_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);

	
$idProjeto = numbers($_GET['idProjeto']);


donoProjeto($idUsuario,$idProjeto);
$nomeProjeto = sanitize(dadosProjeto("nome",$idProjeto));
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
<title> <?php echo $title ?> - Ver Artigo </title>
</head>

<body>
    <!-- Left Panel -->
<?php $onde="baixarAcm";include("includes/menu.php"); ?>
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
                        <h1>Baixar ACM</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="main.php">Sw3t</a></li>
                            <li><a href="meusProjetos.php">Meus Projetos</a></li>
							<li><a href="gerirProjeto.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>"><?php echo $_SESSION['nomeProjeto'] ?></a></li>
							<li><a href="gerirArtigo.php">Gerenciar Artigos</a></li>
                            <li class="active">Baixar Acm</li>
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
							 <div class="card-body">
							 <script>
									function baixarTodos(){
		
		document.getElementById('botaoTudo').innerHTML="Carregando ..."; 
		
		$.ajax({
            type: 'POST',
            url: 'ajaxAcm.php',
		data: 'idProjeto=<?php echo $idProjeto ?>&baixarTodos=true',
            success: function(data) {
                eval(data);
            }
        });
		
		
	}
</script>
									<?php
									$sql = "select count(idAcm) from resumo where idProjeto = '18' and idAcm != 0";
$result = $link->query($sql);
$row = $result->fetch_assoc();
$quantos = $row['count(idAcm)'];
?>
			<h2>Fora detectado <?php echo $quantos ?> Artigos da acm, deseja baixar ? <button onclick='baixarTodos();baixarTodos();' id='botaoTudo' style='color:white;' class='btn btn-success'>Baixar Tudo</button></h2><br><div id='resultAcm'></div><br><div id='falhasAcm'></div>	
								
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
