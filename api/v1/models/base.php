<?php

class BaseModel
{
    // protected $dsn = 'sqlite::memory:';
    protected $username = "your_username";
    protected $password = "your_password";
    protected $pdo;

    function __construct()
    {
        $this->dsn = "sqlite:" . $_SERVER["DOCUMENT_ROOT"] . "/api/dev.sqlite";
        $this->pdo = new PDO($this->dsn);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
}
