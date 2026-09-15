<?php 
require_once("config.php"); 

$erro="";

if(isset($_COOKIE['email']) and isset($_COOKIE['key'])){
//se tiver cookie, verificar se é login valido e logar 
$email = sanitize($_COOKIE['email']);
	$senha = hashPassword($_COOKIE['key'],true);//veio do cookie
	// $senha = hashPassword($senha1,true);
	$query = "SELECT id FROM {$dbUso}.`usuario` where email= '{$email}' and senha = '{$senha}'";
	$result = $link->query($query);
	$row = $result->fetch_assoc();
	if(empty($row['id'])){
		//senha ou login errado
	}else{
		//login certo
		$_SESSION['email']= $email;
		$_SESSION['key']= $senha;
		header("location:main.php");
		die;
	}



}
if(isset($_POST['email'])){
	$email = sanitize($_POST['email']);
	$senha1 = hashPassword($_POST['senha']);
	$senha = hashPassword($senha1,true);
	$query = "SELECT id FROM {$dbUso}.`usuario` where email= '{$email}' and senha = '{$senha}'";
	$result = $link->query($query);
	$row = $result->fetch_assoc();
	if(empty($row['id'])){
		//senha ou login errado
		$erro = "email e/ou senha incorreto(s)<br>";
	}else{
		//login certo
		$_SESSION['email']= $email;
		$_SESSION['key']= $senha;
		setcookie('email', $email, time() + (86400 * 14) );
		setcookie('key', $senha1, time() + (86400 * 14) );//14 dias de vida
		header("location:main.php");
		die;
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
                        <img class="align-content" src="images/sw3t_new.png" style="width: 250px;" alt="">
                    </a>
                </div>
                <div class="login-form">
                    <form method="post">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email">
                        </div>
                            <div class="form-group">
                                <label>Senha</label>
                                <input type="password"  name="senha"  class="form-control" placeholder="Senha">
                        </div>
                                <div class="checkbox">
                         
                                    <label class="pull-right">
                                <a href="esqueceuSenha.php">Esqueci minha senha</a>
                            </label>

                                </div>
								<?php echo $erro ?>
                                <button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30">Entrar</button>
                              <!--  <div class="social-login-content">
                                    <div class="social-button">
                                        <button type="button" class="btn social facebook btn-flat btn-addon mb-3"><i class="ti-facebook"></i>Entrar Com o Facebook</button>
                                        
                                    </div>
                                </div> -->
                                <div class="register-link m-t-15 text-center">
                                    <p>Não tem conta? <a href="cadastro.php">Se cadastre aqui!</a></p>
                                </div>
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