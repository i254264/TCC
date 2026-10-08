<?php
include_once('conexaoDB.php');
$tableName = 'tabela_' . $DBName;
$BDjaCriado = NULL;

try {
    $conn->exec("CREATE TABLE IF NOT EXISTS `$tableName` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `title` TEXT DEFAULT NULL,
      `year` VARCHAR(10) DEFAULT NULL,
      `abstract` TEXT DEFAULT NULL,
      `col` TEXT DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

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
    $messagesError[] = "Testing if data already exists failed: " . $e->getMessage();
    $respostaAjax = 0;
}
?>