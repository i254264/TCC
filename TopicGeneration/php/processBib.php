<?php
include_once('conexaoDB.php');

$tableName = 'tabela_topicgeneration'; // Tabela final
$content = file_get_contents($destinoArquivo . $nomeArquivo);

// Regex para separar cada entrada @ARTICLE, @PROCEEDINGS, etc.
preg_match_all('/@\w+\{(.*?),/s', $content, $matches, PREG_OFFSET_CAPTURE);

$entries = [];
for ($i = 0; $i < count($matches[0]); $i++) {
    $start = $matches[0][$i][1];
    $end = isset($matches[0][$i+1]) ? $matches[0][$i+1][1] : strlen($content);
    $entryText = substr($content, $start, $end - $start);

    // Extração simples via Regex para Title, Year e Abstract
    $title = preg_match('/title\s*=\s*[\{"](.*?)[\}"]/s', $entryText, $m) ? $m[1] : '';
    $year = preg_match('/year\s*=\s*[\{"](.*?)[\}"]/s', $entryText, $m) ? $m[1] : '';
    $abstract = preg_match('/abstract\s*=\s*[\{"](.*?)[\}"]/s', $entryText, $m) ? $m[1] : '';

    // Função interna para limpar chaves residuais comuns em BibTeX
    $clean = function($text) {
        return trim(str_replace(['{', '}', "\r", "\n"], ['', '', ' ', ' '], $text));
    };

    if ($title || $abstract) {
        $cleanAbstract = $clean($abstract);
        $entries[] = [
            'title' => $clean($title),
            'year' => $clean($year),
            'abstract' => $cleanAbstract
        ];
    }
}

if (!empty($entries)) {
    try {
        // Garante que a coluna 'col' (usada pelo Python) receba o abstract
        $sql = "CREATE TABLE IF NOT EXISTS $tableName (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title TEXT,
            year VARCHAR(10),
            abstract TEXT,
            col TEXT 
        )";
        $conn->exec($sql);

        $stmt = $conn->prepare("INSERT INTO $tableName (title, year, abstract, col) VALUES (?, ?, ?, ?)");
        foreach ($entries as $e) {
            // Salvamos o abstract em 'col' para compatibilidade com o script Python existente
            $stmt->execute([$e['title'], $e['year'], $e['abstract'], $e['abstract']]);
        }
        $messages[] = count($entries) . " referências bibliográficas importadas.";
    } catch (PDOException $e) {
        $messagesError[] = "Erro ao inserir BibTeX: " . $e->getMessage();
    }
}
?>