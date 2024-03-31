<?php

declare(strict_types=1);

namespace encritary\registerAuth\controller\exception;

use encritary\registerAuth\exception\AppException;
use encritary\registerAuth\exception\ErrorCode;
use encritary\registerAuth\response\http\HttpCode;
use Throwable;

class BadParameterFormatException extends AppException{

	public function __construct(string $message = "", HttpCode $httpCode = HttpCode::BadRequest, ?Throwable $previous = null){
		parent::__construct($message, $httpCode, ErrorCode::BadParameterFormat->value, $previous);
	}
}