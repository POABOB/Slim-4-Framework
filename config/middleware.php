<?php

declare(strict_types=1);

use Slim\App;
use Selective\BasePath\BasePathMiddleware;

return function(App $app)
{
  // 解析JSON
  $app->addBodyParsingMiddleware();
  
  // SLIM內建路中間件
  $app->addRoutingMiddleware();

  // BASEPATH
  $app->add(BasePathMiddleware::class);

  // JWT

  // DEBUG
  $app->addErrorMiddleware(
    (bool)$_ENV["DISPLAY_ERROR_DETAILS"], 
    (bool)$_ENV["LOG_ERROR_DETAILS"], 
    (bool)$_ENV["LOG_ERRORS"]
  );
};
