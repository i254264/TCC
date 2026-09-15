<?php
include_once('conexaoDB.php');
$tableName = 'auxiliar';
$table2 = 'tabela_' . $DBName;

try{
    // Deleta todos os dados da tabela antes de adicionar
    if ($BDjaCriado == '3') {
        try {
            $sqlTruncate = "TRUNCATE TABLE $table2";
            $conn->exec($sqlTruncate);
            $messages[] = "$table2 truncada com sucesso!";
        }
        catch (PDOException $e) {
            $messageError[] = "Failed to truncate table: " . $e->getMessage();
            $respostaAjax = 0;
        }
    }

    $conn->beginTransaction();
    $sqlInsert = "INSERT INTO $table2 (col) SELECT col FROM $tableName";
    $conn->exec($sqlInsert);
    $conn->commit();

    //fim do processo, respostaAjax deve ser 1
    $respostaAjax = 1;
    $messages[] = "Colunas copiadas com sucesso!";
}
catch (PDOException $e) {
    $messageError[] = "Failed to copy data: " . $e->getMessage();
    $respostaAjax = 0;
}
?>