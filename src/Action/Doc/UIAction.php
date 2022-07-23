<?php

declare(strict_types=1);

namespace App\Action\Doc;

final class UIAction
{
  public function __invoke()
  {
    $file = ($_ENV["MODE"] !== "stage" ? "index.php" : "index.{$_ENV["MODE"]}.php");
    require __DIR__ . "/../../../public/swagger-ui/{$file}";
    exit();
  }
}