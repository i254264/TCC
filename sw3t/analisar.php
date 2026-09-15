<?php
#####CÓDIGOS DE CONFIGURAÇÃO#####
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
#################################
?>
<!doctype html>
<html class="no-js" lang="pt-br">

<head>
    <?php include("includes/head.php"); ?>
    <?php $nomeProjeto = dirname(__DIR__) . "\\"; ?>
    <title>
        <?php echo $title ?> - Analisar
    </title>
</head>

<body>
    <!-- Left Panel -->
    <?php $onde = "analisar";
    include("includes/menu.php"); ?>
    <!-- Left Panel -->

    <!-- Right Panel -->
    <div id="right-panel" class="right-panel">

        <!-- Header-->
        <?php include("includes/header.php"); ?>

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Analisar Projeto</h1>
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
                            <li class="active">Analisar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- Final Header-->

        <!-- Conteúdo -->
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Analisar Projeto</strong>
                            </div>
                            <div class="card-body">
                                <div class="col-4 analiseButtonWrapper">
                                    <button type="button" id="artigosLimpos" onclick="artigosLimpos();"
                                        class="btn btn-primary btn-md btn-block">Transforma em letra minúscula e limpa pontuação</button>
                                        <!-- <small class="form-text text-muted">Resumo original em letras minúsculas</small> -->
                                </div>
                                <div class="col-4">
                                    <button type="button" id="enxuto" onclick="resumoEnxuto();"
                                        class="btn btn-primary btn-md btn-block">Tratar tabelas De/Para e
                                        StopWords</button>
                                        <!-- <small class="form-text text-muted">Com troca de termos e eliminação de stop words do usuário</small> -->
                                </div>
                                <div class="col-4">
                                    <button type="button" id="lematizar" onclick="lematizar();"
                                        class="btn btn-primary btn-md btn-block">Retira StopWords padrão e lematiza termos</button>
                                        <!-- <small class="form-text text-muted">Sem stopwords do sistema e termos simplificados</small> -->
                                </div>
                                <br><br><br><br><br>
                                <div class="qtdWrapper">
                                    <div class="colWrapper">
                                        <h6 style="font-weight: bold;">Quantidade de Tópicos:</h6>
                                        <input type="number" value="<?php echo $presetQtdTopico; ?>" id="qtdTopicos"
                                            max="10" min="1" />
                                    </div>
                                    <hr class="mt-1 mb-2"/>
                                    <div class="colWrapper">
                                        <h6 style="font-weight: bold;">Quantidade de Palavras por Tópico:</h6>
                                        <input type="number" value="<?php echo $presetQtdPalavras; ?>" id="qtdPalavras"
                                            max="10" min="1" />
                                    </div>
                                    <hr class="mt-1 mb-2"/>
                                    <div class="colWrapper">
                                        <h6 style="font-weight: bold;">Ciclos de aprendizado do modelo:</h6>
                                        <input type="number" value="<?php echo $presetQtdPalavras; ?>" id="rounds"
                                            max="20" min="1" />
                                    </div>
                                </div>
                                <hr class="mt-1 mb-2"/>
                                <span style="font-weight: bold;">Intervalo de Anos:</span>
                                <br>
                                <div class="row w-25 mt-3">
                                    <label for="anos" class="col">Começo:</label>
                                    <select id="inicioAno" name="anos"  class="col form-select">

                                        <?php
                                        $sql = "select distinct(anoPublicacao) from resumo where idProjeto = '{$idProjeto}' order by anoPublicacao asc";
                                        $result = $link->query($sql);
                                        $rows = resultToArray($result);
                                        $primeiro = "selected";
                                        foreach ($rows as $ano) {

                                            ?>
                                            <option <?php echo $primeiro ?>value="<?php echo $ano['anoPublicacao'] ?>"><?php
                                                echo $ano['anoPublicacao'] ?></option>
                                            <?php $primeiro = "";
                                        } ?>
                                    </select>
                                </div>

                                <div class="row w-25">
                                    <label for="anos" class="col">Fim:</label>
                                    <select id="fimAno" name="anos" class="col form-select">
                                        <?php
                                        arsort($rows);

                                        foreach ($rows as $ano) {

                                            ?>
                                            <option value="<?php echo $ano['anoPublicacao'] ?>"><?php echo $ano['anoPublicacao'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                
                                <hr class="mt-3 mb-2"/>
                                <div class="mt-3 row">
                                    <label style="font-weight: bold;" for="areaConhecimento" class="col-auto">Área Conhecimento:</label>
                                    <select id="areaConhecimento" name="areaConhecimento" class="col-auto form-select">

                                        <?php
                                        $sql = "select distinct(area_conhecimento) from resumo where idProjeto = '{$idProjeto}' and status = 'incluido' order by area_conhecimento asc;";
                                        $result = $link->query($sql);
                                        $rows = resultToArray($result);
                                        ?>
                                        <option selected value="todas">Todas as áreas</option>
                                        <?php
                                        foreach ($rows as $area) {

                                            ?>
                                            <option value="<?php echo $area['area_conhecimento'] ?>"><?php echo $area['area_conhecimento'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <hr class="mt-3 mb-2"/>
                                <input type="checkbox" id="grafico"><label for="grafico">Gerar
                                    Gráfico</label><br><small>Recomenda-se apenas para análise final.</small>

                                <input type="hidden" value="false" id="probabilidade">
                                <!--
                        <br>
                        <input type="checkbox" id="probabilidade" ><label for="probabilidade" > Analisar probabilisticamente </label><br><small>Não marcando essa opção, o software irá analisar deterministicamente </small>
                        Marbilia falou para comentar dia 18/02/2020
                        -->
                                <br>
                                <br>

                                <button type="button" id="python" onclick="python();" style="font-weight: bold;"
                                    class="btn btn btn-info btn-md btn-block">GERAR TÓPICOS</button>
                                <br>

                                <div id="result"></div>

                                <!--Funções chamadas para botões 'Gerar Artigos Limpos', 'Gerar Resumos Enxutos', 'Lematizar' e 'Enviar pro python'-->
                                <script>
                                    function artigosLimpos() {
                                        document.getElementById('artigosLimpos').innerHTML = "Carregando ...";
                                        document.getElementById("artigosLimpos").className = "btn btn-secondary btn-lg btn-block";
                                        $.ajax({
                                            type: 'POST',
                                            url: 'ajaxLimpar.php',
                                            data: "idProjeto=<?php echo $idProjeto ?>&limpar=true",
                                            success: function (data) {
                                                eval(data);
                                            }
                                        });
                                    }

                                    function resumoEnxuto() {
                                        document.getElementById('enxuto').innerHTML = "Carregando ...";
                                        document.getElementById("enxuto").className = "btn btn-secondary btn-lg btn-block";
                                        $.ajax({
                                            type: 'POST',
                                            url: 'ajaxPython.php',
                                            data: "idProjeto=<?php echo $idProjeto ?>&enxuto=true",
                                            success: function (data) {
                                                eval(data);
                                            }
                                        });
                                    }

                                    function lematizar() {
                                        document.getElementById('lematizar').innerHTML = "Carregando ...";
                                        document.getElementById("lematizar").className = "btn btn-secondary btn-lg btn-block";
                                        $.ajax({
                                            type: 'POST',
                                            url: 'ajaxPython.php',
                                            data: "idProjeto=<?php echo $idProjeto ?>&lematizar=true",
                                            success: function (data) {
                                                eval(data);
                                            }
                                        });
                                    }

                                    function python() {
                                        document.getElementById("python").disabled = true;
                                        var qtdPalavras = document.getElementById('qtdPalavras').value;
                                        var qtdTopicos = document.getElementById('qtdTopicos').value;
                                        var qtdRounds = document.getElementById('rounds').value;
                                        var inicioAno = document.getElementById('inicioAno').value;
                                        var fimAno = document.getElementById('fimAno').value;
                                        var areaConhecimento = document.getElementById('areaConhecimento').value;
                                        var grafico = document.getElementById('grafico').checked;
                                        var probabilidade = document.getElementById('probabilidade').checked;
                                        document.getElementById('python').innerHTML = "Carregando ...";
                                        document.getElementById("python").className = "btn btn-secondary btn-lg btn-block";
                                        $.ajax({
                                            type: 'POST',
                                            url: 'ajaxPython.php',
                                            data: "idProjeto=<?php echo $idProjeto ?>&python=true&qtdPalavras=" + qtdPalavras + "&qtdTopico=" + qtdTopicos + "&rounds=" + qtdRounds + "&inicioAno=" + inicioAno + "&fimAno=" + fimAno + "&areaConhecimento=" + areaConhecimento + "&grafico=" + grafico + "&probabilidade=" + probabilidade,
                                            success: function (data) {
                                                eval(data);
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

    <?php include("includes/js.php"); ?>



</body>

</html>