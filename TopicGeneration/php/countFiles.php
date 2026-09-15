<?php
include_once('conexaoDB.php');
$tableName = 'tabela_' . $DBName;
try {
    $sqlSelect = "SELECT COUNT(*) AS qtd FROM $tableName;";
    $stmt = $conn->query($sqlSelect);
    try{
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $quantidade = $result[0]['qtd'];
        echo $quantidade;
    }
    catch (PDOException $e) {
        echo ("Connection failed: " . $e->getMessage());
    }
}
catch (PDOException $e) {
    echo ("Connection failed: " . $e->getMessage());
}
?>