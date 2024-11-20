<?php  

session_start();

require("./../dbm/DatabaseConnector.php");
require("./../controllers/BDController.php");

$horaAtual = time(); // time() retorna a hora atual em segundos desde a Era Unix (1 de Janeiro de 1970 00:00:00 GMT)
if (!isset($_SESSION['user_email'])==True) {
  $tempoDecorrido = 0;
  unset($_SESSION['user_email']);
  session_write_close();

}else{
  if (isset($_SESSION)==True) {
    $tempoDecorrido =  $horaAtual-$_SESSION['ultima_atividade'];
    if ($tempoDecorrido > 600) { 
      $tempoDecorrido = 0;
      header('location:./logout.php');

      }    
  }
}

if($_SESSION['user_superuser']==2){

    include_once("./../dbm/DBConfig.php");

    $dsn = DBConfig::getDSN();
    $username = DBConfig::getUsername();
    $password = DBConfig::getPassword();
    $options = DBConfig::getOptions();
    $servername = DBConfig::getServer();
    $dbname = DBConfig::getBdName();

    $dbConnector = new DatabaseConnector($dsn, $username, $password, $options);
    $construtor = new BDController($dbConnector, "empresas", "id", $id);

    $empresas = $construtor->getter_all_empresas($_SESSION['user_superuser']);
}
        


?>

<!DOCTYPE html>
<html lang="pt_br">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard - SB Admin</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="dashboard.php">Humor AI</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <!-- <input required class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button> -->
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
<!--                         <li><hr class="dropdown-divider" /></li>
 -->                        <li><a class="dropdown-item" href="#!">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Principal</div>
                            <a class="nav-link" href="dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                            </a>
                            <a class="nav-link" href="detail_cloudwords.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-cloud"></i></div>
                                Nuvem de palavras
                            </a>
                            <a class="nav-link" href="form_contato.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-envelope"></i></div>
                                Contato
                            </a>
                            <div class="sb-sidenav-menu-heading">Admnistrador</div>
                            <a class="nav-link" href="detail_user.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-gamepad"></i></div>
                                Administrador
                            </a>
                            <div class="sb-sidenav-menu-heading"></div>
                            <a class="nav-link" href="painel_controle.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                Painel de Controle
                            </a>

                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Bem-vindo</div>
                        <?php echo $_SESSION['user_nome'] . ' ' . $_SESSION['user_sobrenome']; ?>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Painel de Controle</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Cadastro</li>
                        </ol>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-building me-1"></i>
                                        Cadastrar de empresas
                                    </div>                                    
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <form action="cadastra_empresa.php" method="post">
                                                    <div class="container">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label class='form-label' for="razao">Razão Social:</label>
                                                                <input required class="form-control" type="text" name="razao"><br>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class='form-label' for="cnpj">CNPJ:</label>
                                                                <input required class="form-control" type="text" name="cnpj"><br>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class='form-label' for="telefone">Telefone:</label>
                                                                <input required class="form-control" type="text" name="telefone"><br>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class='form-label' for="email">E-mail:</label>
                                                                <input required class="form-control" type="email" name="email"><br>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class='form-label' for="endereco">Endereço:</label>
                                                                <input required class="form-control" type="text" name="endereco"><br>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class='form-label' for="cidade">Cidade:</label>
                                                                <input required class="form-control" type="text" name="cidade"><br>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class='form-label' for="estado">Estado:</label>
                                                                <input required class="form-control" type="text" name="estado"><br>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class='form-label' for="pais">País:</label>
                                                                <input required class="form-control" type="text" name="pais"><br>
                                                            </div>  
                                                            <div class="col-md-6 mb-3">
                                                                <label class='form-label' for="cep">CEP:</label>
                                                                <input required class="form-control" type="text" name="cep"><br>
                                                            </div>  
                                                            <div class="col-md-6 mb-3">                  
                                                                <button class='btn btn-primary mt-2' type="submit">Enviar</button>
                                                                <a href="./painel_controle.php"><button type="button" class="btn btn-secondary mt-2">Retornar</button></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Belloni & França 2024</div>
                            <div>
                                <!-- <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a> -->
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
