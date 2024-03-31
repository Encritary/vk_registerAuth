<?php

declare(strict_types=1);

namespace encritary\registerAuth\controller\exception;

use encritary\registerAuth\exception\AppException;
use encritary\registerAuth\exception\ErrorCode;
use encritary\registerAuth\response\http\HttpCode;
use Throwable;

class ControllerNotFoundException extends AppException{

	public readonly string $controllerName;

	public function __construct(string $controllerName, HttpCode $httpCode = HttpCode::NotFound, ?Throwable $previous = null){
		$this->controllerName = $controllerName;
		parent::__construct("Controller '$controllerName' not found", $httpCode, ErrorCode::ControllerNotFound->value, $previous);
	}
}