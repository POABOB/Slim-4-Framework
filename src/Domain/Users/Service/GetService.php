<?php

declare(strict_types=1);

namespace App\Domain\Users\Service;

use App\Domain\Users\Repository\UsersRepository;
use App\Utils\ResponseFormat;

final class GetService {

  /** @var UsersRepository */
  private UsersRepository $repository;

  /** @var ResponseFormat */
  private ResponseFormat $res;

  /**
   *
   * @param UsersRepository $repository DB操作
   */
  public function __construct(UsersRepository $repository, ResponseFormat $res)
  {
    $this->repository = $repository;
    $this->res = $res;
  }

  /**
   * 獲取Users
   *
   * @param array $user 資料型別
   *
   * @return ResponseFormat 
   */
  public function getUsers(): ResponseFormat
  {
    // 獲取Users
    $data = $this->repository->getUsers();
    return $this->res->format(200, $data);
  }
}