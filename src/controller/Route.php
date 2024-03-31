<?php

declare(strict_types=1);

namespace encritary\registerAuth\controller;

use Attribute;

#[Attribute]
class Route{

	public readonly ?string $method;

	public function __construct(?string $method = null){
		$this->method = $method;
	}
}