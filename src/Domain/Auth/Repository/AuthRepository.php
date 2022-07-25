<?php

declare(strict_types=1);

namespace App\Domain\Auth\Repository;

use UnexpectedValueException;
use Medoo\Medoo;

class AuthRepository {
    
  /** @var Medoo $DB 連線 */
  private Medoo $db;

  /**
   *
   * @param Medoo $DB 連線
   */
  public function __construct(Medoo $db)
  {
    $this->db = $db;
  }

  /**
   * 登入
   *
   * @param array|string 欄位
   * @param array|int WHERE條件
   * @param string 表名
   * 
   * @return array
   */
  public function login(array | string $params = "*", array | int $where = 1, string $table = "Users"): array
  {
    try {
      return $this->db->select($table, $params, $where);
    } catch (PDOException $e) {
      throw new UnexpectedValueException($e->getMessage());
    }
  }
}