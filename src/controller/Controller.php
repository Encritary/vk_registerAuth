<?php

declare(strict_types=1);

namespace encritary\registerAuth\controller;

use encritary\registerAuth\request\Request;
use encritary\registerAuth\response\Response;

interface Controller{

	public function execute(string $methodName, Request $request) : Response;

	public function getName() : string;

	public function setup() : void;
}