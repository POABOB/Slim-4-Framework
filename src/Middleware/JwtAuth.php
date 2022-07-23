<?php

declare(strict_types=1);
 
namespace App\Middleware;
 
use App\Utils\Jwt;
use App\Utils\ResponseFormat;
// use Psr\Http\Message\ResponseFactoryInterface;

use Slim\Http\Factory\DecoratedResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
 
/**
 * JWT middleware.
 */
final class JwtAuth implements MiddlewareInterface
{
    /** @var Jwt JWT authorizer */
    private Jwt $jwt;
 
    /**
     *  @var DecoratedResponseFactory
     *  The response factory 
     */
    private DecoratedResponseFactory $responseFactory;

    /**
     *  @var ResponseFormat
     */
    private ResponseFormat $response;
 
    public function __construct(Jwt $jwt, DecoratedResponseFactory $responseFactory, ResponseFormat $response)
    {
        $this->jwt = $jwt;
        $this->responseFactory = $responseFactory;
        $this->response = $response;
    }
 
    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface The response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authorization = explode(" ", (string)$request->getHeaderLine("Authorization"));
        $token = $authorization[1] ?? "";

        // 判斷有無TOKEN並驗證
        if (!$token || !$this->jwt->validateToken($token)) {
            return $this->responseFactory->createResponse()
                ->withJson($this->response->format(403, "請先登入"), 200, JSON_UNESCAPED_UNICODE);
        }
 
        // Append valid token
        $parsedToken = $this->jwt->createParsedToken($token);
        $request = $request->withAttribute("token", $parsedToken);
 
        // Append the info as request attribute
        $request = $request->withAttribute("info", $parsedToken->claims()->get("info"));
 
        return $handler->handle($request);
    }
}
