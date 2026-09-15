<?php
require_once("config.php");
//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
checar($_SESSION['email'], $_SESSION['key']);
$idUsuario = idUsuario($_SESSION['email']);


$idProjeto = numbers($_GET['idProjeto']);
donoProjeto($idUsuario, $idProjeto);
$nomeProjeto = sanitize(dadosProjeto("nome", $idProjeto));
$nomeProjeto = mb_substr($nomeProjeto, 0, 20);
$_SESSION['idProjeto'] = $idProjeto;
$_SESSION['nomeProjeto'] = $nomeProjeto;
if (isset($_GET['deletar'])) {

    $idTrabalho = numbers($_GET['deletar']);

    $sql = "Select idProjeto from trabalho where id = '{$idTrabalho}'";
    $result = $link->query($sql);
    $row = $result->fetch_assoc();

    $idProjeto = $row['idProjeto'];

    // echo $sql;die;
// echo $idProjeto;die;

    donoProjeto($idUsuario, $idProjeto);
    $sql = "DELETE FROM trabalho where id =" . $idTrabalho;
    $result = $link->query($sql);
}
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
        <?php echo $title ?> - Analises
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
                        <h1>Analises do Projeto</h1>
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
                            <li class="active">Analises</li>
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
                                <strong class="card-title">Analises desse projeto :</strong>
                            </div>
                            <div class="card-body">


                                <table class="table table-responsive">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Qtd Artigos</th>
                                            <th scope="col">Coerencia</th>
                                            <th scope="col">Perplexidade</th>
                                            <th scope="col">Acuracia</th>
                                            <th scope="col">Topicos/Palavras</th>
                                            <th scope="col">Rounds</th>
                                            <th scope="col">Anos</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Data</th>
                                            <th scope="col">Área Conhecimento</th>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT id,status,dateTime,coerencia,perplexidade,acuracia,qtdTopicos,qtdPalavras,qtdArtigos,qtdRounds,inicioAno,fimAno,area_conhecimento FROM `trabalho` where idProjeto = $idProjeto order by dateTime desc";
                                        //echo $sql; die;
                                        $result = $link->query($sql);
                                        $rows = resultToArray($result);

                                        $i = 0;
                                        echo "<div style='margin-top: inherit;' class='row'>";
                                        foreach ($rows as $trabalho) {
                                            $idTrabalho = $trabalho['id'];
                                            $status = sanitize($trabalho['status']);
                                            $qtdArtigos = numbers($trabalho['qtdArtigos']);
                                            $rounds = numbers($trabalho['qtdRounds']);
                                            $inicioAno = sanitize($trabalho['inicioAno']);
                                            $fimAno = sanitize($trabalho['fimAno']);
                                            $area_conhecimento = sanitize($trabalho['area_conhecimento']);

                                            $anos = "$inicioAno/$fimAno";




                                            $coerencia = str_replace("%", "", $trabalho['coerencia']);
                                            $perplexidade = str_replace("%", "", $trabalho['perplexidade']);
                                            $acuracia = str_replace("%", "", $trabalho['acuracia']);

                                            $coerencia = number_format($coerencia, 1, '.', '');
                                            $perplexidade = number_format($perplexidade, 1, '.', '');

                                            if ($perplexidade < 0) {
                                                $perplexidade = $perplexidade * -1;
                                            }
                                            $acuracia = number_format($acuracia, 1, '.', '');


                                            $qtdTopicos = $trabalho['qtdTopicos'];
                                            $qtdPalavras = $trabalho['qtdPalavras'];
                                            $dateTime = $trabalho['dateTime'];
                                            $dateTime = strtotime($dateTime);
                                            $dateTime = date("d/m/Y H:i:s", $dateTime);
                                            ?>
                                            <tr>
                                                <th scope="row">
                                                    <?php echo $idTrabalho ?>
                                                </th>

                                                <td>
                                                    <?php echo $qtdArtigos ?>
                                                </td>

                                                <td>
                                                    <?php echo $coerencia ?>%
                                                </td>
                                                <td>
                                                    <?php echo $perplexidade ?>
                                                </td>
                                                <td>
                                                    <?php echo $acuracia ?>%
                                                </td>
                                                <td>
                                                    <?php echo $qtdTopicos ?> /
                                                    <?php echo $qtdPalavras ?>
                                                </td>
                                                <td>
                                                    <?php echo $rounds ?>
                                                </td>
                                                <td>
                                                    <?php echo $anos ?>
                                                </td>
                                                <td>
                                                    <?php echo $status ?>
                                                </td>
                                                <td>
                                                    <?php echo $dateTime ?>
                                                </td>
                                                <td>
                                                    <?php echo $area_conhecimento ?>
                                                </td>
                                                <td><a href="analise.php?idTrabalho=<?php echo $idTrabalho ?>"><button
                                                            type="button" class="btn btn-primary btn-sm"><i
                                                                class="fa fa-dot-circle-o"></i> Ver</button></a></td>
                                                <td><a
                                                        href="analises.php?idProjeto=<?php echo $idProjeto ?>&deletar=<?php echo $idTrabalho ?>"><button
                                                            type="button" class="btn btn-danger btn-sm"><i
                                                                class="fa fa-dot-circle-o"></i> Deletar</button></a></td>
                                            </tr>
                                        <?php

                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->

    <!-- Right Panel -->

    <?php include("includes/js.php"); ?>



</body>

</html>