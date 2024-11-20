<?php

// Incluir as classes
require("./../dbm/DatabaseConnector.php");
require("./../controllers/BDController.php");
require("./../controllers/TokenHandler.php");
require("./../controllers/ApiHandler.php");

// Iniciar a sessão
session_start();

if (isset($_GET['data_inicio'])) {
    $data_inicio = $_GET['data_inicio'];
}

// Verifique se o parâmetro 'sobrenome' está presente na URL
if (isset($_GET['data_fim'])) {
    $data_fim = $_GET['data_fim'];
}

if (isset($_SESSION['user_email'])) {
  
  $dsn = 'mysql:host=127.0.0.1:3306;dbname=humorai';
  $username = 'root';
  $password = '';
  $options = array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  );
  $dbConnector = new DatabaseConnector($dsn, $username, $password, $options);
  $files = new BDController($dbConnector, "files", "id_empresa", $_SESSION['empresa_id']);  

  
    // $result = $files->getter_words(); 
    $result = $files->getter_words($data_inicio, $data_fim );

 $palavras=[];           
    foreach ($result as $files) {

      $palavras[]= $files["word"];
    }

  // Consumir o endpoint da API


  $palavras=[];           
  foreach ($result as $files) {

    $palavras[]= $files["word"];

  }

  $new_data = [
    'word' => $palavras
];

// Consumir o endpoint da API
$apiHandler = new ApiHandler($endpoint_url, $token);
$words = implode(", ", $palavras);
// echo(implode(", ", $palavras));



$palavras_com_aspas = array_map(function($palavra) {
  return $palavra;
}, $palavras);

// Transformar o array de volta em string
$resultado = implode(", ", $palavras_com_aspas);

// Transformar a string em um array
$palavras = explode(", ", $resultado);

// Inserir aspas nas palavras
$palavras_com_aspas = array_map(function($palavra) {
    return $palavra;
}, $palavras);

// Montar o array associativo
$data = [
    'word' => $palavras_com_aspas
];

try {
  // Configurações
  $username = 'belloni';
  $password = '0220';
  $token_url = 'http://127.0.0.1:8000/api/token/';
  $endpoint_url = 'http://127.0.0.1:8000/api/wordcloud/';

  // Obter o token
  $tokenHandler = new TokenHandler($username, $password, $token_url);    

  $token = $tokenHandler->getToken();
 
  // Consumir o endpoint da API
  $apiHandler = new ApiHandler($endpoint_url, $token);
  $response = $apiHandler->sendData($data);
  
  header('Content-Type: image/png'); // Define o tipo de conteúdo como PNG (ou outro formato se aplicável)
  echo $response;
  

} catch (Exception $e) {
  echo 'Erro: ' . $e->getMessage();
}


 
  


// } catch (Exception $e) {
//     echo 'Erro: ' . $e->getMessage();
// }



}else{

  $_SESSION['user_email']=null;
  session_destroy();
  header("Location:./index.php");

}