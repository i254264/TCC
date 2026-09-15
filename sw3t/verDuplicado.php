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
if(isset($_GET['resumo'])){
	$tipo = "resumo";
}else{
	$tipo = "titulo";
}
if(!base64_decode($_GET['pesquisa'])){
	erro("Erro no servidor para essa pesquisa");
}
$pesquisa = sanitize(base64_decode($_GET['pesquisa']));
	
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
<title> <?php echo $title ?> - Gerenciar Resumos </title>
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
                        <h1>Gerenciar Resumos</h1>
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
                           <li><a href="verificarDuplicados.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Verificar Duplicados</a></li>
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
                            <div class="card-header">
                                <strong class="card-title">Resumos duplicados</strong>
                            </div>
                            <div class="card-body">
							
						<!--Pesquisa :	<?php echo $pesquisa ?>
							<br>
							<br>-->
							<table id="bootstrap-data-table-export" class="table table-sm table-striped table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Titulo</th>
                                            <th>Ano</th>
                                            <th>journal</th>
                                            <th class="d-none d-sm-table-cell">resumo</th>
                                            <th>fonte</th>
                                            <th>área conhecimento</th>
                                            <!-- <th>autores</th> -->
                                            <th>status</th>
                                           
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									
									if($tipo == "resumo"){
                                        $sql = "Select id,tituloArtigo,anoPublicacao,area_conhecimento,idFonte,journal,resumo,idFonte from resumo where idProjeto = '{$idProjeto}' and tituloArtigo = '{$pesquisa}' and status = 'incluido' ";
									}
									
									if($tipo == "titulo"){
									$sql = "Select id,tituloArtigo,anoPublicacao,area_conhecimento,idFonte,journal,resumo,idFonte from resumo where idProjeto = '{$idProjeto}' and tituloArtigo = '{$pesquisa}' and status = 'incluido' ";
									}
				$result = $link->query($sql);
				$rows =resultToArray($result);
					
					foreach ($rows as $artigo){
						// $nomeArtigo = mb_substr($artigo['tituloArtigo'], 0, 30);
						$nomeArtigo = $artigo['tituloArtigo'];
						$idArtigo = $artigo['id'];
						$anoPublicacao = $artigo['anoPublicacao'];
						$journal = $artigo['journal'];
                        $area_conhecimento = $artigo['area_conhecimento'];
						$resumo = $artigo['resumo'];
						$sql = "select descricao from fontes where id = '{$artigo['idFonte']}'";
								$result = $link->query($sql);
								$row = $result->fetch_assoc();
								$fonte = $row['descricao'];
							$status = dadosResumo("status",$idArtigo);
									$sql = "select nomeAutor from autores where idResumo = {$idArtigo} order by id asc";
									$result = $link->query($sql);
									$rows =resultToArray($result);
									$autores="";
									$primeiro=true;
                                    echo $rows;
									foreach($rows as $autor){
										$autor = $autor['nomeAutor'];
										if($primeiro){
											$autores = $autor;
											$primeiro=false;
										}else{
											$autores .= " <b>e</b> ".$autor;
										}
									}
                                    
							

	if($status =="incluido"){
		$status = "<button style='width: 100%;' id = 'status$idArtigo' onclick = 'excluir($idArtigo)' type='button' class='btn btn-success btn-sm'><i class='fa fa-dot-circle-o'></i> Mantido</button>";
	}else{
		$status = "<button style='width: 100%;' id = 'status$idArtigo' onclick = 'incluir($idArtigo)' type='button' class='btn btn-danger btn-sm'><i class='fa fa-dot-circle-o'></i> Excluido</button>";
	}
					?>
                                        <tr id="<?php echo $idArtigo ?>"> 
                                            <td><?php echo $nomeArtigo ?></td>
                                            <td><?php echo $anoPublicacao ?></td>
                                            <td><?php echo $journal ?></td>
                                            <td style="display: flex; height: 300px; overflow-y: scroll;"><?php echo $resumo ?></td>
                                            <td><?php echo $fonte ?></td>
                                            <td><?php echo $area_conhecimento ?></td>
                                            <td>
											<?php echo $status ?><br>
											<button style="width: 100%;" onclick = 'motivo(<?php echo $idArtigo ?>)' data-toggle='modal' data-target='#largeModal'  type='button' class='btn btn-info btn-sm'><i class='fa fa-dot-circle-o'></i> Motivo</button>
											</td>
											<!-- <td><?php echo $autores ?></td> -->

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
