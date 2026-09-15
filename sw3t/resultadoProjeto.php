<?php
require_once("config.php");
//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'], $_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);


$idProjeto = numbers($_GET['idProjeto']);
donoProjeto($idUsuario, $idProjeto);
$nomeProjeto = sanitize(dadosProjeto("nome", $idProjeto));
$nomeProjeto = mb_substr($nomeProjeto, 0, 20);
$_SESSION['idProjeto'] = $idProjeto;
$_SESSION['nomeProjeto'] = $nomeProjeto;

//SE TIVER ALGUM NULO, JUSTIFICATIVA PADRÃO FICA EXCLUÍDO POR TÍTULO
$sql = "update `resumo` set `justificativaStatus` = 'titulo' where (justificativaStatus is NULL or justificativaStatus = '') and idProjeto = '{$idProjeto}'";
$result = $link->query($sql);
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
	<title>
		<?php echo $title ?> : Resultado Projeto
	</title>
</head>

<body>
	<!-- Left Panel -->
	<?php $onde = "resultadoProjeto";
	include("includes/menu.php"); ?>
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
						<h1>Resultados do Projeto</h1>
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
							<li class="active">Resultados do projeto</li>
						</ol>
					</div>
				</div>
			</div>
		</div>

		<div class="content mt-3">
			<div class="animated fadeIn">
				<div class="row">
					<div class="col-lg-12">
						<!-- <div class="card">
							<div class="card-header">
								<strong class="card-title">Grafico</strong>
							</div> -->
						<div class="card-body">


							<div class="col-lg-10">
								<center>
									<?php
									$sql = "select id,descricao from fontes order by descricao asc";
									$result = $link->query($sql);
									$rows = resultToArray($result);

									foreach ($rows as $fonte) {
										$idFonte = $fonte['id'];
										$sql = "Select count(id) from resumo where idFonte = '{$idFonte}' and idProjeto = '{$idProjeto}'";
										$result = $link->query($sql);
										$row = $result->fetch_assoc();
										$qtdArtigos = $row['count(id)'];
										if ($qtdArtigos == 0) {
											continue;
										}

										echo "<h3><b>{$qtdArtigos}</b> resumos(s) mantidos(s) da fonte : {$fonte['descricao']} </h3>";
										echo "|<br>\/<br>";

										

										/*echo "<h2>Fonte ".$fonte['descricao']."</h2>";
										echo "<h3> excluidos por : </h3>";
										foreach($justificativasMotivo as $justificativa){
										echo "<b>$justificativa</b> : ";
										$sql = "Select count(id) from resumo where status='excluido' and idFonte = '{$idFonte}' and justificativaStatus = '{$justificativa}' and idProjeto = '{$idProjeto}'";
										$result = $link->query($sql);
										$row = $result->fetch_assoc();
										$qtdArtigos = $row['count(id)'];
										echo "$qtdArtigos<br>";
										}
										echo "<b> excluido sem justificativa : </b>";
										$sql = "Select count(id) from resumo where status='excluido' and idFonte = '{$idFonte}' and justificativaStatus = '' and idProjeto = '{$idProjeto}'";
										$result = $link->query($sql);
										$row = $result->fetch_assoc();
										$qtdArtigos = $row['count(id)'];
										echo "$qtdArtigos<br>";
										
										
										
										echo "<br><br><b> total excluido : </b>";
										$sql = "Select count(id) from resumo where status='excluido' and idFonte = '{$idFonte}' and idProjeto = '{$idProjeto}'";
										$result = $link->query($sql);
										$row = $result->fetch_assoc();
										$qtdArtigos = $row['count(id)'];
										echo "$qtdArtigos<br>";
										
										
										echo "<b> total incluido : </b>";
										$sql = "Select count(id) from resumo where status='incluido' and idFonte = '{$idFonte}' and idProjeto = '{$idProjeto}'";
										$result = $link->query($sql);
										$row = $result->fetch_assoc();
										$qtdArtigos = $row['count(id)'];
										echo "$qtdArtigos<br><br><br>";
										*/



									}

									foreach ($justificativasMotivo as $justificativa) {
										// echo "<b>$justificativa</b> : ";
										$sql = "Select count(id) from resumo where status='excluido' and justificativaStatus = '{$justificativa}' and idProjeto = '{$idProjeto}'";
										$result = $link->query($sql);
										$row = $result->fetch_assoc();
										$qtdArtigos = $row['count(id)'];
										//echo "$qtdArtigos<br>";
										if ($qtdArtigos == 0) {
											continue;
										}
										echo "<h4><b>{$qtdArtigos}</b> resumo(s) excluido(s) por: {$justificativa}</h4>";
										echo "|<br>\/<br>";
									}

									//areas de conhecimento
									$sql = "select distinct(area_conhecimento) from resumo where idProjeto = '{$idProjeto}' and status = 'incluido' order by area_conhecimento asc;";
									$result = $link->query($sql);
									$rows = resultToArray($result);
									$numItems = count($rows);
									$i = 0;
									echo "<h4><b>Áreas registradas des resumos mantidos: </b></h4>";
									foreach ($rows as $area) {
										echo "{$area['area_conhecimento']}";
										if (++$i != $numItems) {
											//ultimo item
											echo ", ";
										}
									}
									echo "<br>|<br>\/<br>";

									$sql = "Select count(id) from resumo where status='excluido' and (justificativaStatus is NULL or justificativaStatus = '') and idProjeto = '{$idProjeto}'";
									$result = $link->query($sql);
									$row = $result->fetch_assoc();
									$qtdArtigos = $row['count(id)'];

									if ($qtdArtigos != 0) {
										echo "<h4><b>{$qtdArtigos}</b> resumo(s) excluido(s) sem uma justificativa cadastrada</h4>";
										echo "|<br>
								    \/<br>";
									}

									$sql = "Select count(id) from resumo where status='incluido' and idProjeto = '{$idProjeto}'";
									$result = $link->query($sql);
									$row = $result->fetch_assoc();
									$qtdArtigos = $row['count(id)'];
									echo "<h3><b>{$qtdArtigos}</b> Resumo(s) mantidos</h3><br>";






									?>

								</center>
							</div>


							<div class="col-lg-2">
								<?php
								$sql = "SELECT count(id),anoPublicacao from resumo where status = 'incluido' and idProjeto = '{$idProjeto}' group by anoPublicacao order by anoPublicacao asc";
								$result = $link->query($sql);
								$rows = resultToArray($result);

								echo "<h4> Publicações por ano </h4>";
								echo "(artigos mantidos)<br>";
								foreach ($rows as $pubAno) {

									echo "<b>{$pubAno['anoPublicacao']}</b> - {$pubAno['count(id)']}<br>";

								}
								?>
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