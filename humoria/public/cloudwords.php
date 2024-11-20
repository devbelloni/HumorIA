<?php

// Incluir as classes
require("./../dbm/DatabaseConnector.php");
require("./../controllers/BDController.php");
require("./../controllers/TokenHandler.php");
require("./../controllers/ApiHandler.php");


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
  $password = '2aGHQPcW2#wxrC@e';
  // $password = '';
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



  $new_data = [
    'word' => $palavras
];



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


// echo json_encode($data);

$frequencia = array_count_values($palavras_com_aspas);
$dados_json = json_encode($frequencia);


}else{

  $_SESSION['user_email']=null;
  session_destroy();
  header("Location:./index.php");

}



?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.0.6/wordcloud2.min.js"></script>
</head>
<body>
    <div id="nuvem" style="width: 800px; height: 600px; border: 1px solid #ddd;"></div>

    <script>
        // Receber os dados do PHP
        var dados = <?php echo $dados_json; ?>;

        // Converter os dados em um array de palavras e tamanhos
        var palavras = [];
        for (var palavra in dados) {
            palavras.push([palavra, dados[palavra]]);
        }

        // Criar a nuvem de palavras
        WordCloud(document.getElementById('nuvem'), {
            list: palavras,
            gridSize: Math.round(16 * window.innerWidth / 1024),
            weightFactor: 10,
            fontFamily: 'Times, serif',
            color: 'random-dark',
            rotateRatio: 0.5,
            backgroundColor: '#f7f7f7'
        });
    </script>
</body>
</html>