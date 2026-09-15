<?php 
require_once("config.php");

checar($_SESSION['email'],$_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);

$idArtigo = numbers($_GET['idArtigo']);


$sql = "select idProjeto from resumo where id = $idArtigo";

$result = $link->query($sql);
$row = $result->fetch_assoc();
	
$idProjeto = numbers($row['idProjeto']);


donoProjeto($idUsuario,$idProjeto);
$nomeProjeto = sanitize(dadosProjeto("nome",$idProjeto));
$nomeProjeto = mb_substr($nomeProjeto, 0, 20);
$_SESSION['idProjeto']=$idProjeto;
$_SESSION['nomeProjeto']=$nomeProjeto;



if(isset($_GET['deletar'])){
	$sql = "delete from resumo where id = '{$idArtigo}' ";
				$result = $link->query($sql);
					$sql = "delete from autores where idResumo = '{$idArtigo}' ";
				$result = $link->query($sql);
				if(isset($_GET['close'])){
					   echo "
					    document.getElementById($idArtigo).innerHTML='';
					   var element = document.getElementById($idArtigo);
					element.parentNode.removeChild(element);
					
					";
					die;
				}
				header("location:gerirArtigos.php?idProjeto=".$idProjeto);die;
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
<title> <?php echo $title ?> - Ver Resumo </title>
</head>

<body>
    <!-- Left Panel -->
<?php $onde="gerirArtigos";include("includes/menu.php"); ?>
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
                        <h1>Ver Resumo</h1>
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
							<li><a href="gerirArtigo.php">Gerenciar Resumos</a></li>
                            <li class="active">Ver Resumo</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">
					<div class="col-lg-12">
					<?php
					$sql = "select resumo,resumoEnxuto,tituloArtigo from resumo where id = $idArtigo";

					$result = $link->query($sql);
					$row = $result->fetch_assoc();
					$resumo = ($row['resumo']);
					$resumoEnxuto = ($row['resumoEnxuto']);
					$titulo = ($row['tituloArtigo']);
					$tamanhoResumo = strlen($resumo);
					$tamanhoResumoEnxuto = strlen($resumoEnxuto);
					?>
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Título do resumo:</strong> <?php echo $titulo ?>
                            </div>
                            <div class="card-body">
								<div class="row">
									<div class="col-lg-6"><h4>Resumo Original</h4><br><?php echo $resumo ?></div>
									<div class="col-lg-6"><h4>Resumo Enxuto<br><small class="text-muted">Com troca de termos e eliminação de stopwords do usuário</small></h4><p><?php echo $resumoEnxuto ?></p></div>
								</div>
								
								<div class="row">
									<div class="col-lg-6"><p><?php echo $tamanhoResumo ?> Caracteres</p></div>
									<div class="col-lg-6"><p><?php echo $tamanhoResumoEnxuto ?> Caracteres</p></div>
								</div>
								<div class="row">
								<div class="col-lg-12"><h4>Resumo Lematizado<br><small class="text-muted">Sem stopwords do sistema e termos simplificados</small></h4><p><?php echo dadosResumo("resumoLematizado",$idArtigo) ?></p></div>
								</div>
								
								
								
                            </div>
                        </div>
                    </div>
					
					

                </div>
				
				<div class="row">
						<div class="col-lg-12">
						 <div class="card">
							 <div class="card-body">
								<center>
									<button type="button" style="width:80% !important;font-size: 2rem !important;" id="enxuto" onclick="resumoEnxuto();" class="btn btn-primary btn-lg btn-block">Gerar Resumos Enxutos</button>
								<script>
								function resumoEnxuto(){
		 document.getElementById('enxuto').innerHTML="Carregando ..."; 
		document.getElementById("enxuto").className = "btn btn-secondary btn-lg btn-block";
		$.ajax({
            type: 'POST',
            url: 'ajaxPython.php',
            data: "idProjeto=13&enxuto=true",
            success: function(data) {
               eval(data);
            }
        });
		
	}
	</script>
								
								</center>
								
								</div>
							</div>
						</div>
					</div>
					
					
					
					<div class="row">
						<div class="col-lg-12">
						 <div class="card">
							 <div class="card-body">
								<center>
									<a href="verArtigo.php?idArtigo=<?php echo $idArtigo ?>&deletar=true"><button style="width:80% !important;font-size: 2rem !important;"type="button" class="btn btn-danger btn-sm"><i class="fa fa-dot-circle-o"></i> Deletar Artigo</button></a>
								
								</center>
								
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
