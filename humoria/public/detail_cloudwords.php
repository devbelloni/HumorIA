<?php  
session_start();

if (isset($_SESSION['user_email'])) {
    $horaAtual = time(); // time() retorna a hora atual em segundos desde a Era Unix (1 de Janeiro de 1970 00:00:00 GMT)
    if (!isset($_SESSION['user_email'])) {
        $tempoDecorrido = 0;
        unset($_SESSION['user_email']);
        session_write_close();
    } else {
        if (isset($_SESSION)) {
            $tempoDecorrido = $horaAtual - $_SESSION['ultima_atividade'];
            if ($tempoDecorrido > 600) { 
                $tempoDecorrido = 0;
                header('location:./logout.php');
            }    
        }
    }

    if (isset($_POST['data_inicio']) && isset($_POST['data_fim'])) {
        $data_inicio = $_POST['data_inicio'];
        $data_fim = $_POST['data_fim'];
    } else {
        // Obter a data atual
        $dataAtual = new DateTime();
        // Calcular 7 dias antes
        $dataAntes = clone $dataAtual; // Clone para evitar modificar a data atual
        $dataAntes->modify('-7 days');
        $data_inicio = $dataAntes->format('Y-m-d');
        $data_fim = $dataAtual->format('Y-m-d');
    }
} else {
    header('location:./logout.php');
    exit();
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
        <title>Humor AI</title>
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
            <!-- <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input required class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form> -->
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                       <li><a class="dropdown-item" href="./logout.php">Logout</a></li>
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
                            <?php if ($_SESSION['user_superuser'] > 0){
                                echo'<div class="sb-sidenav-menu-heading">Admnistrador</div>';
                                echo' <a class="nav-link" href="detail_user.php">';
                                echo '<div class="sb-nav-link-icon"><i class="fas fa-gamepad"></i></div>Administrador</a>';
                            }?>

                            <?php if ($_SESSION['user_superuser'] > 1){
                                echo'<div class="sb-sidenav-menu-heading"></div>';
                                echo'<a class="nav-link" href="painel_controle.php">';
                                echo'<div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>Painel de Controle</a>';
                            }?>

                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Bem-vindo:</div>
                        <?php echo $_SESSION['user_nome'] . ' ' . $_SESSION['user_sobrenome']; ?>
                    </div>
                </nav>
            </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Nuvem de Palavras</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Palavras mais encontradas num período específico.</li>
                    </ol>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-calendar-days me-1"></i>
                                    Busca por data                                    
                                </div>
                                <form action="detail_cloudwords.php" method="post">
                                    <label for="data_inicio">Data de Início</label>
                                    <input required type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?php echo isset($_POST['data_inicio']) ? $_POST['data_inicio'] : ''; ?>"> 
                                    <br>
                                    <label for="data_fim">Data de Fim</label>
                                    <input required type="date" name="data_fim" id="data_fim" class="form-control" value="<?php echo isset($_POST['data_fim']) ? $_POST['data_fim'] : ''; ?>"> 
                                    <br>
                                    <button type="submit" class="btn btn-primary">Buscar</button>
                                </form>
                                
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fab fa-cloudversify me-1"></i>
                                    Nuvem de Palavras
                                </div>
                                <div class="card-body">
                                    <iframe class="card-iframe" src="cloudwords.php?data_inicio=<?php echo $data_inicio;?>&data_fim=<?php echo $data_fim;?>" style="display:block; width:100%; height:400px;"></iframe>
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

                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>
</html>
