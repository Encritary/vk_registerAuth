<?php

declare(strict_types=1);

namespace encritary\registerAuth\model\impl;

use encritary\registerAuth\db\Db;
use encritary\registerAuth\model\exception\ModelNotFoundException;
use encritary\registerAuth\model\Model;
use PDO;
use PDOStatement;

class User extends Model{

	public static function get(int $id) : self{
		$db = Db::get();
		$stmt = $db->prepare(<<<QUERY
SELECT email, passwordHash
FROM users
WHERE id = ?
QUERY);
		$stmt->bindValue(1, $id, PDO::PARAM_INT);
		$stmt->execute();

		$row = $stmt->fetch();
		if($row === false){
			throw new ModelNotFoundException("User with ID $id not found");
		}

		[$email, $passwordHash] = $row;
		return new self($email, $passwordHash, $id);
	}

	public static function getByEmail(string $email) : self{
		$db = Db::get();
		$stmt = $db->prepare(<<<QUERY
SELECT id, email, passwordHash
FROM users
WHERE email = ?
QUERY);
		$stmt->bindValue(1, $email);
		$stmt->execute();

		$row = $stmt->fetch();
		if($row === false){
			throw new ModelNotFoundException("User with E-mail $email not found");
		}

		// e-mail запрашивается на случай, если в БД другой регистр у букв в e-mail
		[$id, $email, $passwordHash] = $row;
		return new self($email, $passwordHash, $id);
	}

	public function __construct(
		public string $email,
		public string $passwordHash,
		public ?int $id = null
	){}

	protected function prepareInsert(PDO $db) : PDOStatement{
		$stmt = $db->prepare(<<<QUERY
INSERT INTO users
(email, passwordHash)
VALUES (?, ?)
QUERY);
		$stmt->bindValue(1, $this->email);
		$stmt->bindValue(2, $this->passwordHash);
		return $stmt;
	}

	protected function afterInsert(PDO $db) : void{
		$this->id = (int) $db->lastInsertId();
	}
}