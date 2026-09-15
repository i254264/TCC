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
<title> <?php echo $title ?> - Limpar Artigos </title>
</head>

<body>
    <!-- Left Panel -->
<?php $onde="limparArtigos";include("includes/menu.php"); ?>
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
                        <h1>Limpar Artigos</h1>
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
                            <li class="active">Limpar Artigos</li>
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
                                <strong class="card-title">Limpar Artigos</strong>
                            </div>
                            <div class="card-body">
							
<!--
$search = "/[^&copy;](.*)[^.]/";<br>
echo preg_replace($search,"",$string);-->
										
							Remover tudo entre &copy; e .
							<br><br>
							<button type="button" id = "artigosLimpos" onclick="artigosLimpos();" class="btn btn-primary btn-lg btn-block">Gerar Artigos Limpos</button>
							<br>
							<!--<br>
							<br>
							
							<a href= "artigosLimpos.php?idProjeto=<?php echo $idProjeto ?>" class="btn btn-info btn-lg btn-block"> Textos removidos dos artigos </a>-->
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- .animated -->
        </div><!-- .content -->

<script>
function artigosLimpos(){
		 document.getElementById('artigosLimpos').innerHTML="Carregando ..."; 
		document.getElementById("artigosLimpos").className = "btn btn-secondary btn-lg btn-block";
		$.ajax({
            type: 'POST',
            url: 'ajaxLimpar.php',
            data: "idProjeto=<?php echo $idProjeto ?>&limpar=true",
            success: function(data) {
               eval(data);
            }
        });
		
	}

</script>
    </div><!-- /#right-panel -->

    <!-- Right Panel -->

  <?php include("includes/js.php"); ?>
   


</body>

</html>
