<?php
$dirLocation = dirname(__FILE__)."/";
$dirLocation = str_replace('\\','/',$dirLocation);
$dirLocation = str_replace('php/','python/',$dirLocation);

// Nome do arquivo Python
$pythonFile = "processingModeling.py";
// Caminho completo do arquivo Python
$path = $dirLocation . $pythonFile;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clean = intval($_POST['cleanCorpus']);
    $lemma = intval($_POST['lemmaCorpus']);
    $topics = $_POST['topics'];
    $words = $_POST['words'];
    $interaction = $_POST['interaction'];
    $typeModeling = $_POST['typeModeling'];

    // echo $clean,' - ', $lemma,' - ', $topics,' - ', $words,' - ', $interaction, ' - ', $typeModeling;
    
    $shell = "python {$path} $clean $lemma $topics $words $interaction $typeModeling";
    $cmdResult = exec($shell);
    
    #pode retornar 4 coisas: Erro SQL - Erro LDA - Erro EXCEL - OK + 'caminho do arquivo'
    echo $cmdResult;
} else {
    echo "nada recebido!";
}
?>