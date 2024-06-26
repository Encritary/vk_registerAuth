<?php

declare(strict_types=1);

namespace encritary\registerAuth\controller;

use encritary\registerAuth\controller\exception\MethodNotFoundException;
use encritary\registerAuth\request\Request;
use encritary\registerAuth\response\Response;
use ReflectionObject;
use UnexpectedValueException;

abstract class AttributedController implements Controller{

	/** @var array<string, callable> */
	private array $methods = [];

	public function execute(string $methodName, Request $request) : Response{
		if(!isset($this->methods[$methodName])){
			throw new MethodNotFoundException("{$this->getName()}.$methodName");
		}
		return ($this->methods[$methodName])($request);
	}

	public function setup() : void{
		$obj = new ReflectionObject($this);
		foreach($obj->getMethods() as $method){
			foreach($method->getAttributes(Route::class) as $attr){
				$methodName = $attr->getArguments()['method'] ?? $method->getName();

				if(isset($this->methods[$methodName])){
					throw new UnexpectedValueException("Method $methodName declared twice");
				}

				$numParams = $method->getNumberOfRequiredParameters();

				if($numParams === 1){
					$param = $method->getParameters()[0];
					if($param->getType()->getName() !== Request::class || $param->allowsNull()){
						throw new UnexpectedValueException($obj->getName() . "::" . $method->getName() . ": Expected parameter 'request' to be non-null " . Request::class);
					}
				}elseif($numParams !== 0){
					throw new UnexpectedValueException($obj->getName() . "::" . $method->getName() . ": Expected from zero to one required parameter 'request'");
				}

				if($method->getReturnType()->getName() !== Response::class){
					throw new UnexpectedValueException($obj->getName() . "::" . $method->getName() . ": Expected return type to be " . Response::class);
				}

				$this->registerMethod($methodName, [$this, $method->getName()]);
			}
		}
	}

	protected function registerMethod(string $methodName, callable $callable) : void{
		$this->methods[$methodName] = $callable;
	}
}