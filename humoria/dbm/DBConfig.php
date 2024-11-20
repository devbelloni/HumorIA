<?php

class DBConfig
{
    private static $servername = "127.0.0.1:3306"; // Endereço do servidor MySQL
    private static $username = "root"; // Nome de usuário do MySQL
    // private static $password = ""; // Senha do MySQL
    private static $password = "2aGHQPcW2#wxrC@e";
    private static $dbname = "humorai"; // Nome do banco de dados
    private static $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Modo de erro: exceções
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Modo de busca padrão: array associativo
    ];

    // Método estático para obter o Servidor para a conexão
    public static function getServer()
    {
        return self::$servername;
    }

    // Método estático para obter o nome do banco de dados para a conexão
    public static function getBdName()
    {
        return self::$dbname;
    }
        // Método estático para obter a DSN (Data Source Name) para a conexão
    public static function getDSN()
    {
        return "mysql:host=" . self::$servername . ";dbname=" . self::$dbname;
    }

    // Método estático para obter as opções de configuração para a conexão
    public static function getOptions()
    {
        return self::$options;
    }

    // Método estático para obter o nome de usuário do MySQL
    public static function getUsername()
    {
        return self::$username;
    }

    // Método estático para obter a senha do MySQL
    public static function getPassword()
    {
        return self::$password;
    }
}
?>


