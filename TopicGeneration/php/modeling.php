<?php
// php/modeling.php

$clean = isset($_POST['cleanCorpus']) ? $_POST['cleanCorpus'] : '0';
$lemma = isset($_POST['lemmaCorpus']) ? $_POST['lemmaCorpus'] : '0';
$topics = isset($_POST['topics']) ? $_POST['topics'] : '5';
$words = isset($_POST['words']) ? $_POST['words'] : '10';
$interaction = isset($_POST['interaction']) ? $_POST['interaction'] : '100';
$typeModeling = isset($_POST['typeModeling']) ? $_POST['typeModeling'] : '1'; // 1 para LDA

// Caminho para o executável python e para o script
// No Windows/XAMPP geralmente usa-se 'python'. No Linux 'python3'.
$pythonPath = "python"; 
$scriptPath = "../python/processingModeling.py";

// Escapa os argumentos para segurança
$arg1 = escapeshellarg($clean);
$arg2 = escapeshellarg($lemma);
$arg3 = escapeshellarg($topics);
$arg4 = escapeshellarg($words);
$arg5 = escapeshellarg($interaction);
$arg6 = escapeshellarg($typeModeling);

$command = "$pythonPath $scriptPath $arg1 $arg2 $arg3 $arg4 $arg5 $arg6 2>&1";
$output = shell_exec($command);

if (strpos($output, '.xlsx') !== false) {
    echo json_encode(["status" => "success", "file" => trim($output)]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $output]);
}
?>