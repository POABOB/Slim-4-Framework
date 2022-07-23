<?php

declare(strict_types=1);

namespace App\Action\Users;

use Slim\Http\Response;
// 發現使用Slim\Http\Request常常會報錯，所以使用官方的Request當作請求
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Users\Service\UpdateService;
/**
 * @OA\Patch(
 *      path="/api/user", 
 *      tags={"Users"},
 *      summary="更新Users",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  required={"id","name"},
 *                  @OA\Property(property="id", type="int(11)", example="2"),
 *                  @OA\Property(property="name", type="string(64)", example="Nick"),
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response="200", 
 *          description="獲取Users",
 *          @OA\JsonContent(type="object",
 *              @OA\Property(property="code", type="integer", example=200),
 *              @OA\Property(property="data", example="null"),
 *              @OA\Property(property="message", example="Success"),
 *          ),
 *      ),
 *      @OA\Response(response="401", description="提交格式有誤"),
 * )
 */
final class UpdateAction
{
  /** @var UpdateService The user service */
  private UpdateService $service;

  public function __construct(UpdateService $service)
  {
    $this->service = $service;
  }

  public function __invoke(Request $req, Response $res): Response
  {
      // 獲取json
      $data = (array)$req->getParsedBody();
      $return = $this->service->updateUser($data);
      return $res->withJson($return, 200, JSON_UNESCAPED_UNICODE);
  }
}