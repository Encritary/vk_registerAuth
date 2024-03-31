<?php

declare(strict_types=1);

namespace encritary\registerAuth\response;

use encritary\registerAuth\response\http\HttpCode;
use encritary\registerAuth\view\View;
use function http_response_code;

class Response{

	public function __construct(
		public readonly View $view,
		public readonly HttpCode $httpCode = HttpCode::Ok
	){}

	public function echo() : void{
		http_response_code($this->httpCode->value);
		foreach($this->view->getHeaders() as $headerName => $val){
			header($headerName . ': ' . $val);
		}
		echo $this->view->encode();
	}
}