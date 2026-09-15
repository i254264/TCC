<?php
include_once('conexaoDB.php');
header('Content-Type: application/json');

try {
    $stmt = $conn->query("SELECT title, year, abstract FROM tabela_topicgeneration ORDER BY id DESC");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>