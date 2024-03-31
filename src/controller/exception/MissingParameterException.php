<?php

declare(strict_types=1);

namespace encritary\registerAuth\controller\exception;

use encritary\registerAuth\exception\AppException;
use encritary\registerAuth\exception\ErrorCode;
use encritary\registerAuth\response\http\HttpCode;
use Throwable;

class MissingParameterException extends AppException{

	public readonly string $parameter;

	public function __construct(string $parameter, HttpCode $httpCode = HttpCode::BadRequest, ?Throwable $previous = null){
		$this->parameter = $parameter;
		parent::__construct("Parameter $parameter is required", $httpCode, ErrorCode::MissingParameter->value, $previous);
	}
}