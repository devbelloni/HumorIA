<?php

session_start();

require_once ("../dbm/DbConnector.php");
require_once ("./../controllers/CreateController.php");


if (isset($_POST['email']) && isset($_POST['senha'])) {
    // Database connection details
    
    include_once("./../dbm/DBConfig.php");

    $dsn = DBConfig::getDSN();
    $username = DBConfig::getUsername();
    $password = DBConfig::getPassword();
    $options = DBConfig::getOptions();
    $servername = DBConfig::getServer();
    $dbname = DBConfig::getBdName();

    // User data    
    $user = array(
        'nome' => $_POST['nome'],
        'sobrenome' => $_POST['sobrenome'],
        'telefone' => $_POST['telefone'],
        'email' => $_POST['email'],
        'senha' => $_POST['senha'],
        'empresa' => $_POST['empresa'],
        'superuser' => $_POST['superuser'],
    );



    // Create database connector object
    $dbConnector = new DBConnector($servername, $username, $password, $dbname);
    // Create UserRegister object with table name
    $userRegister = new CreateController($dbConnector, 'users');
    // Register a user
    $userRegister->registerUser($user);

    



    $horaAtual = time();
    if (!isset($_SESSION['user_email'])) {
        unset($_SESSION['user_email']);
        session_write_close();
    } else {
        $tempoDecorrido = $horaAtual - $_SESSION['ultima_atividade'];
        if ($tempoDecorrido > 600) {
            header('location:./logout.php');
        }
    }
}
?>



