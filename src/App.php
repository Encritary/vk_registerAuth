<?php

declare(strict_types=1);

namespace encritary\registerAuth;

use encritary\registerAuth\config\Config;
use encritary\registerAuth\controller\ControllerFactory;
use encritary\registerAuth\db\Db;
use encritary\registerAuth\key\JwtKey;
use encritary\registerAuth\request\Request;
use encritary\registerAuth\response\Response;
use encritary\registerAuth\router\Router;

final class App{

	private Router $router;

	public function __construct(){
		$config = Config::fromFile('config.json');

		Db::init($config->dbCredentials);

		JwtKey::initFromFile('jwt_key.dat');

		ControllerFactory::init();

		$this->router = new Router();
	}

	public function handleRequest(Request $request) : Response{
		return $this->router->execute($request);
	}
}