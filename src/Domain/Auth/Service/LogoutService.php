<?php

declare(strict_types=1);

namespace App\Domain\Auth\Service;

use App\Utils\ResponseFormat;

final class LogoutService {

  /** @var ResponseFormat */
  private ResponseFormat $res;

  /**
   *
   * @param ResponseFormat response
   */
  public function __construct(ResponseFormat $res)
  {
    $this->res = $res;
  }

  /**
   * Logout
   *
   * @return ResponseFormat 
   */
  public function logout(): ResponseFormat
  {
    return $this->res->format(200, "Success");
  }
}