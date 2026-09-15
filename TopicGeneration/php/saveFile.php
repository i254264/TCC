<?php
include_once('conexaoDB.php');
$DBName = $db; // Define para compatibilidade com scripts incluídos
$messages = array();
$messagesError = array();
//respostaAjax = 0: Erro
//respostaAjax = 1: Deu tudo certo
//respostaAjax = 2: Apenas criou tabela auxiliar sem problemas
$respostaAjax = 0;
//BDjaCriado = 0: BD não existe
//BDjaCriado = 1: existe BD e vai fazer pergunta
//BDjaCriado = 2: existe BD e vai adicionar mais dados ao que já tem
//BDjaCriado = 3: existe BD e vai excluir dados que já existam antes
$BDjaCriado = isset($_POST['BDjaCriado']) ? (string)$_POST['BDjaCriado'] : '0';
$columnDrop = isset($_POST['columnDrop']) ? $_POST['columnDrop'] : null;
$header = NULL;

if($BDjaCriado != '2' && $BDjaCriado != '3'){
    //verifica se o BD já existe
    include_once('BDexist.php');
}

if($BDjaCriado === '2' || $BDjaCriado === '3') {
    //clicou continue então vai adicionar mais dados os dados que já existem
    include_once('dropColumns.php');
    include_once('copyColumns.php');

}else{
    if (!empty($_FILES['files']['name'])) {
        $files = $_FILES['files'];
    
        // Diretório onde os arquivos serão salvos
        $destinoArquivo = "filesSent/";
    
        // Verifica se o diretório de destino existe; se não, cria-o
        if (!file_exists($destinoArquivo) && !is_dir($destinoArquivo)) {
            mkdir($destinoArquivo, 0777, true);
        }
    
        // Loop através de cada arquivo enviado
        foreach ($files['tmp_name'] as $key => $tmp_name) {
            $nomeArquivo = $files['name'][$key];
    
            // Move o arquivo para o diretório de destino
            if (move_uploaded_file($tmp_name, $destinoArquivo . $nomeArquivo)) {
                $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
                if ($extensao === 'bib') {
                    $fileToProcess = $destinoArquivo . $nomeArquivo;
                    include('processBib.php');
                    $messages[] = "Arquivo BibTeX processado: $nomeArquivo";
                    $respostaAjax = 1; 
                } else {
                    $messages[] = "SUCESSO ao salvar os arquivos";
                    $respostaAjax = 2;
                    include_once('test.php');
                }
            } else {
                $messagesError[] = "Error when saving files";
                $respostaAjax = 0;
            }
        }
    } else {
        $messagesError[] = "No files sent";
        $respostaAjax = 0;
    }
}

if ($BDjaCriado === '0' && $columnDrop != 'null') {
    include_once('dropColumns.php');
    include_once('copyColumns.php');
}

echo json_encode(array(
    'BD' => $BDjaCriado,
    'columnsName' => $header,
    'response' => $respostaAjax,
    '$columnDrop' => $columnDrop,
    'messages' => $messages,
    'messagesError' => $messagesError
));
?>
