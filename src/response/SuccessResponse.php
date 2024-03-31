<?php

declare(strict_types=1);

namespace encritary\registerAuth\response;

use encritary\registerAuth\response\http\HttpCode;
use encritary\registerAuth\view\JsonView;

class SuccessResponse extends Response{

	public function __construct(mixed $data, HttpCode $httpCode = HttpCode::Ok){
		parent::__construct(new JsonView([
			'status' => 'success',
			'result' => $data
		]), $httpCode);
	}
}