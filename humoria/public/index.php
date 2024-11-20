<?php  

session_start();


  $status=[
    'home'=>'btn-outline-success',
    'contato'=>'btn-sm btn-outline-secondary',
    'detalhes'=>'btn-sm btn-outline-secondary',
    'dashboard'=>'btn-sm btn-outline-secondary',
    'pipeline'=>'btn-sm btn-outline-secondary',
    'administrador'=>'btn-sm btn-outline-secondary'
  ];

  
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

            <h2>Login</h2>
            <form action="logger.php" method="post">
                <input type="text" placeholder="Usuário" name="email" required>
                <input type="password" placeholder="Senha" name="senha" required>
                <button type="submit">Entrar</button>
            </form>
            <br>
            <!-- <a href="./password_lost.php"><button type="button" class="btn btn-warning mt-2">Perdi a senha</button></a> -->

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
