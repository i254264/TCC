<?php

// if(empty($idProjeto)){ echo "Oops";die; } //acessado diretamente

/* Tem que dar include nessa ordem pois ele é feito para autoload  */
require_once($sw3tLocation."vendors/liteBibLibParser.php");

$bib = parseBib($sw3tLocation.$destinoArquivo);

$quantidadeArtigosAcm = 0;
$quantidadeArtigos = 0;
$quantidadeFalhas = 0;
$idsAcm = "";
$falhas = array();
$acm = array();

$fonte = numbers($_POST['fonte']);
foreach ($bib as $artigo) {
    $artigo = array_change_key_case($artigo, CASE_LOWER);
    $artigo['title'] = isset($artigo['title']) ? $artigo['title'] : '';
    $artigo['author'] = isset($artigo['author']) ? $artigo['author'] : '';
    $artigo['abstract'] = isset($artigo['abstract']) ? $artigo['abstract'] : '';

    if (empty($artigo['title'])) {
        $falhas[] = $artigo;
        $quantidadeFalhas++;
        continue;
    }

    if (empty($artigo['author'])) {
        $falhas[] = $artigo;
        $quantidadeFalhas++;
        continue;
    }

    if (isset($artigo['acmid']) && !empty($artigo['author'])) {
        $acm[] = $artigo;
        $quantidadeArtigosAcm++;
        continue;
    }

    $artigo['title'] = str_replace("   ", "", $artigo['title']);
    $artigo['abstract'] = str_replace("   ", "", $artigo['abstract']);

    $tipoArtigo = sanitize($artigo['type']);
    $tituloArtigo = sanitize(tirarQuebraLinha($artigo['title']));
    $journal = sanitize(tirarQuebraLinha($artigo['journal']));
    $anoArtigo = sanitize($artigo['year']);
    $autoresArtigo = sanitize($artigo['author']);
    $resumoArtigo = sanitize(tirarQuebraLinha($artigo['abstract']));
    $resumoArtigo = str_replace("(C)", "&copy;", $resumoArtigo);
    $resumoArtigo = str_replace("(", " (", $resumoArtigo);
    $resumoArtigo = str_replace("  ", " ", $resumoArtigo);
    $resumoArtigo = str_replace('&nbsp', " ", $resumoArtigo);

    $bibBackup = sanitize(base64_encode($artigo['backup'] ?? ''));

    $quantidadeArtigos++;
    $sql = "INSERT INTO `resumo` (`id`, `idProjeto`, `tituloArtigo`, `resumo`, `resumoLimpo`, `resumoEnxuto`, `anoPublicacao`, `idFonte`, `journal`, resumoLematizado, bibBackup) VALUES (NULL, '$idProjeto', lower('$tituloArtigo'), lower('$resumoArtigo'),'', '', '$anoArtigo','$fonte','$journal','','{$bibBackup}')";
    $result = $link->query($sql);
    $idResumo = mysqli_insert_id($link);

    $autoresArtigo = explode("and", $autoresArtigo);
    foreach ($autoresArtigo as $autorArtigo) {
        $sql = "INSERT INTO `autores` (`id`, `idProjeto`, `idResumo`, `nomeAutor`) VALUES (NULL, '{$idProjeto}', '{$idResumo}', lower('{$autorArtigo}'))";
        $result = $link->query($sql);
    }
}

if ($quantidadeFalhas == 0) {
    $out = "document.getElementById('result').innerHTML='$quantidadeArtigos artigo(s) Foram enviados<br>";
} else {
    $out = "document.getElementById('result').innerHTML='$quantidadeArtigos artigo(s) Foram enviados<br> $quantidadeFalhas Não foram cadastrados ( artigos sem resumo ou autor )";
    foreach ($falhas as $falha) {
        if (empty($falha['title'])) {
            $out .= "<br>FALHOU e não tinha um titulo";
        } else {
            $out .= "<br>FALHOU : ".addslashes(sanitize($falha['title']));
        }
    }
}

if ($quantidadeArtigosAcm != 0) {
    $out .= "<h2>Fora detectado {$quantidadeArtigosAcm} Artigos da acm, deseja baixar ? <button onclick='baixarTodos()' id='botaoTudo' style='color:white;' class='btn btn-success'>Baixar Tudo</button></h2><br><div id='resultAcm'></div><br><div id='falhasAcm'></div>";
    foreach ($acm as $artigo) {
        $tituloArtigo = sanitize($artigo['title']);
        $anoArtigo = sanitize($artigo['year']);
        $autorArtigo = sanitize($artigo['author']);
        $idAcm = sanitize($artigo['acmid']);
        $resumoArtigo = "";

        $quantidadeArtigos++;
        $sql = "INSERT INTO `resumo` (`id`, `idProjeto`, `tituloArtigo`, `resumo`, `resumoEnxuto`, `anoPublicacao`, `idAcm`) VALUES (NULL, '$idProjeto', lower('$tituloArtigo'), lower('$resumoArtigo'), '', '$anoArtigo', '$idAcm')";
        $result = $link->query($sql);
        $idResumo = mysqli_insert_id($link);
        $sql = "INSERT INTO `autores` (`id`, `idProjeto`, `idResumo`, `nomeAutor`) VALUES (NULL, '{$idProjeto}', '{$idResumo}', lower('{$autorArtigo}'))";
        $result = $link->query($sql);
    }
}

$out .= "';";
$out .= "document.getElementById('file-input').style.cssText='background-color: #28a745; color: #fff;';";

echo $out;
die;
