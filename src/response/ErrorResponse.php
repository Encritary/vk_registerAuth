<?php

declare(strict_types=1);

namespace encritary\registerAuth\response;

use encritary\registerAuth\exception\AppException;
use encritary\registerAuth\exception\ErrorCode;
use encritary\registerAuth\response\http\HttpCode;
use encritary\registerAuth\view\JsonView;
use Throwable;
use function get_class;

class ErrorResponse extends Response{

	public static function fromThrowable(Throwable $e) : self{
		if($e instanceof AppException){
			return new self($e->getMessage(), $e->getCode(), $e->httpCode);
		}
		return new self(get_class($e) . " [{$e->getCode()}]: {$e->getMessage()}", ErrorCode::InternalError->value, HttpCode::InternalServerError);
	}

	public function __construct(string $message, int $code, HttpCode $httpCode = HttpCode::BadRequest){
		parent::__construct(new JsonView([
			'status' => 'error', 'error' => [
				'code' => $code,
				'message' => $message
			]
		]), $httpCode);
	}
}