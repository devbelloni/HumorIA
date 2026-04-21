<?php

// Garante que a sessão esteja ativa para pegar o empresa_id
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir as classes
require("./../dbm/DatabaseConnector.php");
require("./../dbm/DBConfig.php"); // ESSENCIAL incluir para ler o .env
require("./../controllers/BDController.php");
require("./../controllers/TokenHandler.php");
require("./../controllers/ApiHandler.php");

// Captura de datas da URL
$data_inicio = $_GET['data_inicio'] ?? null;
$data_fim = $_GET['data_fim'] ?? null;

if (isset($_SESSION['user_email'])) {
  
    // MUDANÇA CRUCIAL: Pegando dados dinâmicos do DBConfig (que lê do Docker/ENV)
    $dsn      = DBConfig::getDSN();
    $username = DBConfig::getUsername();
    $password = DBConfig::getPassword();
    $options  = DBConfig::getOptions();

    // O DatabaseConnector agora usará o host 'db' definido no Compose
    $dbConnector = new DatabaseConnector($dsn, $username, $password, $options);
    $files = new BDController($dbConnector, "files", "id_empresa", $_SESSION['empresa_id']);  

    $result = $files->getter_words($data_inicio, $data_fim);

    $palavras = [];           
    foreach ($result as $row) {
        $palavras[] = $row["word"];
    }

    // Lógica de contagem para o WordCloud2.js
    $frequencia = array_count_values($palavras);
    $dados_json = json_encode($frequencia);

} else {
    session_destroy();
    header("Location:./index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.0.6/wordcloud2.min.js"></script>
</head>
<body>
    <div id="nuvem" style="width: 800px; height: 600px; border: 1px solid #ddd;"></div>

    <script>
        // Receber os dados do PHP
        var dados = <?php echo $dados_json ?? '{}'; ?>;

        // Converter os dados em um array de palavras e frequências
        var lista_palavras = [];
        for (var palavra in dados) {
            // Aumentamos o peso multiplicando pela frequência
            lista_palavras.push([palavra, dados[palavra] * 5]); 
        }

        // Criar a nuvem de palavras
        WordCloud(document.getElementById('nuvem'), {
            list: lista_palavras,
            gridSize: 8,
            weightFactor: 2,
            fontFamily: 'Google Sans, Arial, sans-serif',
            color: 'random-dark',
            rotateRatio: 0.5,
            backgroundColor: '#f7f7f7'
        });
    </script>
</body>
</html>