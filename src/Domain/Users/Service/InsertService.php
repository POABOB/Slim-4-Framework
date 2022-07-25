<?php

declare(strict_types=1);

namespace App\Domain\Users\Service;

use App\Domain\Users\Repository\UsersRepository;
use App\Utils\ResponseFormat;
use App\Utils\Validation;

final class InsertService {

  /** @var UsersRepository */
  private UsersRepository $repository;

  /** @var ResponseFormat */
  private ResponseFormat $res;

  /** @var Validation */
  private Validation $v;
  /**
   *
   * @param UsersRepository $repository DB操作
   * @param ResponseFormat $res 回傳固定格式
   * @param Validation $v 表單驗證
   */
  public function __construct(UsersRepository $repository, ResponseFormat $res, Validation $v)
  {
    $this->repository = $repository;
    $this->res = $res;
    $this->v = $v;
  }

  /**
   * 插入User
   *
   * @param array $user 資料型別
   *
   * @return array Users array
   */
  public function insertUser(array $data): ResponseFormat
  {
    // Validation
    $this->v->validate(
      [
        "姓名" => (!empty($data["name"]) ? $data["name"] : ""),
        "密碼" => (!empty($data["password"]) ? $data["password"] : ""),
      ],
      [
        "姓名" => ["required", "maxLen" => 64],
        "密碼" => ["required", "maxLen" => 64],
      ]
    );

    // Invalid
    if($this->v->error()) {
      return $this->res->format(401, $this->v->error(),"提交格式有誤!");
    }

    $data["password"] = md5($data["password"]);

    $this->repository->insertUser($data);
    return $this->res->format(200, "Success");
  }
}