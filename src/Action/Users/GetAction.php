<?php

declare(strict_types=1);

namespace App\Action\Users;

use Slim\Http\Response;
// 發現使用Slim\Http\Request常常會報錯，所以使用官方的Request當作請求
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Users\Service\GetService;
/**
 * @OA\Get(
 *      path="/api/user", 
 *      tags={"Users"},
 *      summary="獲取Users",
 *      @OA\Response(
 *          response="200", 
 *          description="獲取Users",
 *          @OA\JsonContent(type="object",
 *              @OA\Property(property="code", type="integer", example=200),
 *              @OA\Property(property="data", type="array",
 *                    @OA\Items(type="object",
 *                      @OA\Property(property="id", type="int(11)", example="1"),
 *                      @OA\Property(property="name", type="string(64)", example="Bob"),
 *                    ), 
 *              ),
 *              @OA\Property(property="message", example="Success"),
 *          ),
 *      ),
 * )
 */
final class GetAction
{
  /** @var GetService The user service */
  private GetService $service;


  public function __construct(GetService $service)
  {
    $this->service = $service;
  }

  public function __invoke(Request $req, Response $res): Response
  {
      // 使用Service
      $return = $this->service->getUsers();
      return $res->withJson($return, 200, JSON_UNESCAPED_UNICODE);
  }
}