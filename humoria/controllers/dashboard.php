<?php
session_start();

require("./../dbm/DatabaseConnector.php");
require("./../controllers/BDController.php");

$status = [
    'home' => 'btn-sm btn-outline-secondary',
    'contato' => 'btn-sm btn-outline-secondary',
    'detalhes' => 'btn-sm btn-outline-secondary',
    'dashboard' => 'btn-outline-success',
    'pipeline' => 'btn-sm btn-outline-secondary',
    'administrador' => 'btn-sm btn-outline-secondary',
];

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

if (isset($_SESSION['empresa_id'])) {
    $display = "active";
} else {
    $display = "deactive";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css"
          integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js"
            integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>
    <link rel="icon" href="./../static/favicon/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <?php include("./_menu.php"); ?>
        </aside>
        <div class="main-content">
            <header class="header">
                <h1>Dashboard</h1>
            </header>
            <section class="content">
                <div class="row">
                    <div class="col-3">
                        <div class="row">
                            <form action="dashboard.php" method="post">
                                <h3>Busca por data</h3>
                                <label for="data_inicio">Data de Início</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control"
                                       value="<?php if (isset($_POST['data_inicio'])) { echo $_POST['data_inicio']; } ?>">
                                <br>
                                <label for="data_fim">Data de Fim</label>
                                <input type="date" name="data_fim" id="data_fim" class="form-control"
                                       value="<?php if (isset($_POST['data_fim'])) { echo $_POST['data_fim']; } ?>">
                                <br>
                                <button type="submit" class="btn btn-primary">Buscar</button>
                            </form>
                        </div>
                        <div class="row">
                            <?php if ($result) { ?>
                                <div class="container-fluid text-center">
                                    <canvas id="meuGrafico"></canvas>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-9 table-container">
                        <table class="table table-dark table-hover">
                            <thead class="thead-fixed">
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

                                echo '<td>' . $formattedDate . '</td>';
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <footer class="footer">
                <p>© 2024 Seu Nome</p>
            </footer>
        </div>
    </div>
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
