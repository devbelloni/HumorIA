<?php
class DatabaseConnector {
    private $dsn;
    private $username;
    private $password;
    private $options;

    public function __construct($dsn, $username, $password, $options) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
    }

    public function connect() {
        return new PDO($this->dsn, $this->username, $this->password, $this->options);
    }
}
?>