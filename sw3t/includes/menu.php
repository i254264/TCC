<?php
	function objetoAtivo($objeto,$onde,$class=true){
		if($objeto == $onde ){
			if($class ==true){
			echo ' class="active" ';
			}else{
				echo ' active ';
			}
		}
	}
	function echoTrue($array,$onde,$show = false){
		$noLugar = "false";
		foreach($array as $value){
			if($value == $onde){$noLugar = "true";}
			
		}
		if(!$show){
		echo $noLugar;
		}else{
			if($noLugar == "true"){
				echo "show";
			}
		}
	}
	?>
	
	<aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="./"><img src="images/sw3t_new.png" alt="Logo"></a>
                <a class="navbar-brand hidden" href="./"><img src="images/logo2.png" alt="Logo"></a>
            </div>

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                   <!-- <li <?php objetoAtivo("main",$onde) ?>>
                        <a href="index.php"> <i class="menu-icon fa fa-home"></i>Home </a>
                    </li>-->
					<li <?php objetoAtivo("meusProjetos",$onde) ?>>
                        <a href="meusProjetos.php"> <i class="menu-icon fa fa-list-ul"></i>Meus Projetos </a>
                    </li>
					<li <?php objetoAtivo("perfil",$onde) ?>>
                        <a href="perfil.php"> <i class="menu-icon fa fa-user"></i>Perfil </a>
                    </li>
					<?php if(isset($_SESSION['idProjeto'])) { ?>
                    <h3 class="menu-title"><?php echo $_SESSION['nomeProjeto'] ?></h3><!-- /.menu-title -->
					
					
					<li class="menu-item-has-children dropdown <?php objetoAtivo("adicionarArtigos",$onde,false) ?> <?php objetoAtivo("verificarDuplicados",$onde,false) ?> <?php objetoAtivo("gerirArtigos",$onde,false) ?> <?php objetoAtivo("pesquisarArtigos",$onde,false) ?> <?php echoTrue(array("adicionarArtigos","verificarDuplicados","gerirArtigos","pesquisarArtigos"),$onde,true) ?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="<?php echoTrue(array("adicionarArtigos","verificarDuplicados","gerirArtigos","pesquisarArtigos"),$onde) ?>"> <i class="menu-icon fa fa-cogs"></i>Gerenciar Projeto</a>
                        <ul class="sub-menu children dropdown-menu <?php echoTrue(array("adicionarArtigos","verificarDuplicados","gerirArtigos","pesquisarArtigos"),$onde,true) ?>">
                         
                            <li <?php objetoAtivo("adicionarArtigos",$onde) ?> ><i class="fa fa-plus"></i><a href="adicionarArtigos.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Adicionar Resumos </a></li>
                            <li <?php objetoAtivo("verificarDuplicados",$onde) ?> ><i class="fa fa-clone"></i><a href="verificarDuplicados.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Verificar Duplicados</a></li>
                            <li <?php objetoAtivo("gerirArtigos",$onde) ?> ><i class="fa fa-clipboard"></i><a href="gerirArtigos.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Todos os Resumos</a></li>
                            <li <?php objetoAtivo("pesquisarArtigos",$onde) ?> ><i class="fa fa-search"></i><a href="pesquisarArtigos.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Pesquisar Resumos</a></li>
                            </ul>
                    </li>
					<li class="menu-item-has-children dropdown <?php objetoAtivo("gerirDePara",$onde,false) ?> <?php objetoAtivo("gerirStopWords",$onde,false) ?> <?php echoTrue(array("gerirDePara","gerirStopWords"),$onde,true) ?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="<?php echoTrue(array("gerirDePara","gerirStopWords"),$onde) ?>"> <i class="menu-icon fas fa-list-alt"></i>Gerenciar Tabelas</a>
                        <ul class="sub-menu children dropdown-menu <?php echoTrue(array("gerirDePara","gerirStopWords"),$onde,true) ?>">
                         
                            <li <?php objetoAtivo("gerirAartigos",$onde) ?> ><i class="fa fa-clipboard"></i><a href="bancosUsuario.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Fontes DBs</a></li>
                            <li <?php objetoAtivo("gerirDePara",$onde) ?> ><i class="fa fa-arrow-right"></i><a href="gerirDePara.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Substituir termos (de/para)</a></li>
                            <li <?php objetoAtivo("gerirStopWords",$onde) ?> ><i class="fa fa-pause"></i><a href="gerirStopWords.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Excluir termos (StopWords)</a></li>
							
                            <li <?php objetoAtivo("copiarTabelas",$onde) ?> ><i class="fa fa-pause"></i><a href="copiarEntreProjetos.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Copiar tabelas</a></li>
                            
                            </ul>
                    </li>
					
					<!--<li class="menu-item-has-children dropdown <?php objetoAtivo("gerirArtigos",$onde,false) ?> <?php objetoAtivo("pesquisarArtigos",$onde,false) ?> <?php objetoAtivo("verificarDuplicados",$onde,false) ?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-cogs"></i>Gerenciar Artigos</a>
                        <ul class="sub-menu children dropdown-menu">
                         
                            <li <?php objetoAtivo("gerirArtigos",$onde) ?> ><i class="fa fa-clipboard"></i><a href="gerirArtigos.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Todos os Artigos</a></li>
                            <li <?php objetoAtivo("pesquisarArtigos",$onde) ?> ><i class="fa fa-search"></i><a href="pesquisarArtigos.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Pesquisar Artigos</a></li>
							
                       
							
                            <li <?php objetoAtivo("verificarDuplicados",$onde) ?> ><i class="fa fa-clone"></i><a href="verificarDuplicados.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Verificar Duplicados</a></li>
							<li <?php objetoAtivo("adicionarArtigos",$onde) ?>><a href="adicionarArtigos.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>"> <i class="fa fa-plus"></i>Adicionar Artigos </a></li>
							<li <?php objetoAtivo("limparArtigos",$onde) ?> ><a href="limparArtigos.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>"> <i class="fa fa-eraser"></i>Limpar Artigos </a></li>
                        </ul>
                    </li>
					-->
					
					
					
					
					<li <?php objetoAtivo("analisar",$onde) ?> ><a href="analisar.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>"> <i class="menu-icon fa fa-eye"></i>Fazer Análise </a></li>
					
					
					<li class="menu-item-has-children dropdown <?php objetoAtivo("analises",$onde,false) ?> <?php objetoAtivo("resultadoProjeto",$onde,false) ?> <?php echoTrue(array("analises","resultadoProjeto"),$onde,true) ?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="<?php echoTrue(array("analises","resultadoProjeto"),$onde) ?>"> <i class="menu-icon fas fa-th-large"></i>Gerenciar Perfil do Projeto</a>
                        <ul class="sub-menu children dropdown-menu <?php echoTrue(array("analises","resultadoProjeto"),$onde,true) ?>">
                         
                            
                            <li <?php objetoAtivo("analises",$onde) ?> ><i class="fa fa-check"></i><a href="analises.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Análises Concluidas</a></li>
                            <li <?php objetoAtivo("resultadoProjeto",$onde) ?> ><i class="fa fa-chart-bar"></i><a href="resultadoProjeto.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>">Resultados Atuais</a></li>
                            
                            </ul>
                    </li>
					
					<?php }else{ ?>
					   <h3 class="menu-title">Projetos :</h3><!-- /.menu-title -->
					<?php
					$sql = "Select id,nome from projeto where idUsuario = '{$idUsuario}' and ativo = 1 and nome !='' order by id desc limit 5";
				$result = $link->query($sql);
				$rows =resultToArray($result);
				
				foreach ($rows as $projeto){
					$idProjeto = $projeto['id'];
					$nome = $projeto['nome'];
					?>
					
					<li ><a href="gerirProjeto.php?idProjeto=<?php echo $idProjeto?>"> <i class="menu-icon fa fa-clipboard"></i><?php echo $nome ?> </a></li>
					
				<?php }
				
				} ?>
                   <!-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-laptop"></i>Components</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-puzzle-piece"></i><a href="ui-buttons.html">Buttons</a></li>
                            <li><i class="fa fa-id-badge"></i><a href="ui-badges.html">Badges</a></li>
                            <li><i class="fa fa-bars"></i><a href="ui-tabs.html">Tabs</a></li>
                            <li><i class="fa fa-share-square-o"></i><a href="ui-social-buttons.html">Social Buttons</a></li>
                            <li><i class="fa fa-id-card-o"></i><a href="ui-cards.html">Cards</a></li>
                            <li><i class="fa fa-exclamation-triangle"></i><a href="ui-alerts.html">Alerts</a></li>
                            <li><i class="fa fa-spinner"></i><a href="ui-progressbar.html">Progress Bars</a></li>
                            <li><i class="fa fa-fire"></i><a href="ui-modals.html">Modals</a></li>
                            <li><i class="fa fa-book"></i><a href="ui-switches.html">Switches</a></li>
                            <li><i class="fa fa-th"></i><a href="ui-grids.html">Grids</a></li>
                            <li><i class="fa fa-file-word-o"></i><a href="ui-typgraphy.html">Typography</a></li>
                        </ul>
                    </li>-->


                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside><!-- /#left-panel -->
