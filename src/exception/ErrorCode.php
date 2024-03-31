<?php

declare(strict_types=1);

namespace encritary\registerAuth\exception;

enum ErrorCode: int{

	case MethodNotSpecified = 1;
	case ControllerNotFound = 2;
	case MethodNotFound = 3;
	case MissingParameter = 4;
	case BadParameterFormat = 5;
	case ModelNotFound = 6;

	case UserAlreadyRegistered = 7;
	case PasswordTooWeak = 8;
	case InvalidPassword = 9;
	case BadAccessToken = 10;

	case InternalError = 0xff;
}