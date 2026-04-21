<?php

require("./../dbm/DatabaseConnector.php");
require("./../controllers/BDController.php");
require("./../controllers/TokenHandler.php");
require("./../controllers/ApiHandler.php");

session_start();

if (isset($_GET['data_inicio'])) {
    $data_inicio = $_GET['data_inicio'];
}

if (isset($_GET['data_fim'])) {
    $data_fim = $_GET['data_fim'];
}

if (!isset($_SESSION['user_email'])) {
    $_SESSION['user_email'] = null;
    session_destroy();
    header("Location:./index.php");
    exit;
}

include_once("./../dbm/DBConfig.php");

$dsn     = DBConfig::getDSN();
$db_user = DBConfig::getUsername();
$db_pass = DBConfig::getPassword();
$options = DBConfig::getOptions();

$dbConnector = new DatabaseConnector($dsn, $db_user, $db_pass, $options);
$files = new BDController($dbConnector, "files", "id_empresa", $_SESSION['empresa_id']);

$result = $files->getter_words($data_inicio ?? null, $data_fim ?? null);

$palavras = [];
foreach ($result as $row) {
    $palavras[] = $row["word"];
}

$data = ['word' => $palavras];

try {
    $django_url   = getenv('DJANGO_URL')      ?: 'http://humorai-api:8000';
    $django_user  = getenv('DJANGO_USER')     ?: 'belloni';
    $django_pass  = getenv('DJANGO_PASSWORD') ?: '';
    $token_url    = $django_url . '/api/token/';
    $endpoint_url = $django_url . '/api/wordcloud/';

    $tokenHandler = new TokenHandler($django_user, $django_pass, $token_url);
    $token = $tokenHandler->getToken();

    $apiHandler = new ApiHandler($endpoint_url, $token);
    $response = $apiHandler->sendData($data);

    header('Content-Type: image/png');
    echo $response;

} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
