<?php

// Garante que a sessão esteja ativa sem gerar erro de "Headers already sent"
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("./../dbm/DbConnector.php");
include_once("./../dbm/DBConfig.php");

class LoginController {

    private $senha;
    private $email;
    private $conn;

    public function __construct($conn, $email, $senha) {
        $this->email = $email;
        $this->conn = $conn;
        $this->senha = $senha;
    }

    public function login() {
        // Validação básica de entrada
        if (empty($this->email) || empty($this->senha)) {
            echo '<script>alert("Preencha todos os campos"); window.location.href = "index.php";</script>';
            return;
        }

        if ($this->conn) {
            $sql = "SELECT
                        users.id AS user_id,
                        users.nome AS user_nome,
                        users.sobrenome AS user_sobrenome,
                        users.telefone AS user_telefone,
                        users.email AS user_email,
                        users.senha AS user_senha,
                        users.superuser AS user_superuser,
                        users.data_criacao AS user_data_criacao,
                        users.data_modificacao AS user_data_modificacao,
                        empresas.id AS empresa_id,
                        empresas.razao AS empresa_razao,
                        empresas.cnpj AS empresa_cnpj,
                        empresas.endereco AS empresa_endereco,
                        empresas.cidade AS empresa_cidade,
                        empresas.estado AS empresa_estado,
                        empresas.pais AS empresa_pais,
                        empresas.cep AS empresa_cep,
                        empresas.telefone AS empresa_telefone,
                        empresas.email AS empresa_email,
                        empresas.data_criacao AS empresa_data_criacao,
                        empresas.data_modificacao AS empresa_data_modificacao
                    FROM
                        users
                    JOIN
                        empresas ON users.id_empresa = empresas.id
                    WHERE
                        users.email = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('s', $this->email);
            $stmt->execute();
            $sql_query = $stmt->get_result();

            if ($sql_query->num_rows == 1) {
                $usuario = $sql_query->fetch_assoc();
                
                // Limpeza preventiva de qualquer lixo de encoding no hash vindo do banco
                $hashed_password_from_db = trim($usuario['user_senha']);

                // Se o hash estiver sem o '$' inicial (problema que detectamos), corrigimos aqui em runtime
                if (substr($hashed_password_from_db, 0, 1) !== '$') {
                    $hashed_password_from_db = '$' . $hashed_password_from_db;
                }

                if (password_verify($this->senha, $hashed_password_from_db)) {
                    
                    // Define fuso horário para log de atividades
                    date_default_timezone_set('America/Sao_Paulo');
                    $horaAtual = time();

                    // Preenchimento da Sessão
                    $_SESSION['user_id'] = $usuario['user_id'];
                    $_SESSION['user_nome'] = $usuario['user_nome'];
                    $_SESSION['user_sobrenome'] = $usuario['user_sobrenome'];
                    $_SESSION['user_telefone'] = $usuario['user_telefone'];
                    $_SESSION['user_email'] = $usuario['user_email'];
                    $_SESSION["user_superuser"] = $usuario['user_superuser'];
                    $_SESSION['user_data_criacao'] = $usuario['user_data_criacao'];
                    $_SESSION['user_data_modificacao'] = $usuario['user_data_modificacao'];
                    $_SESSION['empresa_id'] = $usuario['empresa_id'];
                    $_SESSION['empresa_razao'] = $usuario['empresa_razao'];
                    $_SESSION['empresa_cnpj'] = $usuario['empresa_cnpj'];
                    $_SESSION['empresa_endereco'] = $usuario['empresa_endereco'];
                    $_SESSION['empresa_cidade'] = $usuario['empresa_cidade'];
                    $_SESSION["empresa_estado"] = $usuario['empresa_estado'];
                    $_SESSION['empresa_pais'] = $usuario['empresa_pais'];
                    $_SESSION['empresa_cep'] = $usuario['empresa_cep'];
                    $_SESSION['empresa_telefone'] = $usuario['empresa_telefone'];
                    $_SESSION['empresa_email'] = $usuario['empresa_email'];
                    $_SESSION['empresa_data_criacao'] = $usuario['empresa_data_criacao'];
                    $_SESSION['empresa_data_modificacao'] = $usuario['empresa_data_modificacao'];
                    $_SESSION['ultima_atividade'] = $horaAtual;

                    // Fecha a sessão para garantir a gravação física do arquivo antes do redirecionamento
                    session_write_close();

                    // Redirecionamento Final
                    header('Location: ../public/dashboard.php');
                    exit;

                } else {
                    echo '<script>alert("Senha incorreta"); window.location.href = "index.php";</script>';
                }
            } else {
                echo '<script>alert("Usuário não encontrado"); window.location.href = "index.php";</script>';
            }
            
            $this->conn->close();
        }
    }
}