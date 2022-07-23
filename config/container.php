<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Factory\AppFactory;
use Medoo\Medoo;
use App\Utils\Jwt;
use Slim\Http\Factory\DecoratedResponseFactory;

return [

  "settings" => function() {
    return require __DIR__ . "/settings.php";
  },

  Dotenv::class => function () {
    return new Dotenv();
  },

  // 註冊SLIM APP
  App::class => function (ContainerInterface $container) {
    AppFactory::setContainer($container);
    return AppFactory::create();
  },

  // 找BasePath
  BasePathMiddleware::class => function (ContainerInterface $container) {
    return new BasePathMiddleware($container->get(App::class));
  },

  Medoo::class => function () {
    if($_ENV['DB_DRIVER'] == "sqlite") {
        $database = [
            'type' => $_ENV['DB_DRIVER'],
          'database' => __DIR__ . "/../" . $_ENV['DB_NAME']
        ];
    } else {
        $database = [
            'type' => $_ENV['DB_DRIVER'],
            'database' => $_ENV['DB_NAME'],
            'host' => $_ENV['DB_HOST'],
            'username' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASS'],
            'charset' => $_ENV['DB_CHARSET'],
            'option' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                //千萬不能開啟，會造成ACID失敗
                // PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false
            ]
        ];
    }
    return new Medoo($database);
  },

  // JWT
  Jwt::class => function () {
    return new Jwt($_ENV["JWT_ISSUER"], (int)$_ENV["JWT_LIFETIME"], $_ENV["JWT_PRIVATE_KEY"], $_ENV["JWT_PUBLIC_KEY"]);
  },

  // Middleware擴充用
  DecoratedResponseFactory::class => function (ContainerInterface $container) {
      return $container->get(App::class)->getResponseFactory();
  }

];
