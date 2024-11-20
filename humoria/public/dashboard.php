<?php
session_start();

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

                            <?php if ($_SESSION['user_superuser'] > 1){
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

                            <?php 
                                // test
                                // var_dump($result); 
                            ?>
                            
                        </ol>
                        <!-- <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">Primary Card</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="#">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-warning text-white mb-4">
                                    <div class="card-body">Warning Card</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="#">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-success text-white mb-4">
                                    <div class="card-body">Success Card</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="#">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-danger text-white mb-4">
                                    <div class="card-body">Danger Card</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="#">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-calendar-day me-1"></i>
                                        Busca por data
                                    </div>
                                    <?php 
                                        // #test
                                        // echo "id_empresa: " . $_SESSION['empresa_id'] . "<br>";
                                        // echo "$result: ";
                                        // var_dump($files);
                                        // echo "<br>";
                                        // var_dump ($result);
                                    ?>

                                    <form action="dashboard.php" method="post">
                                        <label for="data_inicio">Data de Início</label>
                                        <input required type="date" name="data_inicio" id="data_inicio" class="form-control"
                                            value="<?php if (isset($_POST['data_inicio'])) { echo $_POST['data_inicio']; } ?>">
                                        <br>
                                        <label for="data_fim">Data de Fim</label>
                                        <input required type="date" name="data_fim" id="data_fim" class="form-control"
                                            value="<?php if (isset($_POST['data_fim'])) { echo $_POST['data_fim']; } ?>">
                                        <br>
                                        <button type="submit" class="btn btn-primary">Buscar</button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-pie me-1"></i>
                                        Gráfico de sentimentos
                                    </div>
                                    <div class="card-body">
                                        <canvas id="meuGrafico"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Tabela de Análise de Sentimentos
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">

                                    <table id="datatablesSimple">
                                        <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Data</th>
                                            <th scope="col">Ramal</th>
                                            <th scope="col">File</th>
                                            <th scope="col">Emoção Vader</th>
                                            <th scope="col">Emoção Gemini</th>
                                            <th scope="col">Ouvir</th>
                                    </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Name</th>
                                                <th>Position</th>
                                                <th>Office</th>
                                                <th>Age</th>
                                                <th>Start date</th>
                                                <th>Salary</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                        <?php
                                $emotionus = [];
                                foreach ($result as $file) {
                                    echo '<tr>';
                                    echo '<th scope="row">' . $file["id"] . '</th>';

                                    $filename = $file["filename"];
                                    $pos1 = strpos($filename, '_');
                                    $pos2 = strpos($filename, '_', $pos1 + 1);

                                    $dateString = substr($filename, $pos2 + 1, 8);
                                    $formattedDate = substr($dateString, 6, 2) . '/' . substr($dateString, 4, 2) . '/' . substr($dateString, 0, 4);

                                    if(is_numeric(substr($file["data"],8,2) . substr($file["data"],5,2) . substr($file["data"],0,4))){
                                        echo '<td>' . substr($file["data"],8,2) . '/' . substr($file["data"],5,2) . '/' . substr($file["data"],0,4).'</td>';
                                    }else{
                                        echo '<td>' . $formattedDate . '</td>';
                                    }
                                    echo '<td>' . $file["path"] . '</td>';
                                    echo '<td>' . $file["filename"] . '</td>';
                                    echo '<td>' . $file["emotion_vader"] . '</td>';
                                    echo '<td>' . $file["emotion_gemini"] . '</td>';

                                    echo '<td><audio src="./../recordings (3)/' . $_SESSION["empresa_id"] . '/' . $file["path"] . '/' . $file["filename"] . '" controls></audio></td>';
                                    echo '</tr>';

                                    array_push($emotionus, $file["emotion_vader"]);
                                }
                                $freq = array_count_values($emotionus);
                                $soma_total = array_sum($freq);
                                $dados = array(
                                    isset($freq['Positivo']) ? ($freq['Positivo'] / $soma_total) * 100 : 0,
                                    isset($freq['Neutro']) ? ($freq['Neutro'] / $soma_total) * 100 : 0,
                                    isset($freq['Negativo']) ? ($freq['Negativo'] / $soma_total) * 100 : 0
                                );
                                $jsonArray = json_encode(array_values($dados));
                                ?>
                                </tbody>                                    </tbody>
                                    </table>
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
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const jsonArray = <?php echo $jsonArray; ?>;

            const dadosPizza = {
                labels: ['Sentimentos Positivos', 'Sentimentos Neutros', 'Sentimentos Negativos'],
                datasets: [{
                    label: 'Sentimentos',
                    backgroundColor: ['green', 'yellow', 'red'],
                    borderColor: ['rgba(255, 99, 132, 0.6)'],
                    data: jsonArray,
                }]
            };

            const configPizza = {
                type: 'pie',
                data: dadosPizza,
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed + ' %';
                                    return label;
                                }
                            }
                        }
                    }
                }
            };

            const meuGrafico = new Chart(
                document.getElementById('meuGrafico'),
                configPizza
            );
        });
    </script>
    </body>
</html>
