<?php

declare(strict_types=1);

namespace App\Action\Auth;

use Slim\Http\Response;
// 發現使用Slim\Http\Request常常會報錯，所以使用官方的Request當作請求
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Auth\Service\LoginService;

/**
 * @OA\Post(
 *      path="/api/login", 
 *      tags={"登入登出"},
 *      summary="登入",
 *      @OA\RequestBody(
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  required={"name", "password"},
 *                  @OA\Property(property="name",         type="string(64)", example="admin"),
 *                  @OA\Property(property="password",     type="string(64)", example="admin"),
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response="200", 
 *          description="登入",
 *          @OA\JsonContent(type="object",
 *              @OA\Property(property="code",         type="integer", example=200),
 *              @OA\Property(property="message",      example="null"),
 *              @OA\Property(property="data",         type="object",
 *                      @OA\Property(property="access_token", type="string", example="<token>"),
 *                      @OA\Property(property="token_type",   type="string(64)", example="Bearer"),
 *                      @OA\Property(property="expires_in",   type="int(11)", example="86400"),

 *              ),
 *          ),
 *      ),
 *      @OA\Response(response="401",          description="提交格式有誤"),
 *      @OA\Response(response="400",          description="帳號或密碼錯誤"),
 * )
 */
final class LoginAction
{
    /**
     * 
     *
     * @var LoginService The login service 
     */
    private LoginService $service;
 
    public function __construct(LoginService $service)
    {
        $this->service = $service;
    }
 
    public function __invoke(Request $req, Response $res): Response
    {
        $data = (array)$req->getParsedBody();
        $return = $this->service->login($data);
        return $res->withJson($return, 200, JSON_UNESCAPED_UNICODE);
    }
}
