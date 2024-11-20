<?php

// Incluir as classes
include 'TokenHandler.php';
include 'ApiHandler.php';

try {
    // Configurações
    $username = 'belloni';
    $password = '0220';
    $token_url = 'http://127.0.0.1:8000/api/token/';
    $endpoint_url = 'http://127.0.0.1:8000/api/wordcloud/';

    // Obter o token
    $tokenHandler = new TokenHandler($username, $password, $token_url);    

    $token = $tokenHandler->getToken();
    // Dados para enviar no endpoint da API
    $data = [
        'word' => ["hello", "world", "hello", "django", "api", "wordcloud", "teste", "world", "world", "world"]
    ];

    // Consumir o endpoint da API
    $apiHandler = new ApiHandler($endpoint_url, $token);
    $response = $apiHandler->sendData($data);
    header('Content-Type: image/png'); // Define o tipo de conteúdo como PNG (ou outro formato se aplicável)

    echo $response;


} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}

?>





