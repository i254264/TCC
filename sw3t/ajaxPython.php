<?php
	
	require_once("config.php");
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	set_time_limit (60); 
	//usuario ja deveria estar logado, logo iremos checar se ele esta logado mesmo
	checar($_SESSION['email'],$_SESSION['key']);
	$idUsuario = idUsuario($_SESSION['email']);

	if(!isset($_POST['idProjeto'])){
	erro("ERRO FATAL");
	}
	$idProjeto = numbers($_POST['idProjeto']);
	donoProjeto($idUsuario,$idProjeto);
	#################################

	//caso o botao clicado no arquivo "analisar.php" seja GERAR RESUMO ENXUTO
	if(isset($_POST['enxuto'])){
		include("includes/resumoEnxuto.php");
	}

	//caso o botao clicado no arquivo "analisar.php" seja LEMATIZAR
	if(isset($_POST['lematizar'])){
		$pythonBin = "python";
		$pythonCandidates = [
			"C:/Python312/python.exe",
			"C:/Python311/python.exe",
			"C:/Python310/python.exe",
			"C:/Python39/python.exe",
			"C:/Users/isabe/AppData/Local/Programs/Python/Python312/python.exe",
			"C:/Users/isabe/AppData/Local/Microsoft/WindowsApps/python.exe",
			"C:/Users/isabe/AppData/Local/Programs/Python/Python311/python.exe",
			"C:/Program Files/WindowsApps/PythonSoftwareFoundation.Python.3.12_3.12.2800.0_x64__qbz5n2kfra8p0/python3.12.exe"
		];
		foreach ($pythonCandidates as $candidate) {
			if (file_exists(str_replace('/', DIRECTORY_SEPARATOR, $candidate))) {
				$pythonBin = $candidate;
				break;
			}
		}
		$comando = "\"{$pythonBin}\" {$sw3tLocation}python/lema.py $idProjeto";
		exec($comando, $output, $returnVar);

		if ($returnVar !== 0) {
			$out = 'document.getElementById("lematizar").innerHTML="Erro ao lematizar"; ';
			$out .= 'document.getElementById("lematizar").className = "btn btn-danger btn-lg btn-block";';
			echo $out; die;
		}

		$out = 'document.getElementById("lematizar").innerHTML="Lematizado Com Sucesso"; ';
		$out .= 'document.getElementById("lematizar").className = "btn btn-success btn-lg btn-block";';
		echo $out; die;
	}

	////caso o botao clicado no arquivo "analisar.php" seja ENVIAR PARA PYTHON
	if(isset($_POST['python'])){
		// print_r($_POST);
		$qtdPalavras = preg_replace('/\D/',"",$_POST['qtdPalavras']);
		$qtdTopico = preg_replace('/\D/',"",$_POST['qtdTopico']);
		$rounds = preg_replace('/\D/',"",$_POST['rounds']);
		$areaConhecimento = sanitize($_POST['areaConhecimento']);
		
		if($qtdPalavras > 20 or $qtdPalavras < 1 ){$qtdPalavras = 1;	}
		if($qtdTopico > 20 or $qtdTopico < 1 ){$qtdTopico = 1;	}
		if($rounds > 501 or $rounds < 1 ){$rounds = 1;	}

		// die;	
		$sql = "SELECT count(id) FROM `resumo` WHERE idProjeto = {$idProjeto}";
		$result = $link->query($sql);
		$row = $result->fetch_assoc();
		
		$countArtigos = $row['count(id)'];

		if($countArtigos == "0" ){
			$out = 'document.getElementById("python").innerHTML="Não há artigos cadastrados"; ';
			$out .= 'document.getElementById("python").className = "btn btn-danger btn-lg btn-block";';
			echo $out;die;
		}
		
		
		$sql = "Select id from resumo where idProjeto = '{$idProjeto}' and resumoLematizado='' and status='incluido' and length(COALESCE(resumoEnxuto, resumo, tituloArtigo)) > 10";
		$result = $link->query($sql);
		$row = $result->fetch_assoc();
		if(empty($row['id'])){
			//ja rodou o resumo enxuto
			$inicioAno = numbers($_POST['inicioAno']);
			$fimAno = numbers($_POST['fimAno']);
			if($inicioAno > $fimAno){
				$tmp = $inicioAno;
				$inicioAno = $fimAno;
				$fimAno = $tmp;
			}
		
			$sql = "Select count(id) from resumo where status='incluido' and idProjeto = '{$idProjeto}'";

			if ($areaConhecimento == "todas") {
				$sql .=" and anoPublicacao >= '{$inicioAno}' and anoPublicacao <= '{$fimAno}'";
			} else {
				$sql .=" and anoPublicacao >= '{$inicioAno}' and anoPublicacao <= '{$fimAno}' and area_conhecimento = '{$areaConhecimento}'";
			}
			
			$result = $link->query($sql);
			$row = $result->fetch_assoc();
			$qtdArtigos = $row['count(id)'];
			
			if($_POST['grafico']=="true"){$grafico = 1;}else{$grafico = 0;}
			if($_POST['probabilidade']=="true"){$probabilidade = 1;}else{$probabilidade = 0;}

			$sql = "INSERT INTO `trabalho` (`id`, `idProjeto`, `idUsuario`, `status`, `dateTime`, `qtdTopicos`, `qtdPalavras`, `qtdArtigos`, `qtdRounds`, `inicioAno`,`fimAno`,`probabilistico`, `area_conhecimento`) VALUES (NULL, '$idProjeto', '$idUsuario', 'aguardando', CURRENT_TIME(), '$qtdTopico','$qtdPalavras','$qtdArtigos','$rounds','{$inicioAno}','{$fimAno}','$probabilidade', '{$areaConhecimento}')";
			
			$result = $link->query($sql);
			$idTrabalho = mysqli_insert_id($link);
			if(empty($idTrabalho)){
				$out = 'document.getElementById("python").innerHTML="Erro ao rodar o python (DB)"; ';
				$out .= 'document.getElementById("python").className = "btn btn-danger btn-lg btn-block";';
				$out .= 'document.getElementById("python").disabled = false;';
			}else{

				$pythonBin = "python";
				$pythonCandidates = [
					"C:/Python312/python.exe",
					"C:/Python311/python.exe",
					"C:/Python310/python.exe",
					"C:/Python39/python.exe",
					"C:/Users/isabe/AppData/Local/Programs/Python/Python312/python.exe",
					"C:/Users/isabe/AppData/Local/Microsoft/WindowsApps/python.exe",
					"C:/Users/isabe/AppData/Local/Programs/Python/Python311/python.exe",
					"C:/Program Files/WindowsApps/PythonSoftwareFoundation.Python.3.12_3.12.2800.0_x64__qbz5n2kfra8p0/python3.12.exe"
				];
				foreach ($pythonCandidates as $candidate) {
					if (file_exists(str_replace('/', DIRECTORY_SEPARATOR, $candidate))) {
						$pythonBin = $candidate;
						break;
					}
				}
				$shell = "\"{$pythonBin}\" {$sw3tLocation}python/MeuLDA.py $idProjeto $idTrabalho $qtdTopico $qtdPalavras $rounds \"{$inicioAno}\" \"{$fimAno}\" \"$grafico\" \"$probabilidade\" \"$areaConhecimento\"";

				//echo $shell;die;
				exec($shell, $output, $returnVar);
				if ($returnVar !== 0) {
					$out = 'document.getElementById("python").innerHTML="Erro ao gerar tópicos (Python)"; ';
					$out .= 'document.getElementById("python").className = "btn btn-danger btn-lg btn-block";';
					$out .= 'document.getElementById("python").disabled = false;';
					echo $out; die;
				}
				
				$query = "SELECT id FROM `output` where idTrabalho= '{$idTrabalho}' limit 1";
				$result = $link->query($query);
				$row = $result->fetch_assoc();
				if(empty($row['id'])){
					$out = 'document.getElementById("python").innerHTML="Erro ao rodar o python (Py)"; ';
					$out .= 'document.getElementById("python").className = "btn btn-danger btn-lg btn-block";';
					$out .= 'document.getElementById("python").disabled = false;';
				}
				else{
					$out = 'document.getElementById("result").innerHTML="<a class=\'\' href=\'analise.php?idTrabalho='.$idTrabalho.'\'>Ver analise</a>";';
					$out .= 'document.getElementById("python").innerHTML="Analisar Novamente";';
					$out .= 'document.getElementById("python").disabled=false;';
					$out .= 'document.getElementById("python").className = "btn btn-success btn-lg btn-block";';
				}
$out  .="console.log('$shell');";
			}
		}
		else{
			$out = 'document.getElementById("python").innerHTML="Favor Rodar o resumo lematizado antes"; ';
			$out .= 'document.getElementById("python").className = "btn btn-danger btn-lg btn-block";';
		}
		echo $out;die;
	}
?>
