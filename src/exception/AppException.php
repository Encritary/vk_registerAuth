<?php

declare(strict_types=1);

namespace encritary\registerAuth\exception;

use encritary\registerAuth\response\http\HttpCode;
use RuntimeException;
use Throwable;

class AppException extends RuntimeException{

	public readonly HttpCode $httpCode;

	public function __construct(string $message = "", HttpCode $httpCode = HttpCode::BadRequest, int $code = 0, ?Throwable $previous = null){
		$this->httpCode = $httpCode;
		parent::__construct($message, $code, $previous);
	}
}