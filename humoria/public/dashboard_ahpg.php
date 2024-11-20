<?php
session_start();
if (!isset($_SESSION['user_superuser'])==2) {
    header('location:./logout.php');
}

if (isset($_SESSION['empresa_id'])) {
    $display = "active";
} else {
    $display = "deactive";
    header('location:./index.php');

}

require("./../dbm/DatabaseConnector.php");
require("./../controllers/BDController.php");

$horaAtual = time();
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

include_once("./../dbm/DBConfig.php");

$dsn = DBConfig::getDSN();
$username = DBConfig::getUsername();
$password = DBConfig::getPassword();
$options = DBConfig::getOptions();
$servername = DBConfig::getServer();
$dbname = DBConfig::getBdName();


$dbConnector = new DatabaseConnector($dsn, $username, $password, $options); 
$files = new BDController($dbConnector, "files", "id_empresa", $_SESSION['empresa_id']);

if (isset($_POST['data_inicio']) && isset($_POST['data_fim'])) {
    $result = $files->getter_emotions($_POST['data_inicio'], $_POST['data_fim']);
} else {
    $_POST = [];
    $result = $files->getter_emotions();
}



// $sql = "SELECT * FROM {$this->tableName} WHERE {$this->column} = :value";


// traz todos os usuários
$users = new BDController($dbConnector, "users", "1", 1);
$result_users = $users->getter();

// traz todas as empresas
$empresas = new BDController($dbConnector, "empresas", "1", 1);
$result_empresas = $empresas->getter();

// traz todos os arquivos
$files = new BDController($dbConnector, "files", "1", 1);
$result_files = $files->getter_all_files_emotions();

// traz todas as palavras
$words = new BDController($dbConnector, "words", "1", 1);
$result_words = $words->getter();


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
            <a class="navbar-brand ps-3" href="index.html">Humor AI</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <!-- <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li> -->
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
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item deactive">Usuários</li>
                        </ol>

                        <!-- FILES -->
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-calendar-day me-1"></i>
                                        Arquivos

                                    </div>      
                                    <table id="files-table" class="table table-hover"" style="white-space: nowrap;">
                                        <thead>
                                            <tr>
                                                <th scope="col">Id_File</th>
                                                <th scope="col">Id_Empresa</th>
                                                <th scope="col">Id_Emotion</th>
                                                <th scope="col">Filename</th>
                                                <th scope="col">Ramal</th>
                                                <th scope="col">Data</th>
                                                <th scope="col">Vader</th>
                                                <th scope="col">Gemini</th>  
                                                <th scope="col">Resumo</th>                                              
                                                <th scope="col">Criação</th>
                                                <th scope="col">Modificação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                foreach ($result_files as $file) {
                                                    echo '<tr>';
                                                    echo '<th scope="row">' . $file["id_file"] . '</th>';
                                                    echo '<td>' . $file['id_empresa'] . '</td>';
                                                    echo '<td>' . $file['id'] . '</td>';
                                                    echo '<td>' . $file['filename'] . '</td>';
                                                    echo '<td>' . $file["path"] . '</td>';
                                                    echo '<td>' . $file["data"] . '</td>';
                                                    echo '<td>' . $file["emotion_vader"] . '</td>';
                                                    echo '<td>' . $file["emotion_gemini"] . '</td>';
                                                    echo '<td>' . $file["resumo"] . '</td>';
                                                    echo '<td>' . $file["data_criacao"] . '</td>';
                                                    echo '<td>' . $file["data_modificacao"] . '</td>';
                                                }
                                            ?>
                                        </tbody> 
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- WORDS -->
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-calendar-day me-1"></i>
                                        Palavras
                                    </div>
                                    <table id="words-table" class="table table-hover"" style="white-space: nowrap;">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">id_file</th>
                                                <th scope="col">Palavra</th>
                                                <th scope="col">Criação</th>
                                                <th scope="col">Modificação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                foreach ($result_words as $word) {
                                                    echo '<tr>';
                                                    echo '<th scope="row">' . $word["id"] . '</th>';
                                                    echo '<td>' . $word['id_file'] . '</td>';
                                                    echo '<td>' . $word['word'] . '</td>';
                                                    echo '<td>' . $word["data_criacao"] . '</td>';
                                                    echo '<td>' . $word["data_modificacao"] . '</td>';
                                                }
                                            ?>
                                        </tbody> 
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- USUÁRIOS -->
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-calendar-day me-1"></i>
                                        Usuários
                                    </div>      
                                    <table id="user-table" class="table table-hover"" style="white-space: nowrap;">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Id_Empresa</th>
                                                <th scope="col">Nome</th>
                                                <th scope="col">Telefone</th>
                                                <th scope="col">E-mail</th>
                                                <th scope="col">Superuser</th>
                                                <th scope="col">Criação</th>
                                                <th scope="col">Modificação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                foreach ($result_users as $user) {
                                                    echo '<tr>';
                                                    echo '<th scope="row">' . $user["id"] . '</th>';
                                                    echo '<td>' . $user['id_empresa'] . '</td>';
                                                    echo '<td>' . $user['nome'] . $user['sobrenome'] . '</td>';
                                                    echo '<td>' . $file["telefone"] . '</td>';
                                                    echo '<td>' . $file["email"] . '</td>';
                                                    echo '<td>' . $file["superuser"] . '</td>';
                                                    echo '<td>' . $file["data_criacao"] . '</td>';
                                                    echo '<td>' . $file["data_modificacao"] . '</td>';
                                                }
                                            ?>
                                        </tbody> 
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- EMPRESAS -->
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-calendar-day me-1"></i>
                                        Empresas
                                    </div>
                                    <table id="empresa-table" class="table table-hover"" style="white-space: nowrap;">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Razão Social</th>
                                                <th scope="col">CNPJ</th>
                                                <th scope="col">Telefone</th>
                                                <th scope="col">E-mail</th>
                                                <th scope="col">Endereço</th>
                                                <th scope="col">Cidade</th>
                                                <th scope="col">Estado</th>
                                                <th scope="col">Pais</th>
                                                <th scope="col">CEP</th>
                                                <th scope="col">Criação</th>
                                                <th scope="col">Modificação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                foreach ($result_empresas as $empresa) {
                                                    echo '<tr>';
                                                    echo '<th scope="row">' . $empresa["id"] . '</th>';
                                                    echo '<td>' . $empresa['razao'] . '</td>';
                                                    echo '<td>' . $empresa['cnpj'] .'</td>';
                                                    echo '<td>' . $empresa["telefone"] . '</td>';
                                                    echo '<td>' . $empresa["email"] . '</td>';
                                                    echo '<td>' . $empresa["endereco"] . '</td>';
                                                    echo '<td>' . $empresa["cidade"] . '</td>';
                                                    echo '<td>' . $empresa["estado"] . '</td>';
                                                    echo '<td>' . $empresa["pais"] . '</td>';
                                                    echo '<td>' . $empresa["cep"] . '</td>';
                                                    echo '<td>' . $empresa["data_criacao"] . '</td>';
                                                    echo '<td>' . $empresa["data_modificacao"] . '</td>';
                                                }
                                            ?>
                                        </tbody> 
                                    </table>
                                </div>
                            </div>
                        </div>


                        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
                        <script>
                            new window.simpleDatatables.DataTable("#user-table")
                        </script>
                        <script>        
                            new window.simpleDatatables.DataTable("#empresa-table")
                        </script>
                        <script>
                            new window.simpleDatatables.DataTable("#files-table")
                        </script>
                        <script>
                            new window.simpleDatatables.DataTable("#words-table")
                        </script>
                        

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


        <!-- <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script> -->
       
    </body>
</html>
</html>

