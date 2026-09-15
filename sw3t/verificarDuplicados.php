<?php
require_once("config.php");

checar($_SESSION['email'], $_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);

function console_log($dados) {
    printf('<script>console.log(%s);</script>', json_encode($dados));
}

/////VER NOME DAS TABELAS
try {
	$sql = "SELECT COLUMN_NAME from information_schema.columns WHERE table_name = 'resumo'";
	$result = $link->query($sql);
	$rows = resultToArray($result);
	foreach ($rows as $row) {
		console_log($row['COLUMN_NAME']);
	}
} catch (Exception $e) {
	console_log('Exceção capturada: '.$e->getMessage()."\n");
}
/////

$idProjeto = numbers($_GET['idProjeto']);
$quantosDuplicados = "";

donoProjeto($idUsuario, $idProjeto);
$nomeProjeto = sanitize(dadosProjeto("nome", $idProjeto));
$nomeProjeto = mb_substr($nomeProjeto, 0, 20);
$_SESSION['idProjeto'] = $idProjeto;
$_SESSION['nomeProjeto'] = $nomeProjeto;

//***********************************
//DUPLICADOS POR TÍTULO, RESUMO E ANO
//***********************************
//isset($_GET['removerAuto'])
if (isset($_GET['removerAuto'])) {
	$sql = "
SELECT 
	`tituloArtigo`,`anoPublicacao`,`area_conhecimento`,`resumo`,count(resumo)
FROM
    resumo
	 where idProjeto = {$idProjeto} and status = 'incluido'
GROUP BY resumo
HAVING COUNT(resumo) > 1;
";
	// echo $sql;die;
// 1 	IEEE  1	
// 2 	ACM 	2
// 3 	WOS 	4
// 4 	SCOPUS 	3
// 5 	SCIELO  
	$quantosDuplicados = 0;
	$result = $link->query($sql);
	$rows = resultToArray($result);
	$duplicados = array();
	foreach ($rows as $artigo) {
		$tituloArtigo = sanitize($artigo['tituloArtigo'], true);
		$anoPublicacao = sanitize($artigo['anoPublicacao'], true);
		$resumo = sanitize($artigo['resumo'], true);

		$sql = 'select id, idFonte, `area_conhecimento` from resumo where idProjeto = ' . $idProjeto . ' and status = "incluido" and resumo = "' . $resumo . '" and anoPublicacao = ' . $anoPublicacao . ' and tituloArtigo = "' . $tituloArtigo . '"';
		// echo $sql;die;
		$result = $link->query($sql);

		//PARTE QUE CRIA ORDEM DE EXCLUSÃO
		$areas = array();
		foreach ($result as $value) {
			array_push($areas, $value['area_conhecimento']);
		}
		;

		//Verifica se todas as áreas são iguais
		$status = 1;
		foreach ($areas as $value) {
			if ($areas[0] != $value) {
				$status = 0;
				break;
			}
		}

		//variavel que testa se passou em exclusao de exatas ou humanas
		$exclusaoArea = 0;

		//seleciona por area de conhecimento para excluir
		if ($status == 0) {
			if (in_array("exatas", $areas)) {
				$sql = 'select tituloArtigo, id, idFonte, `area_conhecimento` from resumo where idProjeto = ' . $idProjeto . ' and status = "incluido" and resumo = "' . $resumo . '" and anoPublicacao = ' . $anoPublicacao . ' and tituloArtigo = "' . $tituloArtigo . '" and `area_conhecimento` != "exatas"';
				$exclusaoArea = 1;
			} else {
				if (in_array("humanas", $areas)) {
					$sql = 'select tituloArtigo, id, idFonte, `area_conhecimento` from resumo where idProjeto = ' . $idProjeto . ' and status = "incluido" and resumo = "' . $resumo . '" and anoPublicacao = ' . $anoPublicacao . ' and tituloArtigo = "' . $tituloArtigo . '" and `area_conhecimento` != "humanas"';
					$exclusaoArea = 1;
				}
			}
		} else {
			$sql = 'select tituloArtigo, id, idFonte, `area_conhecimento` from resumo where idProjeto = ' . $idProjeto . ' and status = "incluido" and resumo = "' . $resumo . '" and anoPublicacao = ' . $anoPublicacao . ' and tituloArtigo = "' . $tituloArtigo . '"';
		}

		$result = $link->query($sql);
		$artigosDups = resultToArray($result);

		////se retornou apenas 1 resultado e passou pela exclusao de areas, então tem que excluir esse item
		$itemExcluir = 0;
		if ((count($artigosDups) == 1) && ($exclusaoArea == 1)) {
			$itemExcluir = 1;
		}
		////
		$duplicados = array();
		$i = 0;
		$ids = array();

		foreach ($artigosDups as $artigoDup) {
			$i++;
			$duplicados[$artigoDup['idFonte']][] = $artigoDup['id'];
			$ids[] = $artigoDup['id'];
		}

		$quantIdFonte = (int) $artigoDup['idFonte'];

		//prioridade por id
		for ($i = 0; $i <= $quantIdFonte; $i++) {
			//echo '$prioridade['.$i.'] = $duplicados['.$i.'][0]<br>';
			$prioridade[$i] = $duplicados[$i][0];
		}
		$selecionado = "";
		for ($i = 0; $i <= $quantIdFonte; $i++) {
			if (!empty($prioridade[$i])) {
				//echo '$selecionado = $prioridade['.$i.']<br>';
				$selecionado = $prioridade[$i];
				//echo $selecionado, "<br><br>";
			}
		}

		foreach ($ids as $deletar) {
			//passa direto e não exclue
			if (($deletar == $selecionado) && ($itemExcluir == 0)) {
				continue;
			}
			try {
				$sql = "UPDATE `resumo` SET `status` = 'excluido', `justificativaStatus` = 'duplicado', `descricaoStatus` = 'remoção por duplicado titulo, resumo e ano' WHERE `id` = {$deletar} ";
				$result = $link->query($sql);
				$quantosDuplicados++;
			} catch (Exception $e) {
				console_log('Exceção capturada: '.$e->getMessage()."\n");
			}
		}

	}

	$quantosDuplicados = "<h6>Foram excluidos {$quantosDuplicados} duplicados</h6>";
}
// if (isset($_GET['removerAutoTit'])) {

// 	$sql = "
// SELECT 
//     tituloArtigo, anoPublicacao,
//     COUNT(anoPublicacao)
// FROM
//     resumo
// 	 where idProjeto = {$idProjeto} and status = 'incluido'
// GROUP BY tituloArtigo,anoPublicacao
// HAVING COUNT(anoPublicacao) > 1;
// ";

// 	// echo $sql;die;
// // 1 	IEEE  1	
// // 2 	ACM 	2
// // 3 	WOS 	4
// // 4 	SCOPUS 	3
// // 5 	SCIELO  
// 	$quantosDuplicados = 0;
// 	$result = $link->query($sql);
// 	$rows = resultToArray($result);
// 	$duplicados = array();
// 	foreach ($rows as $artigo) {
// 		$tituloArtigo = sanitize($artigo['tituloArtigo'], true);

// 		$sql = "select id,idFonte from resumo where idProjeto = {$idProjeto} and status = 'incluido' and tituloArtigo = '{$tituloArtigo}'";
// 		// echo $sql;die;
// 		$result = $link->query($sql);
// 		$artigosDups = resultToArray($result);
// 		$duplicados = array();
// 		$i = 0;
// 		$ids = array();
// 		foreach ($artigosDups as $artigoDup) {
// 			$i++;
// 			$duplicados[$artigoDup['idFonte']][] = $artigoDup['id'];
// 			$ids[] = $artigoDup['id'];
// 		}
// 		//prioridade por id : 1 > 2 > 3 > 4 >5
// 		$prioridade = array();
// 		$prioridade['1'] = $duplicados['1'][0];
// 		$prioridade['2'] = $duplicados['2'][0];
// 		$prioridade['3'] = $duplicados['3'][0];
// 		$prioridade['4'] = $duplicados['4'][0];
// 		$prioridade['5'] = $duplicados['5'][0];
// 		$selecionado = "";
// 		if (!empty($prioridade['5'])) {
// 			$selecionado = $prioridade['5'];
// 		}
// 		if (!empty($prioridade['4'])) {
// 			$selecionado = $prioridade['4'];
// 		}
// 		if (!empty($prioridade['3'])) {
// 			$selecionado = $prioridade['3'];
// 		}
// 		if (!empty($prioridade['2'])) {
// 			$selecionado = $prioridade['2'];
// 		}
// 		if (!empty($prioridade['1'])) {
// 			$selecionado = $prioridade['1'];
// 		}


// 		foreach ($ids as $deletar) {
// 			if ($deletar == $selecionado) {
// 				continue;
// 			}
// 			$sql = "UPDATE `resumo` SET `status` = 'excluido', `justificativaStatus` = 'duplicado titulo', `descricaoStatus` = 'remoção duplicado automatica titulo' WHERE `resumo`.`id` = {$deletar} ";
// 			$result = $link->query($sql);
// 			$quantosDuplicados++;
// 		}


// 	}

// 	$quantosDuplicados = "<h3>Foram excluidos {$quantosDuplicados} duplicados</h3>";
// }

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
		<?php echo $title ?> - Ver Artigo
	</title>
</head>
<style>
	ul {
		margin: 0px;
	}

	li {
		list-style: none;
	}

	.li {
		margin-left: 2.5em;
		list-style-type: lower-alpha;
	}
</style>

<body>
	<!-- Left Panel -->
	<?php $onde = "verificarDuplicados";
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
						<h1>Ver Duplicadas</h1>
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
							<li class="active">Ver Duplicados</li>
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
							<div class="card-body text-primary ">
								<div>
									<p class="h5">Identifica registros duplicados por:</p>
									<small class="row">
										<div class="col-auto">
											1) Duplica Total: exclusão coletiva ou individual
											<ul>
												<li class="li"> Título, Ano e Resumo</li>
											</ul>
										</div>
										<div class="col-auto">
											2) Duplicado Parcial : individual
											<ul>
												<li class="li"> Título e Ano</li>
												<li class="li"> Título e Resumo</li>
												<li class="li"> Resumo e Ano</li>
											</ul>
										</div>
									</small>
								</div>
								<?php
								if (isset($quantosDuplicados)) {
									echo "
										<div class='text-danger font-weight-bold text-center mt-1'>
											<br>$quantosDuplicados
										</div>
										";
								}
								?>
							</div>
						</div>
					</div>
				</div>


				<div class="row">
					<div class="col-lg-12">
						<div class="card">
							<div class="card-header d-flex justify-content-between">
								<div>
									<strong>1) Duplica Total: exclusão coletiva ou individual</strong>
									<ul>
										<li class="li"> Título, Ano e Resumo</li>
									</ul>
								</div>
								<div>
									<a href="verificarDuplicados.php?idProjeto=<?php echo $idProjeto ?>&removerAuto=true"
										class="btn btn-danger btn-sm">Excluir duplicados Total </a>
									<small class="form-text text-muted">Exclusão Coletiva</small>
								</div>
							</div>
							<div class="card-body">
								<table id="bootstrap-data-table-export" class="table table-striped table-bordered">
									<thead>
										<tr>
											<th>Titulo</th>
											<th>Quantidade Duplicados</th>
											<th>#</th>
										</tr>
									</thead>
									<tbody>
										<?php
										// Seleciona caso tituloArtigo, anoPublicacao  e resumo sejam iguais
										$sql = "
SELECT 
	tituloArtigo, anoPublicacao,resumo,
	COUNT(tituloArtigo)
FROM
    resumo
	 where idProjeto = {$idProjeto} and status = 'incluido'
GROUP BY tituloArtigo, anoPublicacao,resumo
HAVING COUNT(tituloArtigo) > 1;
";
										$result = $link->query($sql);
										$rows = resultToArray($result);

										foreach ($rows as $artigo) {
											// $nomeArtigo = mb_substr($artigo['tituloArtigo'], 0, 30);
											$tituloArtigo = $artigo['tituloArtigo'];
											$countTitulo = $artigo['COUNT(tituloArtigo)'];
											$pesquisa = base64_encode($tituloArtigo);

											?>
											<tr>
												<td>
													<?php echo $tituloArtigo ?>
												</td>
												<td>
													<?php echo $countTitulo ?>
												</td>

												<td>
													<center>
														<a
															href="verDuplicado.php?idProjeto=<?php echo $idProjeto ?>&titulo=true&pesquisa=<?php echo $pesquisa ?>"><button
																style="width: 100%;" type="button"
																class="btn btn-primary btn-sm"><i
																	class="fa fa-dot-circle-o"></i> Ver</button></a>
														<!--<button style="width: 100%;" onclick = "deletar()" type="button" class="btn btn-danger btn-sm"><i class="fa fa-dot-circle-o"></i> Duplicado</button>-->
													</center>
												</td>

											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>


				<div class="row">
					<div class="col-lg-12">
						<div class="card">
							<div class="card-header d-flex justify-content-between">
								<div>
									<strong>2) Duplicado Parcial : individual</strong>
									<ul>
										<li class="li"> Título e Ano</li>
										<li class="li"> Título e Resumo</li>
										<li class="li"> Resumo e Ano</li>
									</ul>
								</div>
								<!-- <a href="verificarDuplicados.php?idProjeto=<?php echo $idProjeto ?>&removerAutoTit=true" class="btn btn-danger btn-sm">Remover Todos Duplicados (por resumo)</a> -->
							</div>
							<div class="card-body">
								<table id="bootstrap-data-table-export2" class="table table-striped table-bordered">
									<thead>
										<tr>
											<th>Título</th>
											<th>Quantidade Duplicados</th>
											<th>#</th>
										</tr>
									</thead>
									<tbody>
										<?php
										// Seleciona caso (tituloArtigo, anoPublicacao) ou (tituloArtigo, resumo) ou (resumo, anoPublicacao) sejam iguais
										$sql = "								
(SELECT tituloArtigo, COUNT(tituloArtigo)
	FROM resumo
	WHERE idProjeto = {$idProjeto} and status = 'incluido'
	GROUP BY tituloArtigo, anoPublicacao
 	HAVING COUNT(tituloArtigo) > 1)
UNION
(SELECT tituloArtigo, COUNT(anoPublicacao)
	FROM resumo
	WHERE idProjeto = {$idProjeto} and status = 'incluido'
	GROUP BY tituloArtigo, resumo
	HAVING COUNT(anoPublicacao) > 1)
UNION
(SELECT tituloArtigo, COUNT(resumo)
	FROM resumo
	WHERE idProjeto = {$idProjeto} and status = 'incluido'
	GROUP BY resumo, anoPublicacao
	HAVING COUNT(resumo) > 1);
";
// echo $sql;
										$result = $link->query($sql);
										$rows = resultToArray($result);

										foreach ($rows as $artigo) {
											// $nomeArtigo = mb_substr($artigo['tituloArtigo'], 0, 30);
											$tituloArtigo = $artigo['tituloArtigo'];
											$countTitulo = $artigo['COUNT(tituloArtigo)'];
											$pesquisa = base64_encode($tituloArtigo);

											?>
											<tr>
												<td>
													<?php echo $tituloArtigo ?>
												</td>
												<td>
													<?php echo $countTitulo ?>
												</td>

												<td>
													<center>
														<a
															href="verDuplicado.php?idProjeto=<?php echo $idProjeto ?>&resumo=true&pesquisa=<?php echo $pesquisa ?>"><button
																style="width: 100%;" type="button"
																class="btn btn-primary btn-sm"><i
																	class="fa fa-dot-circle-o"></i> Ver</button></a>
														<!--<button style="width: 100%;" onclick = "deletar()" type="button" class="btn btn-danger btn-sm"><i class="fa fa-dot-circle-o"></i> Duplicado</button>-->
													</center>
												</td>

											</tr>
										<?php } ?>
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

	<script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
	<script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
	<script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
	<script src="vendors/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
	<script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
	<script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
	<script src="vendors/datatables.net-buttons/js/buttons.colVis.min.js"></script>
	<script src="assets/js/init-scripts/data-table/datatables-init.js"></script>
	<script>
		(function ($) {
			//    "use strict";


			/*  Data Table
			-------------*/

			$('#bootstrap-data-table').DataTable({
				lengthMenu: [[10, 20, 50, -1], [10, 20, 50, "All"]],
			});

			$('#bootstrap-data-table-export2').DataTable({
				lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
				buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
			});

			$('#row-select').DataTable({
				initComplete: function () {
					this.api().columns().every(function () {
						var column = this;
						var select = $('<select class="form-control"><option value=""></option></select>')
							.appendTo($(column.footer()).empty())
							.on('change', function () {
								var val = $.fn.dataTable.util.escapeRegex(
									$(this).val()
								);

								column
									.search(val ? '^' + val + '$' : '', true, false)
									.draw();
							});

						column.data().unique().sort().each(function (d, j) {
							select.append('<option value="' + d + '">' + d + '</option>')
						});
					});
				}
			});

		})(jQuery);
	</script>

</body>

</html>