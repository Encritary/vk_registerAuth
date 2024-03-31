<?php

declare(strict_types=1);

namespace encritary\registerAuth\model\exception;

use encritary\registerAuth\exception\AppException;
use encritary\registerAuth\exception\ErrorCode;
use encritary\registerAuth\response\http\HttpCode;
use Throwable;

class ModelNotFoundException extends AppException{

	public function __construct(string $message = "", ?Throwable $previous = null){
		parent::__construct($message, HttpCode::NotFound, ErrorCode::ModelNotFound->value, $previous);
	}
}