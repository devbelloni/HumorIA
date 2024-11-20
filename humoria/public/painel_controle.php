
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

# Verifica se o usuário é superusuário
if($_SESSION['user_superuser']==2){

    include_once("./../dbm/DBConfig.php");

    $dsn = DBConfig::getDSN();
    $username = DBConfig::getUsername();
    $password = DBConfig::getPassword();
    $options = DBConfig::getOptions();
    $servername = DBConfig::getServer();
    $dbname = DBConfig::getBdName();

    $dbConnector = new DatabaseConnector($dsn, $username, $password, $options);
    $usuarios = new BDController($dbConnector, "users", "id_empresa", $_SESSION['empresa_id']);
    $everybody = $usuarios->getter_all("empresas");

    
    $pj = new BDController($dbConnector, "empresas", "id", "ALL");
    $jobs = $pj->getter_all_empresas($_SESSION['user_superuser']);




    $num_empresas=0;
    
    foreach($everybody as $pessoas){
        $num_empresas=+1;
    }
    

    // $files = new BDController($dbConnector, "files", "id_empresa", "");
    // $arquivos = $files->getter();

    $vader = new BDController($dbConnector, "emotion_vader", "id_empresa", $_SESSION['empresa_id']);
    $emo = $vader->getter_all_vader();


    $emotionus = [];

    foreach($emo as $emotions){  
        array_push($emotionus, $emotions["emotion_vader"]);
    }



    $freq = array_count_values($emotionus);
    $soma_total = array_sum($freq);

    $dados = array(
        isset($freq['Positivo']) ? ($freq['Positivo'] / $soma_total) * 100 : 0,
        isset($freq['Neutro']) ? ($freq['Neutro'] / $soma_total) * 100 : 0,
        isset($freq['Negativo']) ? ($freq['Negativo'] / $soma_total) * 100 : 0
    );
    $jsonArray = json_encode(array_values($dados));

    if(isset($_SESSION['empresa_id'])){
    $display="active";
    }else{
    $display="deactive";
    }
}

else{
    header('location:./dashboard.php');
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
<!--                         <li><hr class="dropdown-divider" /></li>
 -->                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
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
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">Usuários Cadastrados no Sistema</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <?php 
                                            $num_users = 0;
                                            foreach ($everybody as $user) {
                                                $num_users++;
                                            }
                                            echo '<p class="medium text-white">' . $num_users . ' Usuários</p>';
                                            
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-xl-3 col-md-6">
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
                            </div> -->
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-area me-1"></i>
                                        Cadastro de novos clientes
                                    </div>
                                    <div class="card-body">
                                        <a href="cadastro.php"><button>Cadastrar Usuário</button></a>
                                        <a href="cadastro_empresa.php"><button>Cadastrar Empresa</button></a>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        Bar Chart Example
                                    </div>

                                        aqui vai o conteudo

                                </div>
                            </div> -->
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Controle de usuários
                            </div>
                            <div class="card-body">
                                <table class="table table-dark table-hover">

                                    <thead>
                                        <tr>
                                          <th scope="col">Id</th>
                                          <th scope="col">Empresa</th>
                                          <th scope="col">Nome</th>
                                          <th scope="col">Sobrenome</th>
                                          <th scope="col"></th>
                                          <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>            
                                        <?php  
                                          foreach($everybody as $usuario){
                                            echo '<tr>';
                                            echo '<th scope="row">' . $usuario["user_id"] . '</th>';
                                            echo '<td>' . $usuario["razao"] . '</td>';
                                            echo '<td>' . $usuario["user_nome"] . '</td>';
                                            echo '<td>' . $usuario["user_sobrenome"] . '</td>';
                                            echo '<td>';
                                            echo '<a href="editar_usuario.php?id=' . $usuario["user_id"] . '">';
                                            echo '<button class="btn active btn-outline-warning">Editar</button>';
                                            echo '</a>';
                                            echo '</td>';    
                                            echo '<td>';    
                                            echo '<a href="deletar_usuario.php?id=' . $usuario["user_id"] . '&nome=' . $usuario["user_nome"] . '&sobrenome=' . $usuario["user_sobrenome"] . '" ';
                                            echo 'onclick="return confirm(\'Tem certeza que deseja deletar o usuário ' . $usuario["user_nome"] . ' ' . $usuario["user_sobrenome"] . '?\');">';
                                            echo '<button class="btn active btn-outline-danger">Deletar</button>';
                                            echo '</a>';
                                            echo '</td>';
                                            echo '</tr>';
                                          }
                                        ?>
                                      </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Controle de empresas
                            </div>
                            <div class="card-body">
                                <table class="table table-dark table-hover">

                                    <thead>
                                        <tr>
                                          <th scope="col">Id</th>
                                          <th scope="col">Empresa</th>
                                          <th scope="col">CNPJ</th>
                                          <th scope="col"></th>
                                          <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>            
                                        <?php
                                        // Initialize an array to store seen empresa_ids
                                        $seen_empresas = array();

                                        foreach($jobs as $empresa){
                                        // Check if this empresa_id has already been displayed
                                        if (!in_array($empresa["id"], $seen_empresas)) {
                                            // Add this empresa_id to the seen array
                                            $seen_empresas[] = $empresa["id"];

                                            // Display the table row
                                            echo '<tr>';
                                            echo '<th scope="row">' . $empresa["id"] . '</th>';
                                            echo '<td>' . $empresa["razao"] . '</td>';
                                            echo '<td>' . $empresa["cnpj"] . '</td>';
                                            echo '<td>';
                                            echo '<a href="editar_empresa.php?id=' . $empresa["id"] . '">';
                                            echo '<button class="btn active btn-outline-warning">Editar</button>';
                                            echo '</a>';
                                            echo '</td>';    
                                            echo '<td>';    
                                            echo '<a href="deletar_empresa.php?id=' . $empresa["id"] . '&razao=' . $empresa["razao"] . '&CNPJ=' . $empresa["cnpj"] . '" ';
                                            echo 'onclick="return confirm(\'Tem certeza que deseja deletar a empresa ' . $empresa["razao"] . ', CNPJ: ' . $empresa["cnpj"] . '?\');">';
                                            echo '<button class="btn active btn-outline-danger">Deletar</button>';
                                            echo '</a>';
                                            echo '</td>';
                                            echo '</tr>';
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
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
        <script>
            // Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#292b2c';

// Bar Chart Example
const jsonArray = <?php echo $jsonArray; ?>;

var ctx = document.getElementById("myBarChart");
var myLineChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Negativo", "Neutro", "Positivo"],
    datasets: [{
      label: "Revenue",
      backgroundColor: ['red', 'yellow', 'green'],
      borderColor: "rgba(2,117,216,1)",
      data: jsonArray,
    }],
  },
  options: {
    scales: {
      xAxes: [{
        time: {
          unit: 'percentiles'
        },
        gridLines: {
          display: false
        },
        ticks: {
          maxTicksLimit: 6
        }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          max: 100,
          maxTicksLimit: 5
        },
        gridLines: {
          display: true
        }
      }],
    },
    legend: {
      display: false
    }
  }
});
        </script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>