<?php 
require_once("config.php");
//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
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
<title> <?php echo $title ?> - Pesquisar Artigos </title>
</head>

<body>
    <!-- Left Panel -->
<?php $onde="pesquisarArtigos";include("includes/menu.php"); ?>
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
                        <h1>Pesquisar Artigos</h1>
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
                            <li class="active">Pesquisar Artigos</li>
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
                                <strong class="card-title">Pesquisar </strong>
                            </div>
							<form id="pesquisa" name="pesquisa">
							<input type="hidden" value="<?php echo $idProjeto ?>" name="idProjeto" id="idProjeto">
                            <div class="card-body">
							
								<div class="row form-group">
									<div class="col col-md-3"><label for="textarea-input" class=" form-control-label">Termo a ser pesquisado</label></div>
									<div class="col-12 col-md-9"><textarea name="termo" id="termo" rows="3" placeholder="Alguma frase ou palavra" class="form-control"></textarea></div>
								</div>
								
							
							
								<div class="row form-group">
									<div class="col col-md-3"><label for="multiple-select" class=" form-control-label">Pesquisar em quais Campos ?</label></div>
										<div class="col col-md-9">
											<select name="campos[]" id="multiple-select" multiple="" class="form-control">
												<option value="titulo">Titulo</option>
												<option value="resumo">Resumo</option>
												<!--<option value="enxuto">Resumo Enxuto</option>-->
											</select>
										</div>
                                 </div>
								 
								 
								 	<div class="row form-group">
									<div class="col col-md-3"><label for="multiple-select" class=" form-control-label">Quais Status ?</label></div>
										<div class="col col-md-9">
											<select name="status[]" id="multiple-select" multiple="" class="form-control">
												<option value="incluido">incluido</option>
												<option value="excluido">excluido</option>

											</select>
										</div>
                                 </div>
								 
								 <div class="row form-group">
									<div class="col col-md-3"><label for="multiple-select" class=" form-control-label">Qual ano ?</label></div>
										<div class="col col-md-9">
													<select class="form-control" id="anos" name="anos">
								<option selected value="todos">Todos</option>
											<?php 
											$sql = "select distinct(anoPublicacao) from resumo where idProjeto = '{$idProjeto}' order by anoPublicacao asc";
											$result = $link->query($sql);
											$rows = resultToArray($result);
					
											foreach($rows as $ano){
											
											?>
											<option value="<?php echo $ano['anoPublicacao'] ?>"><?php echo $ano['anoPublicacao'] ?></option>
											<?php  } ?>
											</select>
										</div>
                                 </div>
                                <div class="row form-group">
                                    <div class="col col-md-3"><label for="multiple-select" class=" form-control-label">Criterio</label></div>
                                    <div class="col col-md-9">
                                        <select name="criterio[]" id="multiple-select" multiple="" class="form-control">
                                            <option value="titulo">titulo</option>
                                            <option value="resumo">resumo</option>
                                            <option value="conteudo">conteudo</option>
                                            <option value="duplicado">duplicado</option>

                                        </select>
                                    </div>
                                </div>
								 
                            </div>
							<div class="card-footer">
								<button id="pesquisar" onclick="enviarForm()" type="button" class="btn btn-primary btn-sm">
									<i class="fa fa-dot-circle-o"></i> Pesquisar
								</button>
								<button type="reset" class="btn btn-danger btn-sm">
									<i class="fa fa-ban"></i> Limpar
								</button>
                                <button id="IncludeAll" onclick="includeAllS()" type="button" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-dot-circle-o"></i> INCLUIR EXIBIDOS
                                </button>
                                <button id="excludeAll" onclick="excludeAllS()" type="button" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-dot-circle-o"></i> EXCLUIR EXIBIDOS
                                </button>
							</div>
							</form>
							
                        </div>
                    </div>
                </div>
				<script>
					function deletar(idArtigo) {
								$.ajax({
									type: 'GET',
									url: "verArtigo.php?idArtigo="+idArtigo+"&deletar=true&close=true",
									success: function(data) {
									   eval(data);
									}
								});
							  
							}
					function enviarForm(){
		 document.getElementById('pesquisar').innerHTML="Carregando ..."; 
		 document.getElementById('pesquisar').disabled=true; 
		$.ajax({
            type: 'POST',
            url: 'ajaxPesquisar.php',
            data: $("#pesquisa").serialize(),
            success: function(data) {
				document.getElementById('pesquisar').innerHTML="<i class='fa fa-dot-circle-o'></i> Pesquisar"; 
				document.getElementById('pesquisar').disabled=false; 
                document.getElementById('result').innerHTML=data; 
				
            }
        });
		
	}
					function excludeAllS(){
		 document.getElementById('excludeAll').innerHTML="Carregando ...";
		 document.getElementById('excludeAll').disabled=true;
		$.ajax({
            type: 'POST',
            url: 'ajaxPesquisar.php?excludeAll=1',
            data: $("#pesquisa").serialize(),
            success: function(data) {
				document.getElementById('excludeAll').innerHTML="<i class='fa fa-dot-circle-o'></i> EXCLUIR EXIBIDOS";
				document.getElementById('excludeAll').disabled=false;
                document.getElementById('result').innerHTML=data;

            }
        });

	}
					function includeAllS(){
		 document.getElementById('IncludeAll').innerHTML="Carregando ...";
		 document.getElementById('IncludeAll').disabled=true;
		$.ajax({
            type: 'POST',
            url: 'ajaxPesquisar.php?includeAll=1',
            data: $("#pesquisa").serialize(),
            success: function(data) {
				document.getElementById('IncludeAll').innerHTML="<i class='fa fa-dot-circle-o'></i> INCLUIR EXIBIDOS";
				document.getElementById('IncludeAll').disabled=false;
                document.getElementById('result').innerHTML=data;

            }
        });

	}
	
	function excluir(idArtigo){

	document.getElementById('status'+idArtigo).innerHTML = "<i class='fa fa-dot-circle-o'></i> Excluido</button>"; 
	document.getElementById('status'+idArtigo).className = "btn btn-danger btn-sm"; 
	document.getElementById('status'+idArtigo ).setAttribute( "onClick", "javascript: incluir("+idArtigo+");" );
	
	var element = document.getElementById('Astatus'+idArtigo);
 
    //If it isn't "undefined" and it isn't "null", then it exists.
    if(typeof(element) != 'undefined' && element != null){
      //existe
	document.getElementById('Astatus'+idArtigo).innerHTML = "<i class='fa fa-dot-circle-o'></i> Excluido</button>"; 
	document.getElementById('Astatus'+idArtigo).className = "btn btn-danger btn-sm"; 
	document.getElementById('Astatus'+idArtigo ).setAttribute( "onClick", "javascript: incluir("+idArtigo+");" );
    } 

$.ajax({
            type: 'POST',
            url: 'ajaxStatus.php',
            data: "status=excluido&idArtigo="+idArtigo,
            success: function(data) {

				
            }
        });



	}
function incluir(idArtigo){
	document.getElementById('status'+idArtigo).innerHTML = "<i class='fa fa-dot-circle-o'></i> Incluido</button>"; 
	document.getElementById('status'+idArtigo).className = "btn btn-success btn-sm"; 
	// document.getElementById('status'+idArtigo).onclick   = incluir(idArtigo); 
	document.getElementById('status'+idArtigo ).setAttribute( "onClick", "javascript: excluir("+idArtigo+");" );
	
	var element = document.getElementById('Astatus'+idArtigo);
 
    //If it isn't "undefined" and it isn't "null", then it exists.
    if(typeof(element) != 'undefined' && element != null){
      //existe
	document.getElementById('Astatus'+idArtigo).innerHTML = "<i class='fa fa-dot-circle-o'></i> Incluido</button>"; 
	document.getElementById('Astatus'+idArtigo).className = "btn btn-success btn-sm"; 
	document.getElementById('Astatus'+idArtigo ).setAttribute( "onClick", "javascript: excluir("+idArtigo+");" );
    } 

$.ajax({
            type: 'POST',
            url: 'ajaxStatus.php',
            data: "status=incluido&idArtigo="+idArtigo,
            success: function(data) {

				
            }
        });
}	

function motivo(idArtigo){
	$.ajax({
            type: 'POST',
            url: 'ajaxStatus.php',
            data: "motivo=true&idArtigo="+idArtigo,
            success: function(data) {

				document.getElementById('resultMotivo').innerHTML = data; 
				
            }
        });
}

function salvarMotivo(){
	document.getElementById('resultSalvarMotivo').innerHTML ="carregando ...";
	$.ajax({
            type: 'POST',
            url: 'ajaxStatus.php',
            data: $("#formMotivo").serialize(),
            success: function(data) {

				//resultSalvarMotivo
				document.getElementById('resultSalvarMotivo').innerHTML = data; 
            }
        });
}
	</script>
		
				
						
				 <div class="row">
					<div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Resultado Pesquisa</strong>
                            </div>
                            <div class="card-body">
								<div id="result"></div>
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

			<div class="modal fade" id="largeModal" tabindex="-1" role="dialog" aria-labelledby="largeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="largeModalLabel">Motivo</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="resultMotivo">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                <button type="button" class="btn btn-primary" onclick="salvarMotivo()" >Salvar</button>
                            </div>
                        </div>
                    </div>
                </div>
</body>

</html>
