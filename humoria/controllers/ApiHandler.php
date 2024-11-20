<?php

class ApiHandler {
    private $endpoint_url;
    private $token;

    public function __construct($endpoint_url, $token) {
        $this->endpoint_url = $endpoint_url;
        $this->token = $token;
    }

    public function sendData($data) {
        // Inicializa a sessão cURL
        $ch = curl_init($this->endpoint_url);

        // Configura as opções da requisição POST
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ]);
        $cacheDir = './cache/'; // Diretório onde os arquivos de cache serão armazenados

        $cacheFile = $cacheDir . md5($this->endpoint_url) . '.json'; // Nome do arquivo de cache

        // Verifica se o arquivo de cache existe e não está expirado
        if (file_exists($cacheFile) && (filemtime($cacheFile) > time() - 7200)) {
            // Utiliza o cache existente
            $response = file_get_contents($cacheFile);
        } else {
            // Chama a API para obter a resposta
            $response = file_get_contents($this->endpoint_url);

            // Armazena a resposta no arquivo de cache
            file_put_contents($cacheFile, $response);
        }

        // Processa a resposta da API
        $data = json_decode($response, true);
        
        // Executa a requisição
        $response = curl_exec($ch);

        // Verifica por erros
        if(curl_errno($ch)) {
            throw new Exception('Erro ao realizar requisição: ' . curl_error($ch));
        }

        // Fecha a sessão cURL
        curl_close($ch);

        // Decodifica a resposta JSON
        $response_data = json_decode($response, true);
        // header('Content-Type: image/png');
        return $response;
    }
}

?>
