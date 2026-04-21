<?php  
session_start();

// 1. Trava de Segurança: Verifica se está logado e se é Super Usuário (nível 2)
if (!isset($_SESSION['user_email'])) {
    header('location:./index.php');
    exit;
}

if ($_SESSION['user_superuser'] != 2) {
    header('location:./dashboard.php');
    exit;
}

// 2. Controle de Timeout (10 minutos)
$horaAtual = time();
if (isset($_SESSION['ultima_atividade'])) {
    $tempoDecorrido = $horaAtual - $_SESSION['ultima_atividade'];
    if ($tempoDecorrido > 600) {
        header('location:./logout.php');
        exit;
    }
}
$_SESSION['ultima_atividade'] = $horaAtual;

// 3. Inicialização e Consultas ao Banco
require("./../dbm/DatabaseConnector.php");
require("./../controllers/BDController.php");
include_once("./../dbm/DBConfig.php");

$dsn = DBConfig::getDSN();
$username = DBConfig::getUsername();
$password = DBConfig::getPassword();
$options = DBConfig::getOptions();

$dbConnector = new DatabaseConnector($dsn, $username, $password, $options);

// Busca todos os usuários (Join com empresas)
$usuariosObj = new BDController($dbConnector, "users", "id_empresa", $_SESSION['empresa_id']);
$everybody = $usuariosObj->getter_all("empresas");

// Busca todas as empresas
$pj = new BDController($dbConnector, "empresas", "id", "ALL");
$jobs = $pj->getter_all_empresas($_SESSION['user_superuser']);

// Lógica de Sentimento (Vader)
$vader = new BDController($dbConnector, "emotion_vader", "id_empresa", $_SESSION['empresa_id']);
$emo = $vader->getter_all_vader();

$emotionus = [];
foreach($emo as $emotions){  
    $emotionus[] = $emotions["emotion_vader"];
}

// Cálculo de percentuais para o Gráfico
$jsonArray = json_encode([0, 0, 0]); // Default
if (!empty($emotionus)) {
    $freq = array_count_values($emotionus);
    $soma_total = array_sum($freq);
    
    $dados = [
        isset($freq['Negativo']) ? round(($freq['Negativo'] / $soma_total) * 100, 2) : 0,
        isset($freq['Neutro'])   ? round(($freq['Neutro'] / $soma_total) * 100, 2) : 0,
        isset($freq['Positivo']) ? round(($freq['Positivo'] / $soma_total) * 100, 2) : 0
    ];
    $jsonArray = json_encode($dados);
}
?>

<!DOCTYPE html>
<html lang="pt_br">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Painel de Controle - Humor AI</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="dashboard.php">Humor AI</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
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
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div> Dashboard
                        </a>
                        <a class="nav-link" href="detail_cloudwords.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-cloud"></i></div> Nuvem de palavras
                        </a>
                        <div class="sb-sidenav-menu-heading">Administrador</div>
                        <a class="nav-link" href="detail_user.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-gamepad"></i></div> Usuários
                        </a>
                        <a class="nav-link active" href="painel_controle.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div> Painel de Controle
                        </a>
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
                    <h1 class="mt-4">Painel de Controle</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Gestão Global do Sistema</li>
                    </ol>
                    
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">Total de Usuários: <?php echo count($everybody); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header"><i class="fas fa-table me-1"></i> Usuários Cadastrados</div>
                        <div class="card-body">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Empresa</th>
                                        <th>Nome</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($everybody as $u): ?>
                                        <tr>
                                            <td><?= $u["user_id"] ?></td>
                                            <td><?= $u["razao"] ?></td>
                                            <td><?= $u["user_nome"] . " " . $u["user_sobrenome"] ?></td>
                                            <td>
                                                <a href="editar_usuario.php?id=<?= $u["user_id"] ?>" class="btn btn-sm btn-outline-warning">Editar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header"><i class="fas fa-building me-1"></i> Empresas</div>
                        <div class="card-body">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Razão Social</th>
                                        <th>CNPJ</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $seen = [];
                                    foreach($jobs as $e): 
                                        if(!in_array($e['id'], $seen)): 
                                            $seen[] = $e['id'];
                                    ?>
                                        <tr>
                                            <td><?= $e["id"] ?></td>
                                            <td><?= $e["razao"] ?></td>
                                            <td><?= $e["cnpj"] ?></td>
                                            <td>
                                                <a href="editar_empresa.php?id=<?= $e["id"] ?>" class="btn btn-sm btn-outline-warning">Editar</a>
                                            </td>
                                        </tr>
                                    <?php endif; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header"><i class="fas fa-chart-bar me-1"></i> Humor Global (Vader)</div>
                        <div class="card-body">
                            <canvas id="myBarChart" width="100%" height="40"></canvas>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
    <script>
        const sentimentData = <?php echo $jsonArray; ?>;
        var ctx = document.getElementById("myBarChart");
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ["Negativo", "Neutro", "Positivo"],
                datasets: [{
                    backgroundColor: ['#dc3545', '#ffc107', '#198754'],
                    data: sentimentData,
                }],
            },
            options: {
                scales: {
                    yAxes: [{ ticks: { min: 0, max: 100 } }]
                },
                legend: { display: false }
            }
        });
    </script>
</body>
</html>