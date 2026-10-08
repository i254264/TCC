<?php
include_once('conexaoDB.php');

$fileContent = '';
if (isset($fileToProcess) && file_exists($fileToProcess)) {
    $fileContent = file_get_contents($fileToProcess);
} elseif (isset($_FILES['ris_file'])) {
    $fileContent = file_get_contents($_FILES['ris_file']['tmp_name']);
}

if ($fileContent) {
    // Remove BOM UTF-8 se presente
    $fileContent = preg_replace('/^\xEF\xBB\xBF/', '', $fileContent);

    // Divide o conteúdo em linhas normalizadas
    $lines = preg_split('/\r\n|\r|\n/', $fileContent);

    $records = array();
    $currentRecord = array();
    $currentTag = null;

    foreach ($lines as $line) {
        // Padrão de tags RIS: 2 caracteres alfanuméricos seguidos de espaços e hífen (ex: "TY  - ", "AB  - ")
        if (preg_match('/^([A-Z0-9]{2})\s*-\s*(.*)$/', $line, $matches)) {
            $currentTag = trim($matches[1]);
            $value = trim($matches[2]);

            if ($currentTag === 'TY') {
                if (!empty($currentRecord)) {
                    $records[] = $currentRecord;
                }
                $currentRecord = array('TY' => $value);
            } elseif ($currentTag === 'ER') {
                if (!empty($currentRecord)) {
                    $records[] = $currentRecord;
                    $currentRecord = array();
                }
                $currentTag = null;
            } else {
                if (!isset($currentRecord[$currentTag])) {
                    $currentRecord[$currentTag] = $value;
                } else {
                    $currentRecord[$currentTag] .= ' ' . $value;
                }
            }
        } elseif ($currentTag !== null && trim($line) !== '') {
            // Linhas de continuação (como abstracts longos)
            if (isset($currentRecord[$currentTag])) {
                $currentRecord[$currentTag] .= ' ' . trim($line);
            }
        }
    }

    if (!empty($currentRecord)) {
        $records[] = $currentRecord;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO tabela_topicgeneration (title, year, abstract, col) VALUES (?, ?, ?, ?)");
        $count = 0;

        foreach ($records as $rec) {
            $title = isset($rec['TI']) ? $rec['TI'] : (isset($rec['T1']) ? $rec['T1'] : (isset($rec['CT']) ? $rec['CT'] : null));
            $abstract = isset($rec['AB']) ? $rec['AB'] : (isset($rec['N2']) ? $rec['N2'] : null);

            $rawYear = isset($rec['PY']) ? $rec['PY'] : (isset($rec['Y1']) ? $rec['Y1'] : (isset($rec['DA']) ? $rec['DA'] : null));
            $year = null;
            if ($rawYear && preg_match('/\b(19\d{2}|20\d{2})\b/', $rawYear, $yMatch)) {
                $year = $yMatch[1];
            } elseif ($rawYear && preg_match('/\b\d{4}\b/', $rawYear, $yMatch)) {
                $year = $yMatch[0];
            }

            if ($title || $abstract) {
                $colContent = !empty($abstract) ? $abstract : $title;
                $stmt->execute(array($title, $year, $abstract, $colContent));
                $count++;
            }
        }

        if ($count > 0) {
            $messages[] = "$count registros importados do arquivo RIS.";
        } else {
            $messagesError[] = "Nenhum registro com título ou resumo foi encontrado no arquivo RIS.";
            $respostaAjax = 0;
        }
    } catch (PDOException $e) {
        $messagesError[] = "Erro ao inserir registros RIS no banco: " . $e->getMessage();
        $respostaAjax = 0;
    }
}
?>