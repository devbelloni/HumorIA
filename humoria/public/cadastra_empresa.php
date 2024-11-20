<?php

session_start();

require_once ("../dbm/DbConnector.php");
require_once ("./../controllers/CreateController.php");


if (isset($_POST['email']) && isset($_POST['cnpj'])) {
    // Database connection details
    
    include_once("./../dbm/DBConfig.php");

    $dsn = DBConfig::getDSN();
    $username = DBConfig::getUsername();
    $password = DBConfig::getPassword();
    $options = DBConfig::getOptions();
    $servername = DBConfig::getServer();
    $dbname = DBConfig::getBdName();

    // User data    
    $empresa = array(
        'razao' => $_POST['razao'],
        'cnpj' => $_POST['cnpj'],
        'telefone' => $_POST['telefone'],
        'email' => $_POST['email'],
        'endereco' => $_POST['endereco'],
        'cidade' => $_POST['cidade'],
        'estado' => $_POST['estado'],
        'pais' => $_POST['pais'],
        'cep' => $_POST['cep'],
    );

    // Create database connector object
    $dbConnector = new DBConnector($servername, $username, $password, $dbname);
    // Create UserRegister object with table name
    $userRegister = new CreateController($dbConnector, 'empresas');
    // Register a user
    $userRegister->registerEmpresa($empresa);

    

    // Inserido em 20/11/24 - tenta criar o diretório
    try {


        // Criar um diretório com base no ID da empresa
        // $diretorio = "C:/xampp/htdocs/site/humoria/recordings (3)/{$empresaId}"; 
        $diretorio = "/var/www/humoria/recordings (3)/{$empresaId}"; 
        if (!is_dir($diretorio)) {
            if (!mkdir($diretorio, 0777, true)) {
                throw new Exception("Falha ao criar o diretório: {$diretorio}");
            }
        }
        
        echo("ENTROU");
        echo($empresaId);
        echo($diretorio);

        echo "<script>alert('Empresa registrada com sucesso. Diretório criado: {$diretorio}'); window.location.href = 'cadastro_empresa.php';</script>";

        return $empresaId;
    } catch (Exception $e) {
        echo "Registro da empresa falhou: " . $e->getMessage();
    } finally {
        // Close the prepared statement
    }


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



