<?php

declare(strict_types=1);

namespace App\Domain\Users\Repository;

use UnexpectedValueException;
use Medoo\Medoo;

class UsersRepository {
    
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
   * 獲取Users
   *
   * @param array|string 欄位
   * @param array|int WHERE條件
   * @param string 表名
   * 
   * @return array
   */
  public function getUsers(array | string $params = "*", array | int $where = 1, string $table = "Users"): array
  {
    try {
      return $this->db->select($table, $params, $where);
    } catch (PDOException $e) {
      throw new UnexpectedValueException($e->getMessage());
    }
  }

  /**
   * 插入User
   *
   * @param array 欄位
   * @param string 表名
   * 
   * @return void
   */
  public function insertUser(array $params = [], string $table = "Users"): void
  {
    try {
      $this->db->insert($table, $params);
      return;
    } catch (PDOException $e) {
      throw new UnexpectedValueException($e->getMessage());
    }
  }

  /**
   * 更新User
   *
   * @param array 欄位
   * @param array WHERE條件
   * @param string 表名
   * 
   * @return int
   */
  public function updateUser(array $params = [], array $where = [], string $table = "Users"): void
  {
    try {
      $this->db->update($table, $params, $where);
      return;
    } catch (PDOException $e) {
      throw new UnexpectedValueException($e->getMessage());
    }
  }

  /**
   * 刪除User
   *
   * @param array WHERE條件
   * @param string 表名
   * 
   * @return void
   */
  public function deleteUser(array $where = [], string $table = "Users"): void
  {
    try {
      $this->db->delete($table, $where);
      return;
    } catch (PDOException $e) {
      throw new UnexpectedValueException($e->getMessage());
    }
  }
}