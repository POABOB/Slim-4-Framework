<?php

declare(strict_types=1);

use Slim\Http\Response;
// 發現使用Slim\Http\Request常常會報錯，所以使用官方的Request當作請求
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use Slim\App;

return function(App $app) {
  $app->options("[{routes.*}]", function(Request $req, Response $res, array $args) :Response { return $res; });
  
  $prefix = ($_ENV["MODE"] == "stage" ? "_stage" : "");
  $app->group("/api" . $prefix, function (RouteCollectorProxy $app) {

    $app->group("/doc", function (RouteCollectorProxy $app) {
      $app->get("[/]", \App\Action\Doc\UIAction::class);
      $app->get("/swagger[/]", \App\Action\Doc\InfoAction::class);
    });
    
    $app->post("/login[/]", \App\Action\Auth\LoginAction::class);
    $app->get("/logout[/]", \App\Action\Auth\LogoutAction::class);
 
    $app->group("/user", function (RouteCollectorProxy $app) {
      $app->get("[/]", \App\Action\Users\GetAction::class);
      $app->post("[/]", \App\Action\Users\InsertAction::class);
      $app->patch("[/]", \App\Action\Users\UpdateAction::class);
      $app->delete("[/]", \App\Action\Users\DeleteAction::class)->add(\App\Middleware\JwtAuth::class);
    });
 });
};
