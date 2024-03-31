<?php

declare(strict_types=1);

namespace encritary\registerAuthUnit\controller\impl;

use encritary\registerAuth\controller\exception\BadParameterFormatException;
use encritary\registerAuth\controller\impl\UserController;
use encritary\registerAuth\exception\AppException;
use encritary\registerAuth\exception\ErrorCode;
use encritary\registerAuth\key\JwtKey;
use encritary\registerAuth\request\Request;
use encritary\registerAuth\utils\password\PasswordCheckStatus;
use Firebase\JWT\JWT;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;
use ReflectionObject;
use function sleep;

class UserControllerTest extends TestCase{
	use ControllerTestTrait{
		setUpBeforeClass as ControllerTest_setUpBeforeClass;
	}

	public const TEST_USER_EMAIL_INCORRECT = "test";

	public const TEST_USER_PASSWORD_WEAK = "12345";

	public const TEST_USER_EMAIL_GOOD = "test1@test.ru";
	public const TEST_USER_PASSWORD_GOOD = "123456Aa";

	public const TEST_USER_EMAIL_PERFECT = "test2@test.ru";
	public const TEST_USER_PASSWORD_PERFECT = "0123456789AbCdE!";

	public const TEST_INVALID_TOKEN = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjAsIm5iZiI6MCwiZXhwIjoxMDAsInVzZXJJZCI6MSwiZWFzdGVyRWdnIjoiZ2xhZCB5b3UgZm91bmQgaXQhIn0.B2Ejs0jMaJVHIznZXphMsYkuXX1A0kml5ilP09gC_Wk";

	public const TEST_TOKEN_LIFETIME = 1; // Время жизни токена в секундах для тестирования его истечения

	public static function setUpBeforeClass() : void{
		JwtKey::initFromRandom();
		self::ControllerTest_setUpBeforeClass();
	}

	private UserController $controller;

	protected function setUp() : void{
		$this->controller = new UserController();
	}

	public function testUserEmailIncorrect() : void{
		$this->expectException(BadParameterFormatException::class);
		$this->expectExceptionMessage("Parameter email expected to be a valid e-mail address");
		$this->controller->register(
			new Request('http://localhost:8080/user.register', [
				'email' => self::TEST_USER_EMAIL_INCORRECT,
				'password' => self::TEST_USER_PASSWORD_PERFECT
			])
		);
	}

	public function testUserPasswordWeak() : void{
		$response = $this->controller->register(
			new Request('http://localhost:8080/user.register', [
				'email' => self::TEST_USER_EMAIL_PERFECT,
				'password' => self::TEST_USER_PASSWORD_WEAK
			])
		);

		$data = self::getJsonViewData($response);
		self::assertEquals('error', $data['status']);

		self::assertEquals(ErrorCode::PasswordTooWeak->value, $data['error']['code']);
	}

	private function userPasswordTest(string $email, string $password, PasswordCheckStatus $expectedStatus) : int{
		$response = $this->controller->register(
			new Request('http://localhost:8080/user.register', [
				'email' => $email,
				'password' => $password
			])
		);

		$data = self::getJsonViewData($response);
		self::assertEquals('success', $data['status']);

		self::assertEquals($expectedStatus->value, $data['result']['password_check_status']);

		$userId = $data['result']['user_id'];
		self::assertIsInt($userId);

		return $userId;
	}

	public function testUserPasswordGood() : void{
		$this->userPasswordTest(
			self::TEST_USER_EMAIL_GOOD,
			self::TEST_USER_PASSWORD_GOOD,
			PasswordCheckStatus::Good
		);
	}

	public function testUserPasswordPerfect() : int{
		return $this->userPasswordTest(
			self::TEST_USER_EMAIL_PERFECT,
			self::TEST_USER_PASSWORD_PERFECT,
			PasswordCheckStatus::Perfect
		);
	}

	#[Depends('testUserPasswordPerfect')]
	public function testUserAlreadyRegistered() : void{
		$response = $this->controller->register(
			new Request('http://localhost:8080/user.register', [
				'email' => self::TEST_USER_EMAIL_PERFECT,
				'password' => self::TEST_USER_PASSWORD_PERFECT
			])
		);

		$data = self::getJsonViewData($response);
		self::assertEquals('error', $data['status']);

		self::assertEquals(ErrorCode::UserAlreadyRegistered->value, $data['error']['code']);
	}

	#[Depends('testUserPasswordPerfect')]
	public function testUserAuthorize(int $userId) : string{
		$ref = new ReflectionObject($this->controller);
		$tokenLifetime = $ref->getProperty('tokenLifetime');
		$tokenLifetime->setValue($this->controller, self::TEST_TOKEN_LIFETIME);

		$response = $this->controller->authorize(
			new Request('http://localhost:8080/user.authorize', [
				'email' => self::TEST_USER_EMAIL_PERFECT,
				'password' => self::TEST_USER_PASSWORD_PERFECT
			])
		);
		$data = self::getJsonViewData($response);
		self::assertEquals('success', $data['status']);

		$accessToken = $data['result']['access_token'];
		$payload = JWT::decode($accessToken, JwtKey::get());

		self::assertObjectHasProperty('userId', $payload);
		self::assertEquals($userId, $payload->userId);

		return $accessToken;
	}

	#[Depends('testUserPasswordPerfect')]
	#[Depends('testUserAuthorize')]
	public function testUserFeed(int $userId, string $accessToken) : void{
		$response = $this->controller->feed(
			new Request('http://localhost:8080/user.feed', [
				'access_token' => $accessToken
			])
		);
		$data = self::getJsonViewData($response);
		self::assertEquals('success', $data['status']);

		self::assertEquals($userId, $data['result']['user_id']);
	}

	#[Depends('testUserAuthorize')]
	public function testUserFeedExpired(string $accessToken) : void{
		sleep(self::TEST_TOKEN_LIFETIME);

		$this->expectException(AppException::class);
		$this->expectExceptionMessage("Bad access token: Expired token");
		$this->controller->feed(
			new Request('http://localhost:8080/user.feed', [
				'access_token' => $accessToken
			])
		);
	}

	public function testUserFeedInvalidToken() : void{
		$this->expectException(AppException::class);
		$this->expectExceptionMessage("Bad access token: Signature verification failed");
		$this->controller->feed(
			new Request('http://localhost:8080/user.feed', [
				'access_token' => self::TEST_INVALID_TOKEN
			])
		);
	}
}