<?php

namespace Helper;

use Symfony\Component\Dotenv\Dotenv;
use Medoo\Medoo;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Functional extends \Codeception\Module
{

  private Medoo $db;

  public function __construct() {
    // LOAD TEST ENV
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__.'/../../../.env.test');

    // 測試前使用SQLITE 準備Users表
    $this->db = new Medoo(['type' => $_ENV['DB_DRIVER'], 'database' => __DIR__ . "/../../../" . $_ENV['DB_NAME']]);
  }

  public function setUp() {
    
    // 在這裡建立Table
    $this->db->create("Users", [
      "id" => ["INTEGER", "NOT NULL", "PRIMARY KEY", "AUTOINCREMENT"],
      "name" => ["VARCHAR(64)", "NOT NULL"],
      "password" => ["VARCHAR(64)", "NOT NULL"],
    ]);
    // ...
  }

  public function tearDown() {
    $this->db->drop("Users");
    // ...
  }
}
