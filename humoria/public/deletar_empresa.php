<?php  
session_start();

require("./../dbm/DatabaseConnector.php");
require("./../controllers/BDController.php");

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

if (isset($_SESSION['user_email'])) {


    if (isset($_GET['id'])&&isset($_GET['razao'])&&isset($_GET['CNPJ'])) {
        $id = $_GET['id'];
        $razao = $_GET['razao'];
        $cnpj = $_GET['CNPJ'];


        include_once("./../dbm/DBConfig.php");

        $dsn = DBConfig::getDSN();
        $username = DBConfig::getUsername();
        $password = DBConfig::getPassword();
        $options = DBConfig::getOptions();
        $servername = DBConfig::getServer();
        $dbname = DBConfig::getBdName();

        $data = [
            'id' => $id,
            'nome' => $razao,
            'sobrenome' => $cnpj,
        ];

        var_dump($data);

        $dbConnector = new DatabaseConnector($dsn, $username, $password, $options);
        $empresa = new BDController($dbConnector, "empresas", "id", $id);

        $empresa->delete($data);
        header("Location: ./painel_controle.php");
        exit;

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


?>
