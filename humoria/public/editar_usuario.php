<?php  
session_start();

require("./../dbm/DatabaseConnector.php");
require("./../controllers/BDController.php");

if (isset($_SESSION['user_email'])) {

    if (isset($_GET['id'])) {
        $id = $_GET['id'];


        include_once("./../dbm/DBConfig.php");

        $dsn = DBConfig::getDSN();
        $username = DBConfig::getUsername();
        $password = DBConfig::getPassword();
        $options = DBConfig::getOptions();
        $servername = DBConfig::getServer();
        $dbname = DBConfig::getBdName();
        

        $dbConnector = new DatabaseConnector($dsn, $username, $password, $options);
        $usuario = new BDController($dbConnector, "users", "id", $id);

        if (!empty($_POST)) {
            // Check if the checkbox is set
            $ratio = isset($_POST['new_superuser']) ? 1 : 0;

            $data = [
                'nome' => $_POST["new_name"],
                'sobrenome' => $_POST["new_sobrenome"],
                'email' => $_POST["new_email"],
                'telefone' => $_POST["new_telefone"],
                'superuser' => $ratio,
            ];

            $usuario->update($data);

            // Limpa completamente o array $_POST
            $_POST = array();

            // Recarrega os dados do banco de dados após a atualização
            $result = $usuario->getter();
        } else {
            // Carrega os dados do banco de dados na primeira carga da página
            $result = $usuario->getter();
        }

        if (empty($result)) {
            echo "ID não encontrado no banco de dados";
            exit;
        }
    } else {
        echo "ID não encontrado na URL";
        exit;
    }

} else {
    $_SESSION['user_email'] = null;
    session_destroy();
    header("Location: ./index.php");
    exit;
}

$horaAtual = time();
if (!isset($_SESSION['user_email'])) {
    unset($_SESSION['user_email']);
    session_write_close();
} else {
    if (isset($_SESSION)) {
        $tempoDecorrido = $horaAtual - $_SESSION['ultima_atividade'];
        if ($tempoDecorrido > 600) {
            header('Location: ./logout.php');
            exit;
        }
    }
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
                        <!-- <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li> -->
<!--                         <li><hr class="dropdown-divider" /></li>
 -->                        <li><a class="dropdown-item" href="./logout.php">Logout</a></li>
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
                            <!-- <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Layouts
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="layout-static.html">Static Navigation</a>
                                    <a class="nav-link" href="layout-sidenav-light.html">Light Sidenav</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                Pages
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                        Authentication
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link" href="login.html">Login</a>
                                            <a class="nav-link" href="register.html">Register</a>
                                            <a class="nav-link" href="password.html">Forgot Password</a>
                                        </nav>
                                    </div>
                                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                                        Error
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link" href="401.html">401 Page</a>
                                            <a class="nav-link" href="404.html">404 Page</a>
                                            <a class="nav-link" href="500.html">500 Page</a>
                                        </nav>
                                    </div>
                                </nav>
                            </div> -->
                            <?php if ($_SESSION['user_superuser'] > 0){
                                echo'<div class="sb-sidenav-menu-heading"></div>';
                                echo'<a class="nav-link" href="painel_controle.php">';
                                echo'<div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>Painel de Controle</a>';
                            }?>
                            <!-- <a class="nav-link" href="tables.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                Tables
                            </a> -->
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
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Análise de sentimentos</li>
                        </ol>
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-area me-1"></i>
                                        Editar Usuário
                                    </div>
      
                                    <section class="content-main">
                                        <div class="row row-edit-user justify-content-center align-items-center">
                                            <div class="card-body">
                                                <h3 text-right></h3>
                                                <form action="./editar_usuario.php?id=<?php echo $result[0]['id'] ?>" style="text-decoration: none;" method="post">
                                                    <label for="new_name">Nome</label><br>
                                                    <input required type="text" value="<?php echo htmlspecialchars($result[0]['nome']) ?>" name="new_name"></input><br>
                                                    <label for="new_sobrenome">Sobrenome</label><br>
                                                    <input required type="text" value="<?php echo htmlspecialchars($result[0]['sobrenome']) ?>" name="new_sobrenome"></input><br>
                                                    <label for="new_telefone">Telefone</label><br>
                                                    <input required type="text" value="<?php echo htmlspecialchars($result[0]['telefone']) ?>" name="new_telefone"></input><br>
                                                    <label for="new_email">Email</label><br>
                                                    <input required type="email" value="<?php echo htmlspecialchars($result[0]['email']) ?>" name="new_email"></input><br>
                                                    <label for="new_password">Senha</label><br>
                                                    <button type="submit" class="btn btn-primary mt-2" name="submitBtn">Enviar</button>
                                                    <?php if ($_SESSION['user_superuser'] == 2) {
                                                        echo '<a href="./painel_controle.php"><button type="button" class="btn btn-secondary mt-2">Retornar</button></a>';
                                                    } else{
                                                        echo '<a href="./detail_user.php"><button type="button" class="btn btn-secondary mt-2">Retornar</button></a>';
                                                    }?>
                                                    <a href="./password.php"><button type="button" class="btn btn-warning mt-2">Trocar senha</button></a>
                                                </form>
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2023</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
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


