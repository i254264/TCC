<?php 


require_once("config.php"); 

$erro="";


$nome = "";
$email = "";
$nomeMae="";
if(isset($_POST['email'])){
// print_r($_POST);die;	

$email = sanitize($_POST['email']);
$nomeMae = sanitize($_POST['nomeMae']);

$sql = "SELECT id,respostaPergunta FROM `usuario` where email= '{$email}'";
$result = $link->query($sql);
$row = $result->fetch_assoc();

if(empty($row['id'])){
	$erro = "Dados nao encontrados";
}else{
	if( (!empty($row['respostaPergunta'])) and ($row['respostaPergunta'] == $nomeMae) ){
		
		if($_POST['senha'] == $_POST['senha2']){
		$senha1 = hashPassword($_POST['senha']);
		$senha = hashPassword($senha1,true);
	
		$sql = "update usuario set senha = '{$senha}' where id = '{$row['id']}'";
		$result = $link->query($sql);
		$_SESSION['email']= $email;
		$_SESSION['key']= $senha;
		setcookie('email', $email, time() + (86400 * 14) );
		setcookie('key', $senha1, time() + (86400 * 14) );//14 dias de vida
		header("location:main.php");
		die;
		}else{
			$erro = "senha nao confere";
		}
		
		
	}else{
		$erro = "Dados nao encontrados";
	}
	
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
					<h3> Recuperar Senha </h3>
					<?php echo $erro ?>
					
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Email">
                        </div>
						<div class="form-group">
                            <label>Nome do meio de sua mãe</label>
                            <input type="text" name="nomeMae" class="form-control" placeholder="Nome do meio">
                        </div>
                            <div class="form-group">
                                <label>Nova Senha</label>
                                <input type="password"  name="senha"  class="form-control" placeholder="Senha">
                        </div>
						<div class="form-group">
                                <label>Repetir Senha</label>
                                <input type="password"  name="senha2"  class="form-control" placeholder="Senha">
                        </div>
						

								
                                <button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30">Recuperar</button>

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