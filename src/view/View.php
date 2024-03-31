<?php

declare(strict_types=1);

namespace encritary\registerAuth\view;

interface View{

	/**
	 * @return string[]
	 */
	public function getHeaders() : array;

	public function encode() : string;
}