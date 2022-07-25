<?php

declare(strict_types=1);

namespace App\Domain\Auth\Service;

use App\Domain\Auth\Repository\AuthRepository;
use App\Utils\ResponseFormat;
use App\Utils\Validation;
use App\Utils\Jwt;

final class LoginService {

  /** @var AuthRepository */
  private AuthRepository $repository;

  /** @var ResponseFormat */
  private ResponseFormat $res;

  /** @var Validation */
  private Validation $v;

  /** @var Jwt */
  private Jwt $jwt;

  /**
   * @param AuthRepository $repository DB操作
   * @param ResponseFormat response
   * @param Validation 表單驗證
   */
  public function __construct(AuthRepository $repository, ResponseFormat $res, Validation $v, Jwt $jwt)
  {
    $this->repository = $repository;
    $this->res = $res;
    $this->v = $v;
    $this->jwt = $jwt;
  }

  /**
   * Login
   *
   * @param array $name, $password
   *
   * @return ResponseFormat 
   */
  public function login(array $data): ResponseFormat
  {
    $this->v->validate(
      [
        "帳號" => (!empty($data["name"]) ? $data["name"] : ""),
        "密碼" => (!empty($data["password"]) ? $data["password"] : "")
      ],
      [
        "帳號" => ["required", "maxLen" => 64],
        "密碼" => ["required", "maxLen" => 64]
      ]
    );

    // Invalid
    if($this->v->error()) {
      return $this->res->format(401, $this->v->error(),"提交格式有誤!");
    }

    $data["password"] = md5($data["password"]);
    
    // 查看登入
    $data = $this->repository->login("*", $data);

    if (!$data) {
      return $this->res->format(400, "帳號或密碼錯誤");
    }


    // Transform the result into a OAuh 2.0 Access Token Response
    // https://www.oauth.com/oauth2-servers/access-tokens/access-token-response/
    $token = $this->jwt->createJwt(["name" => $data[0]["name"], "role" => "admin"]);
    $lifetime = $this->jwt->getLifetime();
    $result = [
        "access_token" => $token,
        "token_type" => "Bearer",
        "expires_in" => $lifetime,
    ];
    return $this->res->format(200, $result);
  }
}