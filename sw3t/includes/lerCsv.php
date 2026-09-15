<?php


// if(empty($idProjeto)){ echo "Oops";die; } //acessado diretamente

//$file = file_get_contents($sw3tLocation.$destinoArquivo);
//$lines = explode(PHP_EOL,$file);

$quantidadeArtigosAcm = 0;
$quantidadeArtigos = 0;
$quantidadeFalhas = 0;
$idsAcm="";

/*
authors,title,abstract,type,year,journal



 */

$file = fopen($sw3tLocation.$destinoArquivo, 'r');




$fonte=numbers($_POST['fonte']);
while (($line = fgetcsv($file)) !== FALSE) {
//     print_r($artigo);
//     die;
//    $line = explode(",",$line);
    $artigo = array();
    $artigo['title'] = str_replace("   ","",$line[1]);
    $artigo['author'] = implode(' ,',$line[0]);


    $artigo['abstract'] = str_replace("   ","",$line[2]);
    $tipoArtigo = (sanitize($line[3]));
    $tituloArtigo = (sanitize (tirarQuebraLinha($artigo['title'])));
    $journal = (sanitize (tirarQuebraLinha(@$line[5])));
    $anoArtigo = (sanitize($line[4]));
    $autoresArtigo = (sanitize($artigo['author']));
    // $resumoArtigo = html_entity_decode($artigo['abstract']);
    $resumoArtigo = ($artigo['abstract']);
    $resumoArtigo = (sanitize( tirarQuebraLinha($resumoArtigo)));
    $resumoArtigo = str_replace("(C)","&copy;",$resumoArtigo);
    $resumoArtigo = str_replace("("," (",$resumoArtigo);
    $resumoArtigo = str_replace("  "," ",$resumoArtigo);
    $resumoArtigo = str_replace('&nbsp'," ",$resumoArtigo);
    $bibBackup = sanitize(base64_encode($artigo['backup']));
    $quantidadeArtigos ++;
    $sql = "INSERT INTO `resumo` (`id`, `idProjeto`, `tituloArtigo`, `resumo`, `resumoLimpo`, `resumoEnxuto`, `anoPublicacao`, `idFonte`, `journal`, resumoLematizado, bibBackup) VALUES (NULL, '$idProjeto', lower('$tituloArtigo'), lower('$resumoArtigo'),'', '', '$anoArtigo','$fonte','$journal','','{$bibBackup}')";
    $result = $link->query($sql);
    $idResumo = mysqli_insert_id($link);
    $autoresArtigo = explode(";",$autoresArtigo);
    foreach ($autoresArtigo as $autorArtigo){
        $sql = "INSERT INTO `autores` (`id`, `idProjeto`, `idResumo`, `nomeAutor`) VALUES (NULL, '{$idProjeto}', '{$idResumo}', lower('{$autorArtigo}'))";
        $result = $link->query($sql);
    }

}
fclose($file);
if($quantidadeFalhas == 0 ){
    $out= "document.getElementById('result').innerHTML='$quantidadeArtigos artigo(s) Foram enviados<br>";
}else{
    $out= "document.getElementById('result').innerHTML='$quantidadeArtigos artigo(s) Foram enviados<br> $quantidadeFalhas Não foram cadastrados ( artigos sem resumo ou autor )";
    foreach ($falhas as $falha){
        if( empty ($falha['title'])){
            $out .= "<br>FALHOU e não tinha um titulo";
        }else{
            $out .= "<br>FALHOU : ".addslashes(sanitize($falha['title']));
        }
    }



}


if($quantidadeArtigosAcm != 0 ){

    $out .="<h2>Fora detectado {$quantidadeArtigosAcm} Artigos da acm, deseja baixar ? <button onclick=\'baixarTodos()\' id=\'botaoTudo\' style=\'color:white;\' class=\'btn btn-success\'>Baixar Tudo</button></h2><br><div id=\'resultAcm\'></div><br><div id=\'falhasAcm\'></div>";
    // print_r($acm);
    // echo $quantidadeArtigosAcm;
    foreach($acm as $artigo){


        $tituloArtigo = sanitize($artigo['title']);

        if(empty($artigo['year'])){

            // print_r($artigo);die;
        }
        $anoArtigo = sanitize($artigo['year']);

        if(empty($artigo['author'])){

            // print_r($artigo);die;
        }
        $autorArtigo = sanitize($artigo['author']);

        $idAcm = sanitize($artigo['acmid']);
        $resumoArtigo = "";


        $quantidadeArtigos ++;
        $sql = "INSERT INTO `resumo` (`id`, `idProjeto`, `tituloArtigo`, `resumo`, `resumoEnxuto`, `anoPublicacao`, `idAcm`) VALUES (NULL, '$idProjeto', lower('$tituloArtigo'), lower('$resumoArtigo'), '', '$anoArtigo', '$idAcm')";
        // echo $sql;die;
        $result = $link->query($sql);
        $idResumo = mysqli_insert_id($link);
        $sql = "INSERT INTO `autores` (`id`, `idProjeto`, `idResumo`, `nomeAutor`) VALUES (NULL, '{$idProjeto}', '{$idResumo}', lower('{$autorArtigo}'))";
        $result = $link->query($sql);


        // $out .= "<br>".addslashes($tituloArtigo)." -  <button onclick='baixar({$idInsertAcm})' id='botao{$idInsertAcm}' style='color:white;' class='btn btn-success'>Baixar</button>";

    }


}
$out .= "';";


// $out.= "document.getElementById('file-input').style.background-color='red';";
$out.= "document.getElementById('file-input').style.cssText='background-color: #28a745; color: #fff;';";

// print_r($falhas);
echo $out;
die;
// erro("1");
