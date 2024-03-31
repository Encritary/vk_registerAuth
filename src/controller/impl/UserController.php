<?php

declare(strict_types=1);

namespace encritary\registerAuth\controller\impl;

use encritary\registerAuth\controller\AttributedController;
use encritary\registerAuth\controller\exception\BadParameterFormatException;
use encritary\registerAuth\controller\Parameters;
use encritary\registerAuth\controller\Route;
use encritary\registerAuth\exception\AppException;
use encritary\registerAuth\exception\ErrorCode;
use encritary\registerAuth\key\JwtKey;
use encritary\registerAuth\model\exception\ModelNotFoundException;
use encritary\registerAuth\model\impl\User;
use encritary\registerAuth\request\Request;
use encritary\registerAuth\response\http\HttpCode;
use encritary\registerAuth\response\Response;
use encritary\registerAuth\response\ErrorResponse;
use encritary\registerAuth\response\SuccessResponse;
use encritary\registerAuth\utils\password\PasswordCheckStatus;
use encritary\registerAuth\utils\password\PasswordUtils;
use Exception;
use Firebase\JWT\JWT;
use function filter_var;
use function password_hash;
use function password_verify;
use function time;
use const FILTER_VALIDATE_EMAIL;
use const PASSWORD_BCRYPT;

class UserController extends AttributedController{

	public const TOKEN_LIFETIME = 60; // время жизни (в секундах) JWT-токена для авторизации

	#[Route]
	public function register(Request $request) : Response{
		$email = Parameters::stringWithSize('email', $request->args, 320);
		if(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
			throw new BadParameterFormatException("Parameter email expected to be a valid e-mail address");
		}

		$password = Parameters::string('password', $request->args);

		$checkMessage = "";
		$passwordCheck = PasswordUtils::checkPasswordStrength($password, $checkMessage);
		if($passwordCheck === PasswordCheckStatus::Weak){
			return new ErrorResponse("Password is too weak: $checkMessage", ErrorCode::PasswordTooWeak->value);
		}

		try{
			User::getByEmail($email);
			$exists = true;
		}catch(ModelNotFoundException){
			$exists = false;
		}

		if($exists){
			return new ErrorResponse("User with e-mail $email is already registered", ErrorCode::UserAlreadyRegistered->value);
		}

		$passwordHash = password_hash($password, PASSWORD_BCRYPT);

		$user = new User($email, $passwordHash);
		$user->insert();

		return new SuccessResponse([
			'user_id' => $user->id,
			'password_check_status' => $passwordCheck->value
		]);
	}

	#[Route]
	public function authorize(Request $request) : Response{
		$email = Parameters::stringWithSize('email', $request->args, 320);
		$password = Parameters::string('password', $request->args);

		$user = User::getByEmail($email);

		if(!password_verify($password, $user->passwordHash)){
			return new ErrorResponse("Invalid password", ErrorCode::InvalidPassword->value, HttpCode::Unauthorized);
		}

		$key = JwtKey::get();
		$now = time();

		$jwt = JWT::encode([
			'iat' => $now,
			'nbf' => $now,
			'exp' => $now + self::TOKEN_LIFETIME,
			'userId' => $user->id
		], $key->getKeyMaterial(), $key->getAlgorithm());

		return new SuccessResponse(['access_token' => $jwt]);
	}

	#[Route]
	public function feed(Request $request) : Response{
		$accessToken = Parameters::string('access_token', $request->args);

		try{
			$payload = JWT::decode($accessToken, JwtKey::get());
			if(!isset($payload->userId)){
				throw new Exception("User is not specified in access token");
			}

			// Проверяем, что пользователь существует. Может выбросить ModelNotFoundException
			User::get($payload->userId);
		}catch(Exception $e){
			throw new AppException("Bad access token: {$e->getMessage()}", HttpCode::Unauthorized, ErrorCode::BadAccessToken->value);
		}

		return new SuccessResponse(true);
	}

	public function getName() : string{
		return 'user';
	}
}