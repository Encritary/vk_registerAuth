<?php

declare(strict_types=1);

namespace encritary\registerAuthUnit\controller\impl;

use encritary\registerAuth\db\Db;
use encritary\registerAuth\db\DbCredentials;
use encritary\registerAuth\response\Response;
use encritary\registerAuth\view\JsonView;

trait ControllerTestTrait{

	public static function setUpBeforeClass() : void{
		Db::init(new DbCredentials('sqlite::memory:'));

		$db = Db::get();
		$db->exec(<<<QUERY
CREATE TABLE users (
    id integer primary key,
    email varchar(320) not null,
    passwordHash varchar(60) not null
);
QUERY);
		$db->exec(<<<QUERY
CREATE INDEX user_by_email ON users (email);
QUERY);
	}

	protected static function getJsonViewData(Response $response) : array{
		$view = $response->view;
		self::assertInstanceOf(JsonView::class, $view);
		return $view->data;
	}
}