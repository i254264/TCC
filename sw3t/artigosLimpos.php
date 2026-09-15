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
<title> <?php echo $title ?> - Gerenciar Artigos </title>
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
                        <h1>Gerenciar Artigos</h1>
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
                            <li class="active">Artigos Limpos</li>
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
							 <center>
							 <a href="pesquisarArtigos.php?idProjeto=<?php echo $idProjeto ?>"><button style="width:80% !important;font-size: 2rem !important;"type="button" class="btn btn-info btn-sm"><i class="fa fa-dot-circle-o"></i> Pesquisar artigos</button></a>
							 </center>
							 </div>
						 </div>
					 </div>
				 </div>
				
                <div class="row">
					<div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Artigos Cadastrados no projeto</strong>
                            </div>
                            <div class="card-body">
							
								<table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Titulo</th>
                                            <th>Ano</th>
                                            <th>status</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									$sql = "Select id,resumo,resumoLimpo from resumo where idProjeto = '{$idProjeto}' and resumo like '%&copy;%'";
				$result = $link->query($sql);
				$rows =resultToArray($result);
					
					
function diff($old, $new){
	foreach($old as $oindex => $ovalue){
		$nkeys = array_keys($new, $ovalue);
		foreach($nkeys as $nindex){
			$matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
				$matrix[$oindex - 1][$nindex - 1] + 1 : 1;
			if($matrix[$oindex][$nindex] > $maxlen){
				$maxlen = $matrix[$oindex][$nindex];
				$omax = $oindex + 1 - $maxlen;
				$nmax = $nindex + 1 - $maxlen;
			}
		}	
	}
	if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
	return array_merge(
		diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
		array_slice($new, $nmax, $maxlen),
		diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
}
function htmlDiff($old, $new){
	$diff = diff(explode(' ', $old), explode(' ', $new));
	foreach($diff as $k){
		if(is_array($k))
			$ret .= (!empty($k['d'])?"<del>".implode(' ',$k['d'])."</del> ":'').
				(!empty($k['i'])?"<ins>".implode(' ',$k['i'])."</ins> ":'');
		else $ret .= $k . ' ';
	}
	return $ret;
}


					
					
					foreach ($rows as $artigo){
						$old = $artigo['resumo'];
						$new = $artigo['resumoLimpo'];
						
$string1 = $old;
$string2 = $new;

$matchingcharacters = [];
$mismatchingcharacters = [];

$len1 = strlen($string1);
$len2 = strlen($string2);

$similarity = $i = $j = $dissimilarity = 0;

while (($i < $len1) && isset($string2[$j])) {
    if ($string1[$i] == $string2[$j]) {
        $similarity++;
        $matchingcharacters[] = '['.$string1[$i].']';
    } else {
        $dissimilarity++;
        $mismatchingcharacters[] = '['.$string1[$i] . " & " . $string2[$j].']';
    }
    $i++;
    $j++;
}
echo 'First string : '.$string1.'<br>';
echo 'Second string : '.$string2.'<br>';
echo 'Similarity : ' . $similarity . '<br>';
echo 'Dissimilarity : ' . $dissimilarity . '<br>';
echo 'Matching characters : ' . implode(",", $matchingcharacters) . '<br>';
echo 'Mismatching characters : ' . implode(",", $mismatchingcharacters);
die;
					}
					foreach ($rows as $artigo){
						// $nomeArtigo = mb_substr($artigo['tituloArtigo'], 0, 30);
						$nomeArtigo = $artigo['tituloArtigo'];
						$idArtigo = $artigo['id'];
						$anoPublicacao = $artigo['anoPublicacao'];
							

					?>
                                        <tr id="<?php echo $idArtigo ?>"> 
                                            <td><?php echo $nomeArtigo ?></td>
                                            <td><?php echo $anoPublicacao ?></td>
                                            <td>
											<center>
											<?php echo $status ?><br>
											<button style="width: 100%;" onclick = 'motivo(<?php echo $idArtigo ?>)' data-toggle='modal' data-target='#largeModal'  type='button' class='btn btn-info btn-sm'><i class='fa fa-dot-circle-o'></i> Motivo</button>
											</center>
											</td>
											<td ><center>
											<a  href="verArtigo.php?idArtigo=<?php echo $idArtigo ?>"><button style="width: 100%;" type="button" class="btn btn-primary btn-sm"><i class="fa fa-dot-circle-o"></i> Ver</button></a>
											<button style="width: 100%;" onclick = "deletar(<?php echo $idArtigo ?>)" type="button" class="btn btn-danger btn-sm"><i class="fa fa-dot-circle-o"></i> Deletar</button>
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
