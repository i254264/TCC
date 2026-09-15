<?php


// if(empty($idProjeto)){ echo "Oops";die; } //acessado diretamente

/* Tem que dar include nessa ordem pois ele é feito para autoload  */

// echo "1";

//require_once($sw3tLocation."vendors/liteBibLibParser.php");
// include("../vendors/bib/bibtexParser.php");

// $bib = fopen($destinoArquivo, "r") or erro("BIB ERROR 1");
// $bibtex = fread($bib,filesize($destinoArquivo));
// $destinoArquivo = "tmp/6ea3a88b5a81acdbb776e71490ba0b59.ini";

$xml = file_get_contents($sw3tLocation.$destinoArquivo);
$xml = simplexml_load_string($xml);
$xml = @json_decode(json_encode($xml),true);

//print_r($xml);
//echo "<pre>";
// print_r($bib);
$quantidadeArtigosAcm = 0;
$quantidadeArtigos = 0;
$quantidadeFalhas = 0;
$idsAcm="";
// print_r($xml['records']);die;
/*

Array
(
    [contributors] => Array
        (
            [authors] => Array
                (
                    [author] =>
            Nassif, Lilian Noronha

                )

            [secondary-authors] => Array
                (
                )

        )

    [titles] => Array
        (
            [title] => Conspiracy communication reconstitution from distributed instant messages timeline
        )

    [dates] => Array
        (
            [year] => 2019
            [pub-dates] => Array
                (
                    [date] => 4
                )

        )

    [pages] => 1-6
    [electronic-resource-num] => 10.1109/WCNCW.2019.8902574
    [abstract] => In a cyber attack, several kinds of evidence must be verified to find the responsible. Seizing cell phones became a usual procedure in investigations and digital forensics may be demanded to verify conspiracies about the committed crime. The accelerated growth of cell phones as an indispensable accessory nowadays, followed by a massive adherence to Instant Messaging (IM) applications, has changed the way mankind is interacting virtually. Although IM applications provide several benefits for real-time contact and knowledge sharing, they are also used in private communications for illicit arrangements. When several cell phones are seized in a police operation, many communications using IM may be used as evidence of the committed crime. An investigator spends many hours trying to rearrange this conversation or reading repeated information. This work presents a solution to this problem, retrieving all conversations sequentially from a set of cell phones that participate in the same WhatsApp group. The solution is illustrated with a case study that validates the model and algorithm developed for this purpose. First results pointed to 52% decreasing of investigation effort in some situations.




 */

$fonte=numbers($_POST['fonte']);
foreach ($xml['records']['record'] as $artigo){
//     print_r($artigo);
//     die;

    $artigo = array_change_key_case ($artigo,CASE_LOWER);
    $artigo['title'] = str_replace("   ","",$artigo['titles']['title']);
    if(empty($artigo['abstract'])){

        if(isset($artigo['acmid']) and !empty($artigo['author'])){

            $acm[]=$artigo;
            $quantidadeArtigosAcm++;
            continue;
        }else{

//            print_r($artigo);
//             die;
            $falhas[]=$artigo;
            $quantidadeFalhas++;

        }
        // print_r($artigo);die;
    }else{
		// print_r($artigo['contributors']['authors']);
		
		// die;
        
		if(isset($artigo['contributors']['authors']['author'])){
			if(is_array($artigo['contributors']['authors']['author'])){
				$artigo['author'] = implode(';',$artigo['contributors']['authors']['author']);	
			}else{
				$artigo['author'] =$artigo['contributors']['authors']['author'];		
			}
			
		}else{
			$artigo['author'] = implode(';',$artigo['contributors']['authors']);
		}
        if(empty($artigo['author'])){
//             print_r($artigo);
//             die;
            $falhas[]=$artigo;
            $quantidadeFalhas++;
            continue;

        }

//         $artigo['author'] = implode(', ',$artigo['contributors']['authors']);
        
//         if(empty($artigo['author'])){
// //             print_r($artigo);
// //             die;
//             $falhas[]=$artigo;
//             $quantidadeFalhas++;
//             continue;

//         }
//         else{
//             foreach ($artigo['contributors']['authors'] as $value) {
//                 $artigo['author'] = implode(', ', $value);
//             }
//         }

        $artigo['abstract'] = str_replace("   ","",$artigo['abstract']);
		if(empty($artigo['type'])){
			$artigo['type'] = '';
			}
        $tipoArtigo = (sanitize($artigo['type']));
        $tituloArtigo = (sanitize (tirarQuebraLinha($artigo['title'])));

        /*
         * periodical] => Array
        (
            [full-title
         */

        $journal = (sanitize (tirarQuebraLinha(@$artigo['periodical']['full-title'])));
        $anoArtigo = (sanitize($artigo['dates']['year']));
        $autoresArtigo = (sanitize($artigo['author']));
        // $resumoArtigo = html_entity_decode($artigo['abstract']);
        $resumoArtigo = ($artigo['abstract']);
        $resumoArtigo = (sanitize( tirarQuebraLinha($resumoArtigo)));

        $resumoArtigo = str_replace("(C)","&copy;",$resumoArtigo);


        /* REMOVER CODIGO ABAIXO CASO ESTEJA DANDO ERROS DE VELOCIDADE */


        $resumoArtigo = str_replace("("," (",$resumoArtigo);
        $resumoArtigo = str_replace("  "," ",$resumoArtigo);
        // $resumoArtigo = str_replace("  "," ",$resumoArtigo);
        /* ////\\\\ */
        // print_r($artigo);die;


        $resumoArtigo = str_replace('&nbsp'," ",$resumoArtigo);
        // $autorArtigo = str_replace("-","",$autorArtigo);

        $bibBackup = sanitize(base64_encode(json_encode($artigo)));
		$journal = substr($journal,0,253);
		$tituloArtigo = substr($tituloArtigo,0,253);
        
        $sql = "INSERT INTO `resumo` (`id`, `idProjeto`, `tituloArtigo`, `resumo`, `resumoLimpo`, `resumoEnxuto`, `anoPublicacao`, `idFonte`, `journal`, resumoLematizado, bibBackup, `area_conhecimento`) VALUES (NULL, '$idProjeto', lower('$tituloArtigo'), lower('$resumoArtigo'),'', '', '$anoArtigo','$fonte','$journal','','{$bibBackup}', '$area_conhecimento')";
        // echo $sql;die;
        $result = $link->query($sql);
		if(!$result){
			$quantidadeFalhas++;
			$artigo['sql'] = $sql;
			$falhas[]=$artigo;
		}else{
			$quantidadeArtigos ++;
		}
        $idResumo = mysqli_insert_id($link);


        $autoresArtigo = explode(";",$autoresArtigo);
        foreach ($autoresArtigo as $autorArtigo){

            $sql = "INSERT INTO `autores` (`id`, `idProjeto`, `idResumo`, `nomeAutor`) VALUES (NULL, '{$idProjeto}', '{$idResumo}', lower('{$autorArtigo}'))";
            $result = $link->query($sql);

        }


    }
}
if($quantidadeFalhas == 0 ){
    $out= "document.getElementById('result').innerHTML='$quantidadeArtigos artigo(s) Foram enviados<br>";
}else{
    $out= "document.getElementById('result').innerHTML='$quantidadeArtigos artigo(s) Foram enviados<br> $quantidadeFalhas Não foram cadastrados ( artigos sem resumo ou autor )";
	$sql = false;
    foreach ($falhas as $falha){
        if( empty ($falha['title'])){
            $out .= "<br>FALHOU e não tinha um titulo";
        }else{
            $out .= "<br>FALHOU : ".addslashes(sanitize($falha['title']));
			if(!empty($falha['sql'])){
				$sql = $falha['sql'];
			}
        }
    }
	if($sql != false){
		echo "console.log('".base64_encode($sql)."');";;
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
