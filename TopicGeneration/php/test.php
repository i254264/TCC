<?php
include_once('conexaoDB.php');
$tableName = 'auxiliar';

if (!function_exists('sanitizeHeaderColumn')) {
    function sanitizeHeaderColumn($str) {
        $str = preg_replace('/[^\w\s-]/u', '', (string)$str);
        $str = preg_replace('/[\s-]+/', '_', trim($str));
        return $str !== '' ? $str : 'col_' . uniqid();
    }
}

$arquivos = array();
if (isset($fileToProcess) && file_exists($fileToProcess)) {
    $arquivos[] = $fileToProcess;
} else {
    $diretorio = "filesSent/";
    $arquivos = glob($diretorio . "*.csv") ?: array();
}

if (!empty($arquivos)) {
    foreach ($arquivos as $fileName) {
        if (file_exists($fileName)) {
            // Verifica a conexão com o banco de dados
            if (isset($conn)) {
                $sqlSelect = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$DBName' AND table_name = '$tableName';";
                $stmt = $conn->query($sqlSelect);
                $tableExists = $stmt->fetchColumn();
                if ($tableExists) {
                    $messages[] = "Ja existem dados no banco auxiliar.";
                    try {
                        $sqlDrop = "DROP TABLE $tableName";
                        $conn->exec($sqlDrop);
                        $messages[] = "Dados excluidos com sucesso!";
                    } catch (PDOException $e) {
                        $messagesError[] = "Failed to drop auxiliary table: " . $e->getMessage();
                        $respostaAjax = 0;
                    }
                }
                // Abre o arquivo em modo leitura
                $fileNameOpen = fopen($fileName, "r");
                if ($fileNameOpen !== FALSE) {
                    // Lê a primeira linha do arquivo (cabeçalho)
                    $header = fgetcsv($fileNameOpen);
                    if ($header !== FALSE && !empty($header)) {
                        // Sanitiza os nomes das colunas
                        $header = array_map('sanitizeHeaderColumn', $header);

                        // Irá criar as colunas da tabela
                        try {
                            $sqlTable = "CREATE TABLE `$tableName` (";
                            $sqlTable .= "`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, ";
                            foreach ($header as $columnName) {
                                $sqlTable .= "`$columnName` TEXT NULL, ";
                            }
                            $sqlTable = rtrim($sqlTable, ", ") . ");";
                            $conn->exec($sqlTable);
                            $messages[] = 'Tabela auxiliar criada com sucesso!';
                        } catch (PDOException $e) {
                            $messagesError[] = "Failed to create auxiliary table: " . $e->getMessage();
                            $respostaAjax = 0;
                        }

                        // Inserir dados
                        try {
                            // Começa uma transação
                            $conn->beginTransaction();

                            // Prepara a consulta de inserção
                            $sqlInsert = "INSERT INTO $tableName (`" . implode("`, `", $header) . "`) VALUES (" . implode(',', array_fill(0, count($header), '?')) . ")";
                            $stmt = $conn->prepare($sqlInsert);

                            // Contador para controlar o número de linhas processadas
                            $rowCount = 0;

                            // Lê as linhas do arquivo e insere os dados na tabela
                            while (($row = fgetcsv($fileNameOpen)) !== FALSE) {
                                try {
                                    // Preencher a linha do CSV com valores vazios se necessário
                                    $row = array_pad($row, count($header), '');

                                    // Atribui valores aos parâmetros do statement preparado
                                    $stmt->execute($row);
                                    $rowCount++;

                                    // Como está no while, fazer o commit a cada 1000 linhas do csv (ajuda no desempenho)
                                    if ($rowCount % 1000 == 0) {
                                        $conn->commit();
                                        // Inicia uma nova transação
                                        $conn->beginTransaction();
                                    }
                                } catch (PDOException $e) {
                                    // Ignora a linha problemática e continua para a próxima
                                    // ESSE ERRO PASSA
                                    $messages[] = 'Linha ' . $rowCount . ' apresentou problema e foi excluida.';
                                    continue;
                                }
                            }

                            // Faz commit da transação final
                            $conn->commit();
                            $messages[] = 'Dados inseridos com sucesso!';
                        } catch (PDOException $e) {
                            // Se ocorrer um erro ao preparar a consulta, faz rollback da transação
                            $conn->rollback();
                            $messagesError[] = "Transaction insertion failed: " . $e->getMessage();
                            $respostaAjax = 0;
                        }
                    } else {
                        $messageError[] = "Unable to read csv header";
                        $respostaAjax = 0;
                    }

                    // Fecha o arquivo após a leitura
                    fclose($fileNameOpen);
                } else {
                    $messagesError[] = "Unable to read csv";
                    $respostaAjax = 0;
                }
            } else {
                $messagesError[] = 'Unable to connect to MySQL database';
                $respostaAjax = 0;
            }
        } else {
            $messagesError[] = 'csv file does not exist or path was not found';
            $respostaAjax = 0;
        }
    }
} else {
    // Se nenhum arquivo foi encontrado, exibe uma mensagem
    $messagesError[] = "No files found in folder $diretorio";
    $respostaAjax = 0;
}
