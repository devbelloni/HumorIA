<?php
session_start();

class BDController {
    private $dbConnector;
    private $tableName;
    private $column;
    private $value;

    public function __construct($dbConnector, $tableName, $column, $value) {
        $this->dbConnector = $dbConnector->connect();
        $this->tableName = $tableName;
        $this->column = $column;
        $this->value = $value;
        // test
        // echo "Valor recebido no getter: ".$this->value . "<br>";
        // echo "Tabela: ".$this->tableName . "<br>";
        // echo "Coluna: ".$this->column . "<br>";
    }

    public function getter() {
        //test
        // echo "entrou no getter<br>";

        $sql = "SELECT * FROM {$this->tableName} WHERE {$this->column} = :value";



        $stmt = $this->dbConnector->prepare($sql);

                // test
                // echo $sql;



        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars($this->dbConnector->error));
        }

        $stmt->bindParam(':value', $this->value, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Select failed: " . $e->getMessage());
        }
    }

    public function getter_all_empresas($permissao) {

        if($permissao ==2) {
    
                
            $sql = "SELECT * FROM {$this->tableName} WHERE 1;";
            $stmt = $this->dbConnector->prepare($sql);

            
            if ($stmt === false) {
                throw new Exception('Prepare failed: ' . htmlspecialchars($this->dbConnector->error));
            }

            try {
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                throw new Exception("Select failed: " . $e->getMessage());
            }
        }
    }

    public function getter_all($joined) {
        // $sql = "SELECT * FROM {$this->tableName}";

        $sql = "SELECT 
                users.id AS user_id, 
                users.nome AS user_nome, 
                users.sobrenome AS user_sobrenome, 
                users.telefone AS user_telefone, 
                users.email AS user_email, 
                users.senha AS user_senha, 
                users.superuser AS 
                user_superuser, 
                users.data_criacao AS user_data_criacao, 
                users.data_modificacao AS user_data_modificacao, 
                $joined.id AS empresa_id, 
                $joined.razao AS razao, 
                $joined.cnpj AS empresa_cnpj, 
                $joined.telefone AS empresa_telefone, 
                $joined.email AS empresa_email, 
                $joined.data_criacao AS empresa_data_criacao,
                $joined.data_modificacao AS empresa_data_modificacao 
                FROM users
                JOIN empresas 
                ON users.id_empresa = $joined.id
                ORDER BY $joined.razao DESC;";

        // echo $sql;

        $stmt = $this->dbConnector->prepare($sql);

        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars($this->dbConnector->error));
        }

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Select failed: " . $e->getMessage());
        }
    }

    public function getter_all_vader() {
        // $sql = "SELECT * FROM {$this->tableName}";

        $sql = "SELECT 
                    ev.id AS emotion_vader_id, 
                    ev.emotion_vader AS emotion_vader, 
                    ev.data_criacao AS emotion_vader_data_criacao, 
                    ev.data_modificacao AS emotion_vader_data_modificacao, 
                    f.filename AS file_filename, 
                    f.data AS file_data, 
                    f.data_criacao AS file_data_criacao, 
                    f.data_modificacao AS file_data_modificacao, 
                    e.razao AS razao, 
                    e.cnpj AS empresa_cnpj, 
                    e.data_criacao AS empresa_data_criacao,
                    e.data_modificacao AS empresa_data_modificacao 
                FROM 
                    emotion_vader ev
                JOIN 
                    files f ON ev.id_file = f.id
                JOIN 
                    empresas e ON f.id_empresa = e.id
                ORDER BY 
                    ev.emotion_vader DESC;
                ";

        // echo $sql;

        $stmt = $this->dbConnector->prepare($sql);

        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars($this->dbConnector->error));
        }

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Select failed: " . $e->getMessage());
        }
    }

    public function getter_emotions($data1 = null, $data2 = null) {
        if ($data1 === null) {
            // Se param1 não for fornecido, defina-o como o dia de ontem
            $data1 = date('Y-m-d', strtotime('-1 day'));
        }
    
        if ($data2 === null) {
            // Se param2 não for fornecido, defina-o como o dia de hoje
            $data2 = date('Y-m-d');
        }

        // busca os files
        // $sql = "SELECT * FROM files, emotion_vader, emotion_gemini WHERE id_empresa = " . $this->value . " AND files.data_criacao BETWEEN '" . $data1 . "' AND '" . $data2 . "'";
        $sql = "SELECT * FROM files JOIN emotion_vader ON files.id = emotion_vader.id_file JOIN emotion_gemini ON files.id = emotion_gemini.id_file WHERE id_empresa = " . $this->value . " AND files.data BETWEEN '" . $data1 . "' AND '" . $data2 . "' ORDER BY files.data DESC";


        $stmt= $this->dbConnector->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars($this->dbConnector->error));
        }
              
        try {       
            $stmt->execute();
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
        
        } catch (Exception $e) {
            throw new Exception("Select failed: " . $e->getMessage());
        }

        return $result;

    }
    
    public function getter_all_files_emotions() {
 
        // busca os files
        // $sql = "SELECT * FROM files, emotion_vader, emotion_gemini WHERE id_empresa = " . $this->value . " AND files.data_criacao BETWEEN '" . $data1 . "' AND '" . $data2 . "'";
        $sql = "SELECT * FROM files JOIN emotion_vader ON files.id = emotion_vader.id_file JOIN emotion_gemini ON files.id = emotion_gemini.id_file WHERE 1 ORDER BY files.data DESC";


        $stmt= $this->dbConnector->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars($this->dbConnector->error));
        }
              
        try {       
            $stmt->execute();
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
        
        } catch (Exception $e) {
            throw new Exception("Select failed: " . $e->getMessage());
        }

        return $result;

    }

    public function getter_words($data1 = null, $data2 = null) {


        if ($data1 === null) {
            // Se param1 não for fornecido, defina-o como o dia de ontem
            $data1 = date('Y-m-d', strtotime('-1 day'));
        }
    
        if ($data2 === null) {
            // Se param2 não for fornecido, defina-o como o dia de hoje
            $data2 = date('Y-m-d');
        }
        // echo $data1;
        // echo $data2;    
    
        // busca os files
        // $sql = "SELECT * FROM files, emotion_vader, emotion_gemini WHERE id_empresa = " . $this->value . " AND files.data_criacao BETWEEN '" . $data1 . "' AND '" . $data2 . "'";
        $sql = "SELECT * FROM files JOIN words ON files.id = words.id_file WHERE id_empresa = " . $this->value . " AND files.data BETWEEN '" . $data1 . "' AND '" . $data2 . "'";
        $stmt= $this->dbConnector->prepare($sql);

        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars($this->dbConnector->error));
        }
              
        try {       
            $stmt->execute();
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
            // echo $sql;


        
        } catch (Exception $e) {
            throw new Exception("Select failed: " . $e->getMessage());
        }


        return $result;

    }

    public function setter($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO {$this->tableName} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->dbConnector->prepare($sql);

        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars($this->dbConnector->error));
        }

        foreach ($data as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }

        try {
            $stmt->execute();
            return $this->dbConnector->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Insert failed: " . $e->getMessage());
        }
    }

    public function update($data) {
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "{$key} = '{$value}'";
        }
    
        // test
        // var_dump($data);


        $setString = implode(", ", $sets);

        $sql = "UPDATE {$this->tableName} SET {$setString} WHERE id= '". $this->value . "'";

        // test
        // echo $sql;

        $stmt = $this->dbConnector->prepare($sql);

        // test
        // var_dump($stmt);

        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars($this->dbConnector->error));
        }

       
        try {
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Exception $e) {
            throw new Exception("Update failed: " . $e->getMessage());
        }
    }

    public function delete() {
        $sql = "DELETE FROM {$this->tableName} WHERE {$this->column} = :value";
        $stmt = $this->dbConnector->prepare($sql);

        echo $sql;
        echo $this->value;
        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars($this->dbConnector->error));
        }

        $stmt->bindParam(':value', $this->value, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Exception $e) {
            throw new Exception("Delete failed: " . $e->getMessage());
        }
    }

    public function delete_empresas() {
        $sql = "DELETE FROM {$this->tableName} WHERE {$this->column} = :value";
        $stmt = $this->dbConnector->prepare($sql);

        echo $sql;
        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars($this->dbConnector->error));
        }

        $stmt->bindParam(':value', $this->value, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Exception $e) {
            throw new Exception("Delete failed: " . $e->getMessage());
        }
    }
}
?>
