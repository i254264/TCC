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


if (isset($_POST['banco'])) {
    $banco = sanitize($_POST['banco']);
    if (strlen($banco) > 2) {
        $sql = "INSERT INTO `fontes` (`id`, `descricao`, `site`, `ultimaAlteracao`) VALUES (NULL, '{$banco}', '', '{$idUsuario}')";
        $result = $link->query($sql);
    }
}

if (isset($_POST['deletar'])) {
    $idBanco = sanitize($_POST['deletar']);
    $sql = "DELETE FROM `fontes` WHERE `id` = '{$idBanco}'";
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
        <?php echo $title ?> - Bancos
    </title>
</head>

<style>
    #lista li {
        background-color: #4141412c;
        border-radius: 30px;
        text-align: center;
        border: 0px solid !important;
    }
    #lista li:nth-child(1), #lista li:nth-child(2), #lista li:nth-child(3), #lista li:nth-child(4), #lista li:nth-child(5){
        background-color: #6180e42c;
        font-weight: bold;
        color: #000075;
    }
</style>

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
                        <h1>Gerenciar DBs</h1>
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
                            <li class="active">Fontes DBs</li>
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
                                <strong class="card-title">Fontes DBs</strong>
                            </div>
                            <div class="card-body row d-flex ">
                                <div class="col d-flex justify-content-center">
                                    <?php
                                    $sql = "select id,descricao from fontes where id <= 5 order by descricao asc";
                                    $result = $link->query($sql);
                                    $rows = resultToArray($result);
                                    echo "<ul id='lista' class='list-group'>";
                                    foreach ($rows as $fonte) {
                                        
                                        echo "<li class='list-group-item'>" . $fonte['descricao'] . "</li>";
                                    }
                                    $sql = "select id,descricao from fontes where id >5 order by descricao asc";
                                    $result = $link->query($sql);
                                    $rows = resultToArray($result);
                                    foreach ($rows as $fonte) {

                                        echo "<li class='list-group-item'>" . $fonte['descricao'] . "</li>";
                                    }
                                    ?>
                                    </ul>
                                </div>
                                <div class="col">

                                    <form method="post">
                                        <div class="row form-group">
                                            <div class="col">
                                                <input type="text" placeholder="fonte DB" name="banco" class="form-control">
                                                <small class="form-text text-muted">Nome do banco de dados</small>
                                            </div>
                                            <div class="col"><button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-dot-circle-o"></i> Adicionar fonte DB</button>
                                            </div>
                                        </div>
                                    </form>

                                    <form method="post">
                                        <div class="row form-group w-75">
                                            <div class="col">
                                                <select class="custom-select" name="deletar">
                                                    <option value="" style="color: gray;" disabled selected>Escolha a fonte DB</option>
                                                    <?php
                                                    $sql = "select id,descricao from fontes where `id` > 5";
                                                    $result = $link->query($sql);
                                                    $rows = resultToArray($result);
                                                    foreach ($rows as $fonte) {
                                                        ?>
                                                        <option value="<?php echo $fonte['id']?>"><?php echo $fonte['descricao']?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col col-md-3"><button type="submit" value="Excluir"
                                                    name="Excluir" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-dot-circle-o"></i> Remover fonte DB
                                                </button></div>
                                        </div>
                                    </form>
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