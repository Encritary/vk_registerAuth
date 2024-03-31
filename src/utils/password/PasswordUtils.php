<?php

declare(strict_types=1);

namespace encritary\registerAuth\utils\password;

use function preg_match;
use function strlen;

final class PasswordUtils{

	/**
	 * Возвращает результат проверки пароля на надёжность.
	 *
	 * Хорошим считается пароль, содержащий не менее 8 символов, из которых есть хотя бы одна цифра, одна строчная
	 * и одна заглавная буква.
	 * Идеальным считается хороший пароль, содержащий не менее 16 символов и хотя бы один специальный символ.
	 * Слабым считается пароль, который не удовлетворил ни одному из условий выше.
	 *
	 * @param string $password
	 * @param string &$message в эту переменную запишется сообщение о том, почему пароль получил такой статус
	 * @return PasswordCheckStatus
	 */
	public static function checkPasswordStrength(string $password, string &$message = "") : PasswordCheckStatus{
		if(strlen($password) < 8){
			$message = "Password should be at least 8 characters long";
			return PasswordCheckStatus::Weak;
		}

		if(!preg_match('/[a-z]/', $password)){
			$message = "Password should contain at least one lower case letter";
			return PasswordCheckStatus::Weak;
		}
		if(!preg_match('/[A-Z]/', $password)){
			$message = "Password should contain at least one upper case letter";
			return PasswordCheckStatus::Weak;
		}
		if(!preg_match('/[0-9]/', $password)){
			$message = "Password should contain at least one digit";
			return PasswordCheckStatus::Weak;
		}

		if(preg_match('[^\w]', $password) && strlen($password) >= 16){
			$message = "Contains at least one special character and is at least 16 characters long";
			return PasswordCheckStatus::Perfect;
		}else{
			$message = "Contains upper case and lower case letters and digits, is at least 8 characters long";
			return PasswordCheckStatus::Good;
		}
	}

	private function __construct(){
		// класс сугубо статический
	}
}