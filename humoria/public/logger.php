<?php
ob_start(); // Inicia o buffer de saída

require_once ("./../controllers/LoginController.php");
include_once("./../dbm/DbConnector.php");
include_once("./../dbm/DBConfig.php");

// Verifique se as chaves existem no POST antes de acessar para evitar Warnings
if (isset($_POST['email'], $_POST['senha']) && $_POST['email'] !== "" && $_POST['senha'] !== "") {

    try {
        $username = DBConfig::getUsername();
        $password = DBConfig::getPassword();
        $servername = DBConfig::getServer();
        $dbname = DBConfig::getBdName();
       
        $dbConnector = new DBConnector($servername, $username, $password, $dbname);
        $conn = $dbConnector->connect();

        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $loginController = new LoginController($conn, $email, $senha);
        $resultado = $loginController->login();

        // Se o login falhar por credenciais, o Controller já deve tratar.
        // Se chegar aqui e não redirecionou, algo deu errado no fluxo.

    } catch (Exception $e) {
        // Agora sim a variável $e existe!
        echo "<script>alert('Erro no sistema: " . addslashes($e->getMessage()) . "'); window.location.href = 'index.php';</script>";
    } catch (Error $e) {
        // Captura erros fatais do PHP 7+ (como o que aconteceu agora)
        echo "<script>alert('Erro fatal: " . addslashes($e->getMessage()) . "'); window.location.href = 'index.php';</script>";
    }
} else {
    header('Location: index.php');
    exit;
}
?>