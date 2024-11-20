<?php
class CreateController {
    private $dbConnector;
    private $tableName;

    public function __construct(DBConnector $dbConnector, $tableName) {
        $this->dbConnector = $dbConnector->connect();
        $this->tableName = $tableName;
    }

    public function registerUser(array $user) {
        // Certifique-se de que a sessão foi iniciada e que a variável `$_SESSION['empresa_id']` está definida

        if (!isset($_SESSION['empresa_id'])) {
            throw new Exception('Empresa ID não está definida na sessão.');
        }
        // Hash the password using a secure hashing algorithm
        $hashedPassword = password_hash($user['senha'], PASSWORD_DEFAULT);

        // Prepare SQL query to insert user data into the database
        $sql = "INSERT INTO {$this->tableName} (id_empresa, nome, sobrenome, telefone, email, senha, superuser) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->dbConnector->prepare($sql);

        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->dbConnector->error));
        }

        if ($_SESSION['user_superuser'] == 2) {
            $stmt->bind_param(
            "isssssi",
            $user['empresa'],
            $user['nome'],
            $user['sobrenome'],
            $user['telefone'],
            $user['email'],
            $hashedPassword,
            $user['superuser']
        );

        }else{
            $stmt->bind_param(
                "isssssi",
                $_SESSION['empresa_id'],
                $user['nome'],
                $user['sobrenome'],
                $user['telefone'],
                $user['email'],
                $hashedPassword,
                $user['superuser']
            );
        }
            // Tente executar a declaração
        try {
            $stmt->execute();
            echo "<script>alert('Usuário registrado com sucesso.'); window.location.href = 'cadastro.php';</script>";
        } catch (Exception $e) {
            echo "User registration failed: " . $e->getMessage();
        }

        // Close the prepared statement
        $stmt->close();
    }

    public function registerEmpresa(array $empresa) {
        // Certifique-se de que a sessão foi iniciada e que a variável `$_SESSION['empresa_id']` está definida
        if (!isset($_SESSION['empresa_id'])) {
            throw new Exception('Empresa ID não está definida na sessão.');
        }

        // Prepare SQL query to insert user data into the database
        $sql = "INSERT INTO {$this->tableName} (razao, cnpj, telefone, email, endereco, cidade, estado, pais, cep) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->dbConnector->prepare($sql);
        echo "ok";

        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($this->dbConnector->error));
        }


        $stmt->bind_param(
            "sssssssss",
            $empresa['razao'],
            $empresa['cnpj'],
            $empresa['telefone'],
            $empresa['email'],
            $empresa['endereco'],
            $empresa['cidade'],
            $empresa['estado'],
            $empresa['pais'],
            $empresa['cep'],
        );

        // Tente executar a declaração
        try {
            $stmt->execute();
            echo "<script>alert('Empresa registrada com sucesso.'); window.location.href = 'cadastro_empresa.php';</script>";
        } catch (Exception $e) {
            echo "User registration failed: " . $e->getMessage();
        }

         // Tenta enviar o id da empresa para criar o diretório de arquivos de audio - inserido em 20/11/24
        try {
            $stmt->execute();
    
            // Recuperar o ID da empresa recém-inserida
            $empresaId = $this->dbConnector->insert_id;
    
    
            return $empresaId;
        } catch (Exception $e) {
            echo "Registro da empresa falhou: " . $e->getMessage();
        } finally {
            // Close the prepared statement
            $stmt->close();
        }
    }

}
?>
