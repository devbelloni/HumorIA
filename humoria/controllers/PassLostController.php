<?php


require_once ("./../controllers/BDController.php");
include_once("./../controllers/Tokenize.php");
include_once("./../dbm/DbConnector.php");
include_once("./../dbm/DBConfig.php");
require '../vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;




if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email_recupera'])) {

        $dsn = DBConfig::getDSN();
        $username = DBConfig::getUsername();
        $password = DBConfig::getPassword();
        $options = DBConfig::getOptions();
        $servername = DBConfig::getServer();
        $dbname = DBConfig::getBdName();




        // DatabaseConnector($dsn, $username, $password, $options) {
        // DBConnector($servername, $username, $password, $dbname)
        $DBConnector = new DBConnector($servername, $username, $password, $dbname);

        $conn = $DBConnector->connect();
        // echo "antes do sql<br>";



        $email_recupera = $_POST['email_recupera'];
    

        // test
        // echo "$email_recupera = " . $email_recupera . "<br>";


        // BDController($dbConnector, $tableName, $column, $value) {
        // $BDController = new BDController($conn, 'users', 'email', $email_recupera);
        $sql = "SELECT * FROM users WHERE email = '$email_recupera'";
        $sql_query = $conn->query($sql) or die("Falha ao se conectar". $this->conn->error);
      
        $quantidade = $sql_query->num_rows;

        // TEST
        // echo $sql;


        if($quantidade == 1){
            // test
            // echo "<br>quantidade: $quantidade";




            $tokenize = new Tokenize();
            $token= $tokenize->token();
            
            // test
            // echo "<br>token: $token";

            $sql = "UPDATE users SET token = '$token' WHERE id= '$_SESSION[user_id]'";
            $sql_query = $conn->query($sql) or die("Falha ao se conectar". $this->conn->error);

            // cria o link de recuperação
            // $link_recupera = "http://localhost/DEVELOPMENT/HUMOR/SERVER/frontend/public/troca_password.php?tk=" . $token;
            $link_recupera = "http://humoria.ramalvirtual.com.br/public/troca_password.php?tk=" . $token;

            // test
            // echo "<br>link: $link_recupera";


            // ENVIA EMAIL            
            // Create a new PHPMailer instance
            $mail = new PHPMailer();
            
            // Tell PHPMailer to use SMTP
            $mail->isSMTP();
            
            // Enable SMTP debugging
            // SMTP::DEBUG_OFF = off (for production use)
            // SMTP::DEBUG_CLIENT = client messages
            // SMTP::DEBUG_SERVER = client and server messages
            // SMTPDebug = SMTP::DEBUG_SERVER;
            // $mail->SMTP::DEBUG_OFF
            
            // Set the hostname of the mail server
            $mail->Host = 'smtp.gmail.com';
            
            // Set the SMTP port number
            $mail->Port = 465;
            
            // Set the encryption mechanism to use
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            
            // Whether to use SMTP authentication
            $mail->SMTPAuth = true;
            
            // Username to use for SMTP authentication - use full email address for gmail
            $mail->Username = 'prof.belloni@gmail.com';
            
            // Password to use for SMTP authentication
            $mail->Password = 'hbpxppywbzvwlals';
            
            // Set who the message is to be sent from
            $mail->setFrom('prof.belloni@gmail.com', 'Contato-Humor');
            
            // Set who the message is to be sent to
            $mail->addAddress('mbelloni@alumni.usp.br', 'Belloni');
            
            // Set the subject line
            $mail->Subject = 'HumorIA - Recuperar Senha';
            
            // Read an HTML message body from an external file, convert referenced images to embedded,
            // convert HTML into a basic plain-text alternative body
            $htmlBody = file_get_contents('contents.html');

            
            // Check if the HTML body was read successfully
            if ($htmlBody) {
                $mail->msgHTML($htmlBody, __DIR__);
            } else {
                // Fallback to a default message if the file is not found
                $mail->Body = "Para inserir uma nova senha, acesse o link: $link_recupera";
                
                
            }
            
            // Set the plain text body
            $plainTextBody = isset($_POST['message']) ? $_POST['message'] : 'This is the plain text body';
            $mail->AltBody = $plainTextBody;

            
            // Send the message, check for errors
            if (!$mail->send()) {
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
                echo '<script>alert("Um link para recuperar a senha foi enviado para seu e-mail."); window.location.href = "./../public/index.php";</script>';
            }
            
            // Section 2: IMAP
            // IMAP commands require the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
            function save_mail($mail)
            {
                // You can change 'Sent Mail' to any other folder or tag
                $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail';
            
                // Tell your server to open an IMAP connection using the same username and password as you used for SMTP
                $imapStream = imap_open($path, $mail->Username, $mail->Password);
            
                $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
                imap_close($imapStream);
            
                return $result;
            }

            
        }else{
            echo "<script>alert('Usuário inexistente')</script>";

        }




}else{
    echo "<script>alert('Erro ao recuperar senha')</script>";
}









?>