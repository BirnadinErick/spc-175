<?php

class BaseModel
{
    protected string $dsn = 'mysql:';
    protected string $username = "spc";
    protected string $password = "spcmediaunit2023";
    protected PDO $pdo;

    function __construct()
    {
//        $this->dsn = "sqlite:" . $_SERVER["DOCUMENT_ROOT"] . "/api/dev.sqlite";
        $this->dsn .= "host=localhost;dbname=spc";
        $this->pdo = new PDO($this->dsn, $this->username, $this->password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
}
