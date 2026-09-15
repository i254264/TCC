<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');	
require_once("config.php");
// error_reporting(E_ALL);
// ini_set('display_errors', '1');	

//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'], $_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);

if ((!isset($_REQUEST['exportar'])) && (!isset($_REQUEST['exportarIncluidos']))) {
    erro("ERRO FATAL");
}

$idProjeto = numbers($_REQUEST['idProjeto']);

donoProjeto($idUsuario, $idProjeto);

$sql = "select id,descricao from fontes where 1";
$result = $link->query($sql);
$rows = resultToArray($result);
$fontes = array();
foreach($rows as $fonte){
	$fontes[$fonte['id']] = $fonte['descricao'];
}

if (isset($_REQUEST['exportar'])) {
    $sql = "SELECT * FROM `resumo` WHERE `idProjeto` = {$idProjeto} AND `status`= 'excluido'";
    $result = $link->query($sql);
    $rows = resultToArray($result);

    // $filename =  "ResumosExcluidos.csv";      
    // header("Content-Type: text/csv");
    // header("Content-Disposition: attachment; filename=\"$filename\"");

        // $file = fopen($filename, "w");
    // $cabecalho = array_keys($rows[0]);

        // fputcsv($file, $cabecalho); //escreve a primeira linyha do cabeçalho
    // foreach ($rows as $row) {
    //     fputcsv($file, $row); //escreve todos os registros
    // }
    // fclose($file);
    // echo $filename;
    // exit;

     $filename = randGen(12).".tsv";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");

    $file = fopen($filename, "w");
    ExportFile($rows, $file);

   readfile($filename);
   unlink($filename);
    exit;
}

if (isset($_REQUEST['exportarIncluidos'])) {
    $sql = "SELECT * FROM `resumo` WHERE `idProjeto` = {$idProjeto} AND `status`= 'incluido'";
    $result = $link->query($sql);
    $rows = resultToArray($result);

    $filename = randGen(12).".tsv";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");

    $file = fopen($filename, "w");
    ExportFile($rows, $file);
    readfile($filename);
	unlink($filename);
    exit;
}
    function ExportFile($records, $file)
    {
		global $fontes;
        $heading = false;
        if (!empty($records)) {
            foreach ($records as $row) {
				unset($row['bibBackup']);
                if (!$heading) {
                    // display field/column names as a first row
                    $linha = implode("\t", array_keys($row)) . "\n";
                    fwrite($file, $linha);
                    $heading = true;
                }
				$linha = '';
				// print_r($fontes);
				// print_r($row);die;
				
				foreach ($row as $key => $col){
					if($key == 'idFonte'){
						$col = $fontes[$col] ?  $fontes[$col] : $col;
					}
					$col = html_entity_decode($col);
					$linha .= str_replace("\t",' ',$col)."\t";
				}
                $linha .= PHP_EOL;
                fwrite($file, $linha);
            }
        }
        fclose($file);
    }
	