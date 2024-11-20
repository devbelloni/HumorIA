<?php



class TokenHandler {
    private $token;

    public function __construct($username, $password, $token_url) {
        // Dados para enviar no corpo da requisição POST
 
       
        $data = [
            'username' => $username,
            'password' => $password
        ];

        // Inicializa a sessão cURL
        $ch = curl_init($token_url);

        // Configura as opções da requisição POST
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
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
        // Verifica se o token foi recebido
        if(isset($response_data['access'])) {
            $this->token = $response_data['access'];
        } else {        echo("OK");

            throw new Exception('Erro ao obter o token: ' . json_encode($response_data));
        }

    }

    public function getToken() {
        return $this->token;
    }
}

?>
