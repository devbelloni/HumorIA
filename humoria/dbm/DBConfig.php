<?php

class DBConfig
{
    private static $dbname = "humorai";
    private static $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Modo de erro: exceções
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Modo de busca padrão: array associativo
    ];

    // Método estático para obter o Servidor para a conexão (via DB_HOST / DB_PORT)
    public static function getServer()
    {
        $host = getenv('DB_HOST') ?: 'humoria-db';
        $port = getenv('DB_PORT') ?: '3306';
        return "{$host}:{$port}";
    }

    // Método estático para obter o nome do banco de dados para a conexão
    public static function getBdName()
    {
        return getenv('DB_NAME') ?: self::$dbname;
    }

    // Método estático para obter a DSN (Data Source Name) para a conexão
    public static function getDSN()
    {
        return "mysql:host=" . self::getServer() . ";dbname=" . self::getBdName();
    }

    // Método estático para obter as opções de configuração para a conexão
    public static function getOptions()
    {
        return self::$options;
    }

    // Método estático para obter o nome de usuário do MySQL (via DB_USER)
    public static function getUsername()
    {
        return getenv('DB_USER') ?: 'root';
    }

    // Método estático para obter a senha do MySQL (via variável de ambiente DB_PASSWORD)
    public static function getPassword()
    {
        return getenv('DB_PASSWORD');
    }
}
?>


