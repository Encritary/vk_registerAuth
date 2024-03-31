<?php

declare(strict_types=1);

namespace encritary\registerAuth\db;

namespace encritary\registerAuth\db;

final class DbCredentials{

	public function __construct(
		public readonly string $dsn,
		public readonly ?string $username = null,
		public readonly ?string $password = null
	){}
}