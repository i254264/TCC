<?php
include_once('conexaoDB.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bib_file'])) {
    $fileContent = file_get_contents($_FILES['bib_file']['tmp_name']);
    
    // Regex para separar cada entrada @tipo{...}
    preg_match_all('/@\w+\s*\{[^@]+/s', $fileContent, $entries);

    $stmt = $conn->prepare("INSERT INTO tabela_topicgeneration (title, year, abstract, col) VALUES (?, ?, ?, ?)");

    $count = 0;
    foreach ($entries[0] as $entry) {
        // Extração simples via regex dos campos desejados
        $title = extractField('title', $entry);
        $year = extractField('year', $entry);
        $abstract = extractField('abstract', $entry);

        if ($title || $abstract) {
            // Salvamos o abstract em 'abstract' (exibição) e 'col' (para o Python)
            $stmt->execute([$title, $year, $abstract, $abstract]);
            $count++;
        }
    }
    $messages[] = "$count registros importados do arquivo BibTeX.";
}

function extractField($field, $text) {
    // Procura por campo = {valor} ou campo = "valor" ou campo = valor
    $pattern = '/' . $field . '\s*=\s*[\{"]?\s*(.*?)\s*[\}"]?\s*[,}]/i';
    if (preg_match($pattern, $text, $match)) {
        $value = $match[1];
        // Limpeza básica de chaves do BibTeX
        $value = str_replace(['{', '}', '\\'], '', $value);
        return trim($value);
    }
    return null;
}
?>