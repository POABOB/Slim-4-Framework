<?php

declare(strict_types=1);

namespace App\Action\Auth;

use Slim\Http\Response;
// 發現使用Slim\Http\Request常常會報錯，所以使用官方的Request當作請求
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Auth\Service\LogoutService;

/**
 * @OA\Get(
 *      path="/api/logout", 
 *      tags={"登入登出"},
 *      summary="登出",
 *      @OA\Response(
 *          response="200", 
 *          description="登入",
 *          @OA\JsonContent(type="object",
 *              @OA\Property(property="code", type="integer", example=200),
 *              @OA\Property(property="data", example="null"),
 *              @OA\Property(property="message", example="Success"),
 *          ),
 *      ),
 * )
 */
final class LogoutAction
{
    /** @var LogoutService The Logout service */
    private LogoutService $service;
 
    public function __construct(LogoutService $service)
    {
      $this->service = $service;
    }
 
    public function __invoke(Request $req, Response $res): Response
    {
      $return = $this->service->logout();
      return $res->withJson($return, 200, JSON_UNESCAPED_UNICODE);
    }
}