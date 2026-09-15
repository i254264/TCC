<?php
require_once("config.php");
//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'], $_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);


$idOutput = numbers($_GET['idOutput']);

//padrao pesquisa
if (!isset($_GET['filtroPesquisa'])){
    $filtroPesquisa = "todos";
}
else{
    $filtroPesquisa = $_GET['filtroPesquisa'];
};

$sql = "Select idProjeto,output,idTrabalho from output where id = '{$idOutput}'";
$result = $link->query($sql);
$row = $result->fetch_assoc();

$idProjeto = $row['idProjeto'];
$idTrabalho = $row['idTrabalho'];

// echo $sql;die;

donoProjeto($idUsuario, $idProjeto);
$nomeProjeto = sanitize(dadosProjeto("nome", $idProjeto));
$nomeProjeto = mb_substr($nomeProjeto, 0, 20);
$_SESSION['idProjeto'] = $idProjeto;
$_SESSION['nomeProjeto'] = $nomeProjeto;
$output = limparOutputPython($row['output']);

$idOutputLda = explode(",", $row['output']);
$idOutputLda = str_replace("(", "", $idOutputLda[0]);
//echo $idOutputLda;die;




?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    <?php include("includes/head.php"); ?>
    <title>
        <?php echo $title ?> - Topico
    </title>
</head>

<body>
    <!-- Left Panel -->
    <?php $onde = "analises";
    include("includes/menu.php"); ?>
    <!-- Left Panel -->

    <!-- Right Panel -->

    <div id="right-panel" class="right-panel">

        <!-- Header-->
        <?php include("includes/header.php"); ?>

        <!-- /header -->
        <!-- Header-->

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Tópico</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="main.php">Sw3t</a></li>
                            <li><a href="meusProjetos.php">Meus Projetos</a></li>
                            <li><a href="gerirProjeto.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>"><?php echo $_SESSION['nomeProjeto'] ?></a></li>
                            <li><a href="analises.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Analises</a></li>
                            <li class="active">Tópico</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content mt-3">
            <div class="animated fadeIn">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Relação de Artigos por tópico</strong>
                            </div>
                            <div class="card-body">

                                <?php
                                foreach ($output as $outputPalavra) {
                                    $valor = $outputPalavra['valor'];
                                    $valor = $valor * 1000;
                                    $valor = $valor * 1.5;
                                    //echo "<img src='{$urlSw3t}/piramide.png' style='width:{$valor}px;height:5px;' >";
                                    echo "<img src='piramide.png' style='width:{$valor}px;height:5px;'>";

                                    echo " " . $outputPalavra['palavra'] . "<br>";
                                } ?>
                                <br>

                                <a class="btn btn-primary"
                                    href="analise.php?idTrabalho=<?php echo $idTrabalho ?>">Voltar</a>
                                <br>
                                <br>
                                <a href="?idOutput=<?php echo $idOutput ?>&excluirTodos=1"
                                    class='btn btn-danger btn-sm'>Excluir todos os resumos desse resumo</a>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function deletar(idArtigo) {
                        $.ajax({
                            type: 'GET',
                            url: "verArtigo.php?idArtigo=" + idArtigo + "&deletar=true&close=true",
                            success: function (data) {
                                eval(data);
                            }
                        });

                    }

                    function excluir(idArtigo) {

                        document.getElementById('status' + idArtigo).innerHTML = "<i class='fa fa-dot-circle-o'></i> Excluido</button>";
                        document.getElementById('status' + idArtigo).className = "btn btn-danger btn-sm";
                        document.getElementById('status' + idArtigo).setAttribute("onClick", "javascript: incluir(" + idArtigo + ");");

                        var element = document.getElementById('Astatus' + idArtigo);

                        //If it isn't "undefined" and it isn't "null", then it exists.
                        if (typeof (element) != 'undefined' && element != null) {
                            //existe
                            document.getElementById('Astatus' + idArtigo).innerHTML = "<i class='fa fa-dot-circle-o'></i> Excluido</button>";
                            document.getElementById('Astatus' + idArtigo).className = "btn btn-danger btn-sm";
                            document.getElementById('Astatus' + idArtigo).setAttribute("onClick", "javascript: incluir(" + idArtigo + ");");
                        }

                        $.ajax({
                            type: 'POST',
                            url: 'ajaxStatus.php',
                            data: "status=excluido&idArtigo=" + idArtigo,
                            success: function (data) {


                            }
                        });



                    }
                    function incluir(idArtigo) {
                        document.getElementById('status' + idArtigo).innerHTML = "<i class='fa fa-dot-circle-o'></i> Incluido</button>";
                        document.getElementById('status' + idArtigo).className = "btn btn-success btn-sm";
                        // document.getElementById('status'+idArtigo).onclick   = incluir(idArtigo); 
                        document.getElementById('status' + idArtigo).setAttribute("onClick", "javascript: excluir(" + idArtigo + ");");

                        var element = document.getElementById('Astatus' + idArtigo);

                        //If it isn't "undefined" and it isn't "null", then it exists.
                        if (typeof (element) != 'undefined' && element != null) {
                            //existe
                            document.getElementById('Astatus' + idArtigo).innerHTML = "<i class='fa fa-dot-circle-o'></i> Incluido</button>";
                            document.getElementById('Astatus' + idArtigo).className = "btn btn-success btn-sm";
                            document.getElementById('Astatus' + idArtigo).setAttribute("onClick", "javascript: excluir(" + idArtigo + ");");
                        }

                        $.ajax({
                            type: 'POST',
                            url: 'ajaxStatus.php',
                            data: "status=incluido&idArtigo=" + idArtigo,
                            success: function (data) {


                            }
                        });
                    }

                    function motivo(idArtigo) {
                        $.ajax({
                            type: 'POST',
                            url: 'ajaxStatus.php',
                            data: "motivo=true&idArtigo=" + idArtigo,
                            success: function (data) {

                                document.getElementById('resultMotivo').innerHTML = data;

                            }
                        });
                    }

                    function salvarMotivo() {
                        document.getElementById('resultSalvarMotivo').innerHTML = "carregando ...";
                        $.ajax({
                            type: 'POST',
                            url: 'ajaxStatus.php',
                            data: $("#formMotivo").serialize(),
                            success: function (data) {

                                //resultSalvarMotivo
                                document.getElementById('resultSalvarMotivo').innerHTML = data;
                            }
                        });
                    }



                </script>
                <?php
                //$sql = "Select idResumo,porcentagem from resumotrabalho where idTrabalho = '{$idTrabalho}' and idTopicoLda = '{$idOutputLda}'";

                if ($filtroPesquisa === 'todos') {
                    $sql = "
                    Select idResumo,porcentagem,status,idTrabalho,idTopicoLda from resumo r, resumotrabalho rt 
                    WHERE (r.id = rt.idResumo) AND
                    rt.idTrabalho = '{$idTrabalho}' AND rt.idTopicoLda = '{$idOutputLda}';";
                }
                else{
                    $sql = "
                    Select idResumo,porcentagem,status,idTrabalho,idTopicoLda from resumo r, resumotrabalho rt 
                    WHERE (r.id = rt.idResumo) AND r.status = '{$filtroPesquisa}' AND
                    rt.idTrabalho = '{$idTrabalho}' AND rt.idTopicoLda = '{$idOutputLda}';";
                }
                //echo $sql; die;

                $result = $link->query($sql);
                $quantos = mysqli_num_rows($result);
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Resumos nesse Tópico:</strong>
                                <?php echo $quantos ?>
                                <!-- <br><small class="text-info"> Apenas artigos mantidos</small> -->
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a id ="todos" class="nav-link" aria-current="page" href="?idOutput=<?php echo $idOutput ?>&filtroPesquisa=todos">Todos</a>
                                    </li>
                                    <li class="nav-item">
                                        <a id ="incluido" class="nav-link" href="?idOutput=<?php echo $idOutput ?>&filtroPesquisa=incluido">Mantidos</a>
                                    </li>
                                    <li class="nav-item">
                                        <a id ="excluido" class="nav-link" href="?idOutput=<?php echo $idOutput ?>&filtroPesquisa=excluido">Excluídos</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div id="result">
                                    <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Titulo</th>
                                                <th>Ano</th>
                                                <th>% Dominancia</th>
                                                <th>Status</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php


                                            $rows = resultToArray($result);

                                            foreach ($rows as $artigo) {
                                                // $nomeArtigo = mb_substr($artigo['tituloArtigo'], 0, 30);
                                            
                                                $idArtigo = $artigo['idResumo'];

                                                $nomeArtigo = dadosResumo("tituloArtigo", $idArtigo);
                                                $anoPublicacao = dadosResumo("anoPublicacao", $idArtigo);
                                                $status = dadosResumo("status", $idArtigo);
                                                if (isset($_GET['excluirTodos'])) {
                                                    $status = "excluido";
                                                    $sql = "update resumo set status = 'excluido' where id = '{$idArtigo}'";
                                                    $link->query($sql);
                                                }

                                                if ($status == "incluido") {
                                                    $status = "<button style='width: 100%;' id = 'status$idArtigo' onclick = 'excluir($idArtigo)' type='button' class='btn btn-success btn-sm'><i class='fa fa-dot-circle-o'></i> Mantido</button>";
                                                } else {
                                                    $status = "<button style='width: 100%;' id = 'status$idArtigo' onclick = 'incluir($idArtigo)' type='button' class='btn btn-danger btn-sm'><i class='fa fa-dot-circle-o'></i> Excluido</button>";
                                                }
                                                ?>
                                                <tr id="<?php echo $idArtigo ?>">
                                                    <td>
                                                        <?php echo $nomeArtigo ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $anoPublicacao ?>
                                                    </td>
                                                    <td>
                                                        <?php echo ($artigo['porcentagem'] * 100) ?>%
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <?php echo $status ?><br>
                                                            <button style="width: 100%;"
                                                                onclick='motivo(<?php echo $idArtigo ?>)'
                                                                data-toggle='modal' data-target='#largeModal' type='button'
                                                                class='btn btn-info btn-sm'><i
                                                                    class='fa fa-dot-circle-o'></i> Motivo</button>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <a href="verArtigo.php?idArtigo=<?php echo $idArtigo ?>"><button
                                                                    style="width: 100%;" type="button"
                                                                    class="btn btn-primary btn-sm"><i
                                                                        class="fa fa-dot-circle-o"></i> Ver</button></a>
                                                            <button style="width: 100%;"
                                                                onclick="deletar(<?php echo $idArtigo ?>)" type="button"
                                                                class="btn btn-danger btn-sm"><i
                                                                    class="fa fa-dot-circle-o"></i> Deletar</button>
                                                        </center>
                                                    </td>

                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->
    <?php include("includes/js.php"); 
    echo "
    <script>
        var element = document.getElementById('{$filtroPesquisa}');
        element.classList.add('active');
    </script>
    ";
    ?>
    <div class="modal fade" id="largeModal" tabindex="-1" role="dialog" aria-labelledby="largeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="largeModalLabel">Motivo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="resultMotivo">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarMotivo()">Salvar</button>
                </div>
            </div>
        </div>
    </div>


</body>

</html>