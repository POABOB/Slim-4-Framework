<?php

declare(strict_types=1);

namespace App\Utils;

class ResponseFormat
{
  public int $code;
  public mixed $data;
  public mixed $message;
  
  public function format(int $status = 200, mixed $data = null, mixed $message = null): ResponseFormat
  {
    // RESET
    $this->reset();

    if(gettype($data) === 'string') {
      $this->message = $data;
      $data = null;
      $message = null;
    }

    if($data) {
      $this->data = $data;
    }

    if($message) {
      $this->message = $message;
    }

    $this->code = $status;
    return $this;
  }

  private function reset(): void
  {
    $this->code = 200;
    $this->data = null;
    $this->message = null;
  }
}