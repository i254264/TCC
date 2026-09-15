<?php
include_once('conexaoDB.php');
$tableName = 'tabela_' . $DBName;
$BDjaCriado = NULL;

try {
    $sqlSelect = "SELECT COUNT(*) FROM $tableName;";
    $stmt = $conn->query($sqlSelect);
    $tableExists = $stmt->fetchColumn();
    if ($tableExists > 0) {
        $messages[] = "Ja existem dados na tabela $tableName.";
        $BDjaCriado = '1';
    }
    else{
        $BDjaCriado = '0';
    }
}
catch (PDOException $e) {
    $messageError[] = "Testing if data already exists failed: " . $e->getMessage();
    $respostaAjax = 0;
}
?>