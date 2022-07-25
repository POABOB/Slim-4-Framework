<?php

declare(strict_types=1);

namespace App\Action\Doc;

use Slim\Http\Response;
// 發現使用Slim\Http\Request常常會報錯，所以使用官方的Request當作請求
use Psr\Http\Message\ServerRequestInterface as Request;

final class UIAction
{
  public function __invoke(Request $request, Response $response): Response
  {
    $file = ($_ENV["MODE"] !== "stage" ? "index.php" : "index.{$_ENV["MODE"]}.php");
    require __DIR__ . "/../../../public/swagger-ui/{$file}";
    return $response;
  }
}