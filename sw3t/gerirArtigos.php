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

if(isset($_GET['deletar'])){
	$sql = "delete from resumo where idProjeto = '{$idProjeto}' ";
	$result = $link->query($sql);
	$sql = "delete from autores where idProjeto = '{$idProjeto}' ";
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
                           <li class="active">Gerenciar Resumos</li>
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
                            <div class="card-header d-flex justify-content-between">
                                <strong class="card-title" style="margin: 3px 0px 0px 0px;">Resumos no projeto</strong>
                                <div>
                                    <div class="col">
                                        <a target='_blank' href="ajaxExportarExcluidos.php?idProjeto=<?php echo $idProjeto ?>&exportar=true" type="button" id="exportar" class="btn btn-success btn-sm">Exportar resumos excluidos</a>
                                        <a target='_blank' href="ajaxExportarExcluidos.php?idProjeto=<?php echo $idProjeto ?>&exportarIncluidos=true" type="button" id="exportarIncluidos" class="btn btn-info btn-sm">Exportar resumos mantidos</a>
                                        <a href="gerirArtigos.php?idProjeto=<?php echo $idProjeto ?>&deletar=true"><button type="button" class="btn btn-danger btn-sm">Deletar Todos os resumos</button></a>
                                    </div>
                                </div>
                                </div>
                            <div class="card-body">
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
                                    document.getElementById('status'+idArtigo).innerHTML = "<i class='fa fa-dot-circle-o'></i> Mantido</button>"; 
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
                            <div class="artigosWrapper">
                                <div class="fixWrapper">
								<table id="bootstrap-data-table-export" class="table table-striped table-bordered table-hover" style="width: 90%;">
                                    <thead>
                                        <tr>
                                            <th>Titulo</th>
                                            <th>Ano</th>
                                            <th>Status</th>
                                            <!-- <th class="acoesPainel" name="acoesPainel">Ações</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
            <?php
                $sql = "Select id,tituloArtigo,anoPublicacao from resumo where idProjeto = '{$idProjeto}' ";
				$result = $link->query($sql);
				$rows =resultToArray($result);
					
					foreach ($rows as $artigo){
						// $nomeArtigo = mb_substr($artigo['tituloArtigo'], 0, 30);
						$nomeArtigo = $artigo['tituloArtigo'];
						$idArtigo = $artigo['id'];
						$anoPublicacao = $artigo['anoPublicacao'];
							$status = dadosResumo("status",$idArtigo);

                if($status =="incluido"){
                    $status = "<button style='width: 100%' id = 'status$idArtigo' onclick = 'excluir($idArtigo)' type='button' class='btn btn-success btn-sm'><i class='fa fa-dot-circle-o'></i> Mantido</button>";
                }else{
                    $status = "<button style='width: 100%' id = 'status$idArtigo' onclick = 'incluir($idArtigo)' type='button' class='btn btn-danger btn-sm'><i class='fa fa-dot-circle-o'></i> Excluido</button>";
                }
            ?>
                                        <tr onclick="dados(<?php echo $idArtigo ?>)" id="<?php echo $idArtigo ?>"> 
                                            <td><?php echo $nomeArtigo ?></td>
                                            <td><?php echo $anoPublicacao ?></td>
                                            <td>	
											    <?php echo $status ?>
											    <button style="width: 100%" onclick = 'motivo(<?php echo $idArtigo ?>)' data-toggle='modal' data-target='#largeModal'  type='button' class='btn btn-info btn-sm'><i class='fa fa-dot-circle-o'></i> Motivo</button>
											</td>
											


                                        </tr>
					<?php } ?>
                                    </tbody>
                                    
                                </table>
                                </div>
								<div class="infobox">
                                    <div class="infoboxHeader">
                                        Informações
                                    </div>
                                    <div class="infoboxContent">
                                        <div class="infoboxTitle">
                                            <span id="infoTitulo" class="infoboxTitleSpan"></span>
                                        </div>
                                        <div class="infobotDate">
                                            <span id="infoData" class="infoboxDateSpan"></span>
                                        </div>
                                        <div id="infoAutores" class="infoboxAuthor">
                                            <span class="infoboxAuthorSpan"></span><br>
                                            
                                        </div>
                                        
                                        <div class="infoboxAbstract">
                                            <span id="infoResumo" class="infobosAbstractSpan"></span>
                                        </div>
                                        <a id="infoBotaoVer"  href="verArtigo.php?idArtigo=<?php echo $idArtigo ?>"><button style="width: 100%; margin-top: 10px;" type="button" class="btn btn-primary btn-sm"><i class="fa fa-dot-circle-o"></i> Ver</button></a>
										<button style="width: 100%;  margin-top: 3px;" id="infoBotaoDeletar" onclick = "deletar(<?php echo $idArtigo ?>)" type="button" class="btn btn-danger btn-sm"><i class="fa fa-dot-circle-o"></i> Deletar</button>
                                    </div>
                                </div>
                            </div>
								

                            </div>
                        </div>
                    </div>

                </div>
				
				<script>
				function dados(idResumo){
				document.getElementById("infoBotaoVer").href="verArtigo.php?idArtigo="+idResumo; 
				document.getElementById('infoBotaoDeletar').setAttribute( "onClick", "deletar("+idResumo+")" );
				document.getElementById("infoResumo").innerHTML = "Carregando"; 
				$.ajax({
					type: 'POST',
					url: 'ajaxInfobox.php',
					data: 'idArtigo='+idResumo,
					success: function(data) {
						eval(data);
					}
				});
			
					
				}
                
			
					
				
				</script>
	
				
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
