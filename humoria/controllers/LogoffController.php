<?php
// Remova o include do DbConnector, ele não é necessário aqui e está causando o erro
class LogoffController {

    public function logout() {
        // Verifica se a sessão já não foi iniciada para evitar o Warning
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Limpa todas as variáveis de sessão
        $_SESSION = array();

        // Destrói a sessão
        session_destroy();
        
        // Redireciona
        header("Location: ../public/index.php");
        exit;
    }
}