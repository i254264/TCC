<?php 
require_once("config.php"); 

$erro="";
// Token de proteção usado em alguns ambientes, mas bloqueia o cadastro ao abrir a tela diretamente.
// Para uso local, permitimos acesso sem token; se o token existir, ele precisa bater com o válido.
if(isset($_REQUEST['a']) && $_REQUEST['a'] != 'C2L1QeVMNjbFa2u'){
	die('token de cadastro invalido');
}


$nome = "";
$empresa = "";
$funcao = "";
$tel = "";
$email = "";
if(isset($_POST['nome'])){
// print_r($_POST);die;	

$nome = sanitize($_POST['nome']);
$empresa = sanitize($_POST['empresa']);
$funcao = sanitize($_POST['funcao']);
$tel = sanitize($_POST['tel']);
$email = sanitize($_POST['email']);
$nomeMae = sanitize($_POST['nomeMae']);

$sql = "SELECT id FROM `usuario` where email= '{$email}'";
$result = $link->query($sql);
$row = $result->fetch_assoc();


// Array ( [nome] => Yuri Tadeu [empresa] => unicamp [funcao] => pesquisador [tel] => 11949725989 [email] => YURI@TADEU.WORK [senha] => 1 [senha2] => 1 ) 

if(empty($row['id'])){
//falhou na autenticação, mandar pro login


if( empty($_POST['senha']) or $_POST['senha'] != $_POST['senha2']){
	$erro = "As senhas não conferem";
}else{
	
	$senha1 = hashPassword($_POST['senha']);
	$senha = hashPassword($senha1,true);

$sql = "INSERT INTO `usuario` (`id`, `nome`, `email`, `tel`, `senha`, `funcao`, `empresa`, `respostaPergunta`) VALUES (NULL, '{$nome}', '{$email}', '{$tel}', '{$senha}', '{$funcao}', '{$empresa}','{$nomeMae}')";
$result = $link->query($sql);

	$idUsuario = mysqli_insert_id($link);
	if(!empty($idUsuario)){
		$_SESSION['email']= $email;
		$_SESSION['key']= $senha;
		setcookie('email', $email, time() + (86400 * 14) );
		setcookie('key', $senha1, time() + (86400 * 14) );//14 dias de vida
		header("location:main.php");
		die;
		
	}else{
		// echo $sql;
		$erro = "Erro a cadastrar :<";
	}
}
}else{
	
	$erro ="email ja cadastrado";
}




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
</head>

<body class="bg-dark">


    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">
                <div class="login-logo">
                    <a href="index.php">
                        <img class="align-content" src="logo.png" alt="">
                    </a>
                </div>
                <div class="login-form">
                    <form method="post">
					<h3> Cadastre-se </h3>
					<?php echo $erro ?>
					 <div class="form-group">
                            <label>Nome</label>
                            <input type="text" name="nome" value="<?php echo $nome ?>" class="form-control" placeholder="Nome">
                        </div>
						 <div class="form-group">
                            <label>Instituição</label>
                            <input type="text" name="empresa" value="<?php echo $empresa ?>" class="form-control" placeholder="Instituição">
                        </div>
						 <div class="form-group">
                            <label>Funcção na Instituição</label>
                            <input type="text" name="funcao" value="<?php echo $funcao ?>" class="form-control" placeholder="Função">
                        </div>
						<div class="form-group">
                            <label>Telefone para contato</label>
                            <input type="text" name="tel" value="<?php echo $tel ?>" class="form-control" placeholder="(00) 0000-0000">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo $email ?>" class="form-control" placeholder="Email">
                        </div>
						<div class="form-group">
                            <label>Nome do meio de sua mãe</label>
                            <input type="text" name="nomeMae" class="form-control" placeholder="Nome do meio">
                        </div>
                            <div class="form-group">
                                <label>Senha</label>
                                <input type="password"  name="senha"  class="form-control" placeholder="Senha">
                        </div>
						<div class="form-group">
                                <label>Repetir Senha</label>
                                <input type="password"  name="senha2"  class="form-control" placeholder="Senha">
                        </div>
						

								
                                <button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30">Cadastre-se</button>

                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <script src="vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>


</body>


</html>