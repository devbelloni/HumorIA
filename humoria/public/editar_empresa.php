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
        $empresa = new BDController($dbConnector, "empresas", "id", $id);

        if(isset($_POST['new_razao']) && isset($_POST['new_cnpj'])){
            $updateData = [
                'razao'=> $_POST['new_razao'],
                'cnpj'=> $_POST['new_cnpj'],
                'endereco'=> $_POST['new_endereco'],
                'cidade'=> $_POST['new_cidade'],
                'estado'=> $_POST['new_estado'],
                'pais'=> $_POST['new_pais'],
                'telefone'=> $_POST['new_telefone'],
                'email'=> $_POST['new_email'],
            ];
            $updateCount = $empresa->update($updateData);
            echo "Number of records updated: " . $updateCount;
            }

        $everybody = $empresa->getter();
    }
        

        // $files = new BDController($dbConnector, "files", "id_empresa", "");
        // $arquivos = $files->getter();


            // // Exemplo de uso do método setter
            // // $newData = [
            // //     'nome' => 'Novo Usuário',
            // //     'email' => 'novo@usuario.com'
            // // ];
            // // $newId = $usuarios->setter($newData);
            // // echo "New record ID: " . $newId;

            // Exemplo de uso do método update

            // // Exemplo de uso do método delete
            // // $deleteCount = $usuarios->delete();
            // // echo "Number of records deleted: " . $deleteCount;


        if(isset($_SESSION['empresa_id'])){
        $display="active";
        }else{
        $display="deactive";
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
                            <li class="breadcrumb-item active">Super Usuário</li>
                        </ol>
                        <br>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-building me-1"></i>
                                        Editar Empresa
                                    </div>                                    
                                    <!-- <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div> -->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">

                                            <form action="<?php echo 'editar_empresa.php?id=' . $id; ?>" method="post">
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="razao" class="form-label">Nome da Empresa</label>
                                                            <input required type="text" class="form-control" name="new_razao" value="<?php echo htmlspecialchars($everybody[0]['razao']); ?>">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="cnpj" class="form-label">CNPJ</label>
                                                            <input required type="text" class="form-control" name="new_cnpj" value="<?php echo htmlspecialchars($everybody[0]['cnpj']); ?>">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="telefone" class="form-label">Telefone</label>
                                                            <input required type="tel" class="form-control" name="new_telefone" value="<?php echo htmlspecialchars($everybody[0]['telefone']); ?>">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="email" class="form-label">Email</label>
                                                            <input required type="email" class="form-control" name="new_email" value="<?php echo htmlspecialchars($everybody[0]['email']); ?>">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="endereco" class="form-label">Endereço</label>
                                                            <input required type="text" class="form-control" name="new_endereco" value="<?php echo htmlspecialchars($everybody[0]['endereco']); ?>">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="cidade" class="form-label">Cidade</label>
                                                            <input required type="text" class="form-control" name="new_cidade" value="<?php echo htmlspecialchars($everybody[0]['cidade']); ?>">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="estado" class="form-label">Estado</label>
                                                            <input required type="text" class="form-control" name="new_estado" value="<?php echo htmlspecialchars($everybody[0]['estado']); ?>">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="cep" class="form-label">CEP</label>
                                                            <input required type="text" class="form-control" name="new_cep" value="<?php echo htmlspecialchars($everybody[0]['cep']); ?>">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="pais" class="form-label">País</label>
                                                            <input required type="text" class="form-control" name="new_pais" value="<?php echo htmlspecialchars($everybody[0]['pais']); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <button type="submit" class="btn btn-primary" name="submitBtn">Enviar</button>
                                                        <?php if ($_SESSION['user_superuser'] == 2) {
                                                                    echo '<a href="./painel_controle.php"><button type="button" class="btn btn-secondary mt-2">Retornar</button></a>';
                                                                } else{
                                                                    echo '<a href="./detail_user.php"><button type="button" class="btn btn-secondary mt-2">Retornar</button></a>';
                                                                }?>                                                    </div>
                                                </div>
                                            </form>

                                    
                                </div>


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
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>

