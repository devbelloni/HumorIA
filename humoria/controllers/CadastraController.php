<?php
class CadastraController {

    private $dbConnector;
    private $tableName;

    public function __construct(DBConnector $dbConnector, $tableName) {
        $this->dbConnector = $dbConnector;
        $this->tableName = $tableName;
    }

    public function registerUser(array $user) {

        // Hash the password using a secure hashing algorithm
        $hashedPassword = password_hash($user['senha'], PASSWORD_DEFAULT);
      
        // Prepare SQL query to insert user data into the database
        $sql = "INSERT INTO $this->tableName (nome, sobrenome, telefone, email, senha) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->dbConnector->connect()->prepare($sql);
      
        // Bind parameters to the prepared statement with proper types
        $stmt->bind_param("sssss", $user['nome'], $user['sobrenome'], $user['telefone'], $user['email'], $hashedPassword);
      
        // Execute the prepared statement
        if ($stmt->execute()) {
          echo "Usuário registrado com sucesso!";
        } else {
          echo "Erro ao registrar usuário: " . $stmt->error;
        }
      
        // Close the prepared statement
        $stmt->close();
    }
}      