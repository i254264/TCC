<?php
require_once("config.php");

set_time_limit (10); // nao gargale o banco de dados :D
//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'],$_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);

if(!isset($_POST['idProjeto'])){
erro("ERRO FATAL");
}
$idProjeto = numbers($_POST['idProjeto']);

donoProjeto($idUsuario,$idProjeto,true);




$tituloSelecionar = false;
$resumoSelecionar = false;
$resumoEnxutoSelecionar = false;
$incluidoSelecionar = false;
$excluidoSelecionar= false;
if(empty($_POST['campos'])){
	echo "Selecione um campo para pesquisar";die;
}
foreach ($_POST['campos'] as $campo){
	if($campo == "titulo"){$tituloSelecionar = true;}
	if($campo == "resumo"){$resumoSelecionar = true;}
	if($campo == "enxuto"){$resumoEnxutoSelecionar = true;}
}


if(empty($_POST['status'])){
	$_POST['status'][0]="a";
}
foreach ($_POST['status'] as $status){
	if($status == "incluido"){$incluidoSelecionar = true;}
	if($status == "excluido"){$excluidoSelecionar = true;}
}


if( !$tituloSelecionar and !$resumoSelecionar and !$resumoEnxutoSelecionar){
	echo "Selecione um campo para pesquisar";die;
}
if( !$incluidoSelecionar and !$excluidoSelecionar ){
	$incluidoSelecionar = true;
	$excluidoSelecionar= true;
}


$termo = sanitize($_POST['termo']);

$sql = "Select id from resumo where idProjeto = {$idProjeto} and (";
$first = true;
if($tituloSelecionar)		 {
	$sql .= " tituloArtigo like '%{$termo}%' ";
	$first = false;
	}
if($resumoSelecionar)		 {
	if($first){
	$sql .= " resumo like '%{$termo}%' ";
	$first = false;
	}else{
		$sql .= " or resumo like '%{$termo}%' ";
	}
	
}
if($resumoEnxutoSelecionar){
	if($first){
	$sql .= " resumoEnxuto like '%{$termo}%' ";
	}else{
		$sql .= " or resumoEnxuto like '%{$termo}%' ";
	}
	
}

$sql .= ")";


if( !($incluidoSelecionar and $excluidoSelecionar)){
$first = true;
if($incluidoSelecionar){
	
	$sql .= " and ( status = 'incluido' ";
	$first = false;
	
}

if($excluidoSelecionar){
	if($first){
	$sql .= " and ( status = 'excluido' ";
	}else{
		$sql .= " or status = 'excluido' ";
	}
	
}
$sql .= ")";
}




if($_POST['anos']!="todos"){
	
	$anos = numbers($_POST['anos']);
	$sql .= " and anoPublicacao = '{$anos}' ";
	
}


if(!empty($_REQUEST['criterio'])){
    $sql .=' and (';
    foreach($_REQUEST['criterio'] as $key => $crit){
        $crit = sanitize($crit);
        if($key != 0 ){
            $sql .= ' or ';
        }
        $sql .= "justificativaStatus ='{$crit}'";
    }
    $sql .=' )';

}

// echo $sql;die;
$result = $link->query($sql);
$rows =resultToArray($result);

// print_r($rows);
$quantidadeResultados = count($rows);
if($quantidadeResultados == 0 ){echo "Nenhum resultado encontrado";die;}
// die;

$out="Fora encontrado $quantidadeResultados Artigo(s)<br>";


$out .='<table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Titulo</th>
                                            <th>Ano Publicação / Autores</th>
                                            <th>Resumo</th>
                                           
                                            <th>STATUS</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: smaller;">';

foreach ($rows as $artigo){
	

	$idArtigo = $artigo['id'];

    if(isset($_REQUEST['includeAll'])){
        $sql ="update resumo set status = 'incluido' where id = '{$idArtigo}'";
        $link->query($sql);
    }

    if(isset($_REQUEST['excludeAll'])){
        $sql ="update resumo set status = 'excluido',justificativaStatus ='botao' where id = '{$idArtigo}'";
        $link->query($sql);
    }


    $anoPublicacao = dadosResumo("anoPublicacao",$idArtigo);
	$tituloArtigo = dadosResumo("tituloArtigo",$idArtigo);
	$resumo = dadosResumo("resumo",$idArtigo);
	// $resumoEnxuto = dadosResumo("resumoEnxuto",$idArtigo);
	$status = dadosResumo("status",$idArtigo);
	
	if($tituloSelecionar){
	$tituloArtigo = str_replace($termo, "<b style='color:red'>".$termo."</b>",$tituloArtigo);
	}	
	if($resumoSelecionar){
	$resumo = str_replace($termo, "<b style='color:red'>".$termo."</b>",$resumo);
	}
	if($resumoEnxutoSelecionar){
	// $resumoEnxuto = str_replace($termo, "<b style='color:red'>".$termo."</b>",$resumoEnxuto);
	}
	
	
	if($status =="incluido"){
		$status = "<button style='width: 100%;' id = 'status$idArtigo' onclick = 'excluir($idArtigo)' type='button' class='btn btn-success btn-sm'><i class='fa fa-dot-circle-o'></i> Incluido</button>";
	}else{
		$status = "<button style='width: 100%;' id = 'status$idArtigo' onclick = 'incluir($idArtigo)' type='button' class='btn btn-danger btn-sm'><i class='fa fa-dot-circle-o'></i> Excluido</button>";
	}
	$autores = "";
$sql = "select nomeAutor from autores where idResumo = '{$idArtigo}'";
$result = $link->query($sql);
$autores1 =resultToArray($result);
foreach ($autores1 as $autor){
	$autores = $autores.$autor['nomeAutor'] ." <b>e</b> ";
}
	$autores = $autores. "aaaa1337";//dps colocar a funcao q tira os ultimos x chars;
	$autores = str_replace(' <b>e</b> aaaa1337',"",$autores);
	$out .= "  <tr id='$idArtigo'> 
                                            <td>$tituloArtigo</td>
                                            <td>{$anoPublicacao} / {$autores}</td>
                                            <td>$resumo</td>
                                           
                                            <td><center>$status<br><br>
											<button style='width: 100%;' onclick = 'motivo($idArtigo)' data-toggle='modal' data-target='#largeModal'  type='button' class='btn btn-info btn-sm'><i class='fa fa-dot-circle-o'></i> Motivo</button></center>
											</td>
                                            <td>
											<center>
											<a href='verArtigo.php?idArtigo=$idArtigo'><button style='width: 100%;' type='button' class='btn btn-primary btn-sm'><i class='fa fa-dot-circle-o'></i> Ver</button></a>
											<br>
											<br>
											<button style='width: 100%;' onclick = 'deletar($idArtigo)' type='button' class='btn btn-danger btn-sm'><i class='fa fa-dot-circle-o'></i> Deletar</button>
											</center>
											</td>

                                        </tr>";
	
}
$out .='</tbody>
	</table>';
	
	echo $out;

die;

?>