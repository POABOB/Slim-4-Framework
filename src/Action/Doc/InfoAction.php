<?php

declare(strict_types=1);

namespace App\Action\Doc;

use Slim\Http\Response;
// 發現使用Slim\Http\Request常常會報錯，所以使用官方的Request當作請求
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Generator;

final class InfoAction
{
  /**
   * @OA\Info(title="SLIM 4 REST API", version="1.0", description="Write down your description here...")
   * @OA\Tag(name="登入登出", description="登入登出 API")
   * @OA\Tag(name="Users", description="Users API")
   * @OA\SecurityScheme(
   *      securityScheme="Authorization",
   *      in="header",
   *      name="Authorization",
   *      type="http",
   *      scheme="bearer",
   *      bearerFormat="JWT",
   * )
   */
  public function __invoke(Request $request, Response $response): Response
  {
    error_reporting(0);
    $openapi = Generator::scan([__DIR__ . "/../"]);
    header("Content-Type: application/json");
    echo $openapi->toJson();
    return $response;
  }
}