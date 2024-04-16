<?php

class BaseModel {
  
  // protected $dsn = 'sqlite::memory:';
  protected $dsn = 'sqlite:/home/b/spc-175/api/dev.sqlite';
  protected $username = 'your_username';
  protected $password = 'your_password';
  protected $pdo;

  function __construct() {
    $this->pdo = new PDO($this->dsn);
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
}
