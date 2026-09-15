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

die;

$sql = "
SELECT 
    tituloArtigo, 
    COUNT(tituloArtigo)
FROM
    resumo
	 where idProjeto = {$idProjeto}
GROUP BY tituloArtigo
HAVING COUNT(tituloArtigo) > 1;
";
$result = $link->query($sql);
$rows =resultToArray($result);
$quantidadeResultados = count($rows);
if($quantidadeResultados == 0 ){$out =  "Nenhum resultado encontrado";}else{

$out="Fora encontrado $quantidadeResultados duplicado(s) (Por titulo)<br>";


$out .='<table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Titulo</th>
                                            <th>Quantidade de duplicados</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>';


foreach ($rows as $artigo){
	$tituloArtigo = $artigo["tituloArtigo"];
	$qtdDups = $artigo["COUNT(tituloArtigo)"];
$out .= "<tr> 
			<td>$tituloArtigo</td>
			<td>$qtdDups</td>
			
			<td>
			<center>
			<a href='verArtigo.php?idArtigo='><button style='width: 100%;' type='button' class='btn btn-primary btn-sm'><i class='fa fa-dot-circle-o'></i> Ver</button></a>
			<br>
			<br>
			<button style='width: 100%;' onclick = 'deletar()' type='button' class='btn btn-danger btn-sm'><i class='fa fa-dot-circle-o'></i> Deletar</button>
			</center>
			</td>

		</tr>";
	
}
$out .='</tbody>
	</table>';


}

$out = preg_replace( "/\r|\n/", "", $out );
$out = addslashes($out);
$outHtml ="document.getElementById('result').innerHTML = \"$out\";"; 
$out="";
echo $outHtml;
// $result = $link->query($sql);
// $rows =resultToArray($result);

// foreach ($rows as $artigo){
	
// }
?>