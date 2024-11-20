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

    if (isset($_GET['id'])&&isset($_GET['nome'])&&isset($_GET['sobrenome'])) {
        $id = $_GET['id'];
        $nome = $_GET['nome'];
        $sobrenome = $_GET['sobrenome'];




        $dsn = 'mysql:host=127.0.0.1:3306;dbname=humorai';
        $username = 'root';
        $password = '2aGHQPcW2#wxrC@e';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $data = [
            'id' => $id,
            'nome' => $nome,
            'sobrenome' => $sobrenome
        ];

        $dbConnector = new DatabaseConnector($dsn, $username, $password, $options);
        $usuario = new BDController($dbConnector, "users", "id", $id);

        $usuario->delete($data);
        if ($_SESSION['user_superuser'] >1 ){
        header("Location: ./dashboard.php");
        }else{
            header("Location: ./detail_user.php");
        }
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
