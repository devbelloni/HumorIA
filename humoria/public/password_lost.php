<?php  

  
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

    <!-- Tela de Recuperação de Password -->
    <div class="dashboard-container">
        <div class="main-content">
            <header class="header">
                <h1>Humor AI</h1>
            </header>

               
            <div class="login-container">
                <div class="row">
                    <div class="login-box">

                        <div class="card shadow-lg border-0 rounded-lg mt-5">
                            <div class="card-header"><h3 class="text-center font-weight-light my-4">Password</h3></div>
                                <h5 class="text-center font-weight-light my-4"">Atualize ou recupere sua senha!</h5>
                                <div class="card-body">

                                    <form action="./../controllers/PassLostController.php" method="post">
                                        <label for="email">Insira seu e-mail cadastrado</label>
                                        <input type="text" placeholder="xxxxxxxxx@xxx.xx" name="email_recupera" required>
                                        <button type="submit">Requerer nova senha</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class= "row footer" >
                    <footer class="py-4 bg-light mt-auto">
                        <div class="container-fluid px-4">
                            <div class="d-flex align-items-center justify-content-between small">
                                <div class="text-muted">Copyright &copy; Your Website 2023</div>
                                <div>
                                    <a href="#">Privacy Policy</a>
                                    &middot;
                                    <a href="#">Terms &amp; Conditions</a>
                                </div>
                            </div>
                        </div>
                    </footer>

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>





