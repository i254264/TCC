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
		<?php echo $title ?> - Adicionar Arquivos
	</title>
</head>

<body>
	<!-- Left Panel -->
	<?php $onde = "adicionarArtigos";
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
						<h1 style="font-size: 1.5rem;">Adicionar Arquivo</h1>
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
							<li class="active">Adicionar Arquivo</li>
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
								<br>
								<form id="formulario" method="post" enctype="multipart/form-data">
									<div class="col-lg-8">
									<div class="input-group mb-3">
											<div class="input-group-addon">
												<div>
													<i id="icone" class="fa fa-file"></i>
												</div>
											</div>
											<div class="custom-file">
												<input type="file" id="file-input" name="file" class="form-control" required>
												<label for="file-input" class="form-label"></label>
											</div>

											<!-- BOTÃO ANTIGO
											<div class="input-group-addon">
												<div>
												<i id="icone" class="fa fa-file"></i>
												</div>
											</div>	
											<div class="custom-file">
												<input type="file" class="custom-file-input" id="file-input" name="file" class="form-control" required> 
												<label class="custom-file-label" for="file-input">Escolher arquivo com extensão .xml</label>
											</div> -->
										</div>

										<div class="card col w-50 p-1">
											<label for="area_conhecimento">Área de Conhecimento:</label>
											<select class="custom-select" name="area_conhecimento"
												id="area_conhecimento" required>
												<option value="" style="color: gray;" disabled selected>Escolha a área
												</option>
												<option value="generico">genérico</option>
												<option value="exatas">exatas</option>
												<option value="humanas">humanas</option>
												<option value="biologicas">biológicas</option>
											</select>

										</div>
									</div>

									<input type="hidden" name="idProjeto" value="<?php echo $idProjeto ?>">

									<div class="col-lg-4">
										<div class="row">
											<select name="fonte" class="mr-3" required>
												<option value="" style="color: gray;" disabled selected>Banco de
													Dados</option>
												<?php
												$sql = "select id,descricao from fontes";
												$result = $link->query($sql);
												$rows = resultToArray($result);
												foreach ($rows as $fonte) {
													?>
													<option value="<?php echo $fonte['id'] ?>"><?php echo $fonte['descricao'] ?></option>
													<?php
												}
												?>
											</select>
											<button type="submit" id="botao" style="color:white;"
												class="btn btn-success">Enviar</button><br>
										</div>
									</div>
								</form>
								<br>
								<br>
								<div id="result"></div>
								<script>
																										$("#formulario").submit(function (event) {
										var formData = new FormData(this);
										//console.log(formData.get('idProjeto'));
										document.getElementById('botao').innerHTML = 'Carregando...';
										document.getElementById('icone').className = 'fa fa-spinner';
										$.ajax({
											url: 'ajax.php',
											type: 'POST',
											data: formData,
											success: function (data) {
												document.getElementById('icone').className = 'fa fa-file';
												document.getElementById('botao').innerHTML = 'ENVIAR';
												eval(data);
											},
											cache: false,
											contentType: false,
											processData: false,
											xhr: function () { // Custom XMLHttpRequest
												var myXhr = $.ajaxSettings.xhr();
												if (myXhr.upload) { // Avalia se tem suporte a propriedade upload
													myXhr.upload.addEventListener('progress', function () {
														/* faz alguma coisa durante o progresso do upload */
													}, false);
												}
												return myXhr;
											}

										});

									});

									function baixarTodos() {
										document.getElementById('botaoTudo').innerHTML = "Carregando ...";
										$.ajax({
											type: 'POST',
											url: 'ajaxAcm.php',
											data: 'idProjeto=<?php echo $idProjeto ?>&baixarTodos=true',
											success: function (data) {
												eval(data);
											}
										});
									}
								</script>

								<br><br>


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