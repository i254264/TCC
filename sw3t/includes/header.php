<header id="header" class="header">

<div class="header-menu">

    <div class="col-sm-9">
        <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-list"></i></a>
        <div class="header-left">


    <?php if(!empty($_SESSION['nomeProjetoInteiro'])){?>
        <div class="user-area ">
        <a class="nav-link" style="font-size: 18px;color: #000075;" href="gerirProjeto.php?idProjeto=<?php echo $_SESSION['idProjeto'] ?>"><?php echo "<strong>PROJETO: </strong>" . $_SESSION['nomeProjetoInteiro'] ?></a>
        </div>
        <?php } ?>

            
        </div>
    </div>

    <div class="col-sm-3">
       <div class="user-area dropdown float-right">
       <a class="nav-link" href="logout.php"><i class="fa fa-power-off" style="color: #000075;"></i> Sair</a>
          <!--  <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="user-avatar rounded-circle" src="images/admin.jpg" alt="User Avatar">
            </a>-->

         <!--   <div class="user-menu dropdown-menu">
                <a class="nav-link" href="#"><i class="fa fa-user"></i> My Profile</a>

              

                <a class="nav-link" href="#"><i class="fa fa-cog"></i> Settings</a>

                <a class="nav-link" href="logout.php"><i class="fa fa-power-off"></i> Logout</a>
            </div>-->
            
        </div>
        
        <!--	
        <div class="d-flex align-items-center profileWrapper pointer">
            <div class="bgLetter mr-2">
                <span>P</span>
            </div>
            <div class="profileName">
                <span>Placeholder</span>
            </div>
        </div>
        -->
    </div>
</div>

</header>