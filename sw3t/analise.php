<?php 
require_once("config.php");
//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'],$_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);


$idTrabalho = numbers($_GET['idTrabalho']);

$sql = "Select idProjeto from trabalho where id = '{$idTrabalho}'";
$result = $link->query($sql);
$row = $result->fetch_assoc();

$idProjeto = $row['idProjeto'];

// echo $sql;die;

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
<title> <?php echo $title ?> - Ver analise </title>
</head>

<body>
    <!-- Left Panel -->
<?php $onde="analises";include("includes/menu.php"); ?>
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
                        <h1>Analise</h1>
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
                            <li class="active">Analise</li>
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
                                <strong class="card-title">Analise do Projeto</strong>
                            </div>
                            <div class="card-body">
			<?php
    $palavrasNaAnalise = array();
			$sql = "SELECT coerencia,perplexidade,acuracia,outJson,area_conhecimento FROM `trabalho` where id = $idTrabalho";
				$result = $link->query($sql);
				$row = $result->fetch_assoc();
				$json =  $row['outJson'];
				$coerencia =  (float)$row['coerencia'];
				$perplexidade =  (float)$row['perplexidade'];
				if($perplexidade < 0 ){$perplexidade = $perplexidade * -1 ;}
				$acuracia =  (float)$row['acuracia'];
				$areaConhecimento =  (string)$row['area_conhecimento'];

				$coerencia = number_format($coerencia, 1, '.', '');
				$perplexidade = number_format($perplexidade, 1, '.', '');
				
				$acuracia = number_format($acuracia, 1, '.', '');

				?>
			<div  class="col-sm-4">
			coerencia : <?php echo $coerencia ?>%
			</div>
			<div  class="col-sm-4">
			afirmação : <?php echo $perplexidade ?>
			</div>
			<div class="col-sm-4">
			acuracia : <?php echo $acuracia ?>%
			</div>
            <div class="col-sm-4">
			Área de Conhecimento : <?php echo $areaConhecimento ?>
			</div>
			<br>
			<br>
			<br>
				
									<?php
									
									
				
				
				
										$sql = "SELECT id,output FROM `output` where idTrabalho = $idTrabalho ";
				$result = $link->query($sql);
				$rows =resultToArray($result);
					
					$i=0;
					// echo "<div style='margin-top: inherit;' class='row'>";
				foreach ($rows as $output){
					$idOutput = $output['id'];
					$output = $output['output'];
                     $idOutputLda = explode(",",$output);
                    $idOutputLda = str_replace("(","",$idOutputLda[0]);
					$output = limparOutputPython($output);
                   
					$countValor=0;
                    $sql = "select count(id) from resumotrabalho where idTrabalho = '{$idTrabalho}' and idTopicoLda='{$idOutputLda}'";
                    
                    $result = $link->query($sql);
                    $count = $result->fetch_assoc();
					?>
					
					<a href="topico.php?idOutput=<?php echo $idOutput ?>">
					<div style="border: aquamarine;	border-style: double;"class="col-sm-2">
					
					<?php 	
                        echo $count['count(id)']." artigos nesse topico<br>";
					
					$sql = "select count(id) from resumo where idProjeto ='{$idProjeto}' and (";
					
																$first = true;
											foreach ($output as $outputPalavra ){
												
												$palavraPesquisa = $outputPalavra['palavra'];
												if($first){
												$sql .= " resumoEnxuto like '%{$palavraPesquisa}%' ";
												$first = false;
												}else{
													$sql .= " and resumoEnxuto like '%{$palavraPesquisa}%' ";
												}

												
											}
											$sql .= ")";
#$result = $link->query($sql);
#$row = $result->fetch_assoc();							
								$count = $row['count(id)'];								
					//echo " Artigos : $count <br>";
					
					
											foreach($output as $outputPalavra){
												$valor =$outputPalavra['valor'];
												$valor = $valor * 1000;
												$valor = $valor * 1.1;
												if($valor > 150 ){
													$valor = 150;
												}
											echo "<img src='piramide.png' style='width:{$valor}px;height:5px;' >";
												$palavrasNaAnalise[$outputPalavra['palavra']]++;
												
												
												echo " - ".$outputPalavra['palavra']."<br>";
											
												
											}
											


											?>
											</div></a>
											
											
                                        <?php 
										
				} ?>
                              
							
								
								
                            </div>
                        </div>
                    </div>

                </div>
            </div>
             
            
            
			<?php
			
			if($json != "none"){
                 $jsonData=file_get_contents($json);
                $arrayJson = json_decode($jsonData,true);

                //print_r($arrayJson);
                $arrayJson = $arrayJson['token.table']['Term'];
                foreach($arrayJson as $value){
                $palavrasNaAnalise[$value]++;
                }
				
				$json = str_replace($sw3tLocation,$sw3tUrl,$json);
				
				?>
			       <div class="row">
					<div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Grafico</strong>
                            </div>
                            <div class="card-body">
							<?php echo $json; ?>
							<br>
							<iframe  id="iframeId" style="width:100%;height:900px;" >
                                </iframe>
								
                            </div>
                        </div>
                    </div>
                </div>
			<?php } ?>
           <script>
            $(document).ready(function(){
    $('#iframeId').attr('src', 'grafico/grafico.php?json=<?php echo $json; ?>');    
               document.getElementById("iframeId").style.display = "";
});
            </script>
            <div class="row">
					<div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Remover Palavras ( Adicionar ao StopWords )</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
							<?php 

   
ksort($palavrasNaAnalise);
                    foreach($palavrasNaAnalise as $palavra => $value){
                        echo "<div class='col-lg-2'>";
                        echo "<button class='btn btnPalavrasAnalise' id='botao{$palavra}' onclick='stopWord(\"{$palavra}\")'>{$palavra}</button>";
                        echo "</div>";
                        
                    }

?>
                           </div>
                            </div>
                        </div>
                    </div>
                </div>
            <script>
            	function stopWord(palavra){
		 document.getElementById('botao'+palavra).innerHTML="..."; 
		
		$.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: "idProjeto=<?php echo $idProjeto ?>&palavra="+palavra,
            success: function(data) {
               document.getElementById('botao'+palavra).innerHTML="Adicionado"; 
            }
        });
		
	}
            </script>
			<!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->

  <?php include("includes/js.php"); ?>
   


</body>

</html>
