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
        <?php echo $title ?> - De/Para
    </title>
</head>

<body>
    <!-- Left Panel -->
    <?php $onde = "gerirDePara";
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
                        <h1>Gerenciar de -> para</h1>
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
                            <li class="active">GerirDePara</li>
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
                                <strong class="card-title">Tabela de/para <a
                                        href="copiarEntreProjetos.php">Copiar</a></strong>
                            </div>
                            <div class="card-body bg-light">
                                <div class="row d-flex justify-content-around">
                                    <div class="col-auto p-0">
                                        <form class="p-0" id="formDePara">
                                            <input type="hidden" value="<?php echo $idProjeto ?>" name="idProjeto">
                                            <input type="hidden" value="<?php echo $idProjeto ?>" name="dePara">
                                            <table id="tabelaDePara" class="table">
                                                <tr>
                                                    <th class="tg-fymr">De<br></th>
                                                    <th class="tg-fymr">Para<br></th>
                                                </tr>
                                                <?php

                                                $sql = "SELECT id,input,output FROM `depara` where idProjeto = '{$idProjeto}'";

                                                $result = $link->query($sql);
                                                $rows = resultToArray($result);
                                                //print_r($rows);
                                                if (empty($rows[0]['id'])) {
                                                    ?>
                                                    <tr>
                                                        <td class="tg-0pky"><input type="text" name="de[]"></td>
                                                        <td class="tg-0pky"><input type="text" name="para[]"></td>
                                                    </tr>

                                                    <?php
                                                } else {
                                                    foreach ($rows as $dePara) {

                                                        $de = $dePara['input'];
                                                        $para = $dePara['output'];

                                                        ?>
                                                        <tr>
                                                            <td class="tg-0pky"><input type="text" value="<?php echo $de ?>"
                                                                    name="de[]"></td>
                                                            <td class="tg-0pky"><input type="text" value="<?php echo $para ?>"
                                                                    name="para[]"></td>
                                                        </tr>

                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </table>
                                        </form>
                                    </div>
                                    <div style="height: fit-content" class="col-auto addEnv">
                                        <button style="width: 100px" class="btn btnAddEnv"
                                            onclick="adicionarLinha();">Adicionar</button>
                                        <button style="width: 100px" class="btn btnAddEnv" id="enviar"
                                            onclick="enviarForm();">Enviar</button>
                                    </div>
                                    <script>
                                        function adicionarLinha(argument) {
                                            var myTable = document.getElementById("tabelaDePara");
                                            var myForm = document.getElementById("formDePara");
                                            // var currentIndex = myTable.rows.length;
                                            var currentRow = myTable.insertRow(1);
                                            
                                            if ((myTable.rows.length)>=7) {
                                                myForm.style.height = "375px";
                                                myForm.style.overflowY = "scroll";
                                            }

                                            var de = document.createElement("input");
                                            de.setAttribute("name", "de[]");
                                            de.setAttribute("type", "text");

                                            var para = document.createElement("input");
                                            para.setAttribute("name", "para[]");
                                            para.setAttribute("type", "text");

                                            var currentCell = currentRow.insertCell(-1);
                                            currentCell.appendChild(de);

                                            currentCell = currentRow.insertCell(-1);
                                            currentCell.appendChild(para);


                                        }

                                        function enviarForm() {
                                            document.getElementById('enviar').innerHTML = "Carregando ...";
                                            $.ajax({
                                                type: 'POST',
                                                url: 'ajax.php',
                                                data: $("#formDePara").serialize(),
                                                success: function (data) {
                                                    document.getElementById('enviar').innerHTML = "Salvo com sucesso";
                                                }
                                            });

                                        }
                                    </script>

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