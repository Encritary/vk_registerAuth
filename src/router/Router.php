<?php

declare(strict_types=1);

namespace encritary\registerAuth\router;

use encritary\registerAuth\controller\ControllerFactory;
use encritary\registerAuth\controller\exception\MethodNotFoundException;
use encritary\registerAuth\exception\AppException;
use encritary\registerAuth\exception\ErrorCode;
use encritary\registerAuth\request\Request;
use encritary\registerAuth\response\ErrorResponse;
use encritary\registerAuth\response\http\HttpCode;
use encritary\registerAuth\response\Response;
use Throwable;
use function count;
use function error_log;
use function explode;
use function get_class;
use function parse_url;
use function str_contains;
use const PHP_URL_PATH;

class Router{

	public function execute(Request $request) : Response{
		try{
			$path = explode("/", parse_url($request->uri, PHP_URL_PATH), 2);
			if(count($path) < 2 || $path[1] === ""){
				throw new AppException("No method specified", HttpCode::NotFound, ErrorCode::MethodNotSpecified->value);
			}

			if(!str_contains($path[1], '.')){
				throw new MethodNotFoundException($path[1]);
			}

			$controllerFactory = ControllerFactory::getInstance();

			[$controllerName, $method] = explode(".", $path[1], 2);
			$controller = $controllerFactory->get($controllerName);

			return $controller->execute($method, $request);
		}catch(Throwable $e){
			error_log(get_class($e) . ": " . $e->getMessage());
			error_log($e->getTraceAsString());
			return ErrorResponse::fromThrowable($e);
		}
	}
}