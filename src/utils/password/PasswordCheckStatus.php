<?php

declare(strict_types=1);

namespace encritary\registerAuth\utils\password;

enum PasswordCheckStatus: string{
	case Weak = 'weak';
	case Good = 'good';
	case Perfect = 'perfect';
}