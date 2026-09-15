<?php
header('Content-Type: application/json');
include_once('conexaoDB.php');

$tableName = 'tabela_topicgeneration';

try {
    // Busca apenas os campos necessários para a visualização
    $stmt = $conn->prepare("SELECT title, year, abstract FROM $tableName ORDER BY id DESC");
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>