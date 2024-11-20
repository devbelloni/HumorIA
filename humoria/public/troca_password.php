<?php 

require_once ("./../controllers/BDController.php");
include_once("./../controllers/Tokenize.php");
include_once("./../dbm/DbConnector.php");
include_once("./../dbm/DBConfig.php");
require '../vendor/autoload.php';




    $dsn = DBConfig::getDSN();
    $username = DBConfig::getUsername();
    $password = DBConfig::getPassword();
    $options = DBConfig::getOptions();
    $servername = DBConfig::getServer();
    $dbname = DBConfig::getBdName();
    $token=$_GET['tk'];

    $DBConnector = new DBConnector($servername, $username, $password, $dbname);
    $conn = $DBConnector->connect();
    $sql = "SELECT * FROM users WHERE token = '$token'";
    $sql_query = $conn->query($sql) or die("Falha ao se conectar". $this->conn->error);
    $row = $sql_query->fetch_assoc();
    
    //test
    // var_dump($row);

if($row['token'] == $token){


    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_pass'])) {

        
        $hash_new_pass = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
        $token=$_POST['token'];

        // test
        // echo "<br>Nova senha hash = ".$hash_new_pass;
        // echo "<br>Token = ".$token;


        $sql = "UPDATE users SET senha = '$hash_new_pass', token = '' WHERE token= '$token'";
        $sql_query = $conn->query($sql) or die("Falha ao se conectar". $this->conn->error);

 
        echo '<script>alert("Senha alterada com sucesso."); window.location.href = "./../public/index.php";</script>';

    }

}else{
    header('Location: index.php' );
}


 
$horaAtual = time(); // time() retorna a hora atual em segundos desde a Era Unix (1 de Janeiro de 1970 00:00:00 GMT)
    if (!isset($_SESSION['user_email'])==True) {
      $tempoDecorrido = 0;
      unset($_SESSION['user_email']);
      session_write_close();

    }else{
      if (isset($_SESSION)==True) {
        $tempoDecorrido =  $horaAtual-$_SESSION['ultima_atividade'];
        if ($tempoDecorrido > 600) { 
          $tempoDecorrido = 0;
          header('location:./logout.php');
  
          }    
      }
  }
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Humor AI</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>


    <!-- Tela de Dashboard -->
    <div class="dashboard-container">
        <div class="main-content">
            <header class="header">
                <h1>Humor AI</h1>
            </header>

                <!-- Tela de Login -->
    <div class="login-container">
    
      <div class="row">
        <div class="login-box">
        <img src="https://www.ramalvirtual.com.br/uploads/pages/2020/06/logo-1592227938.png" class="logotipe card-img-top" alt="...">

            <h2>Troca senha</h2>
            <h5 class="text-center">Digite sua nova senha</h5>
            <form action="troca_password.php" method="post">
                <label for="new_pass" class="form-label">Senha nova:</label>
                <input type="password" placeholder="" name="new_pass" required>
                <input type="text" name="token" value="<?php echo $token; ?>" hidden>
                <input type="text" name="user_id" value="<?php echo $row['token']; ?>" hidden>
                <button type="submit">Entrar</button>
            </form>
          </div>
        </div>
      </div>

      <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Belloni & França 2024</div>
                            <div>
                                <!-- <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a> -->
                            </div>
                        </div>
                    </div>
                </footer>
      
    </div>
  </div>
</body>
</html>
