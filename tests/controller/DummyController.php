<?php

declare(strict_types=1);

namespace encritary\registerAuthUnit\controller;

use encritary\registerAuth\controller\Controller;
use encritary\registerAuth\request\Request;
use encritary\registerAuth\response\Response;
use encritary\registerAuth\response\SuccessResponse;

class DummyController implements Controller{
	public bool $isSetUp = false;

	public function execute(string $methodName, Request $request) : Response{
		return new SuccessResponse("");
	}

	public function getName() : string{
		return "test";
	}

	public function setup() : void{
		$this->isSetUp = true;
	}
}