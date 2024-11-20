<?php


require_once ("./../controllers/LoginController.php");
include_once("./../dbm/DbConnector.php");
include_once("./../dbm/DBConfig.php");


if ($_POST['email'] != "" && $_POST['senha'] != "") {


    $dsn = DBConfig::getDSN();
    $username = DBConfig::getUsername();
    $password = DBConfig::getPassword();
    $options = DBConfig::getOptions();
    $servername = DBConfig::getServer();
    $dbname = DBConfig::getBdName();
   

    $dbConnector = new DBConnector($servername, $username, $password, $dbname);
    $conn = $dbConnector->connect();


    $email = $conn->real_escape_string($_POST['email']);
    $senha = $conn->real_escape_string($_POST['senha']);

    // echo "TESTE";


    $loginController = new LoginController($conn, $email, $senha);
    $resultado = $loginController->login();


    echo "<script>alert('Exceção capturada: " . $e->getMessage() . "'); window.location.href = 'index.php';</script>";


  


}

?>