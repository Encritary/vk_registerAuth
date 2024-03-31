<?php

declare(strict_types=1);

namespace encritary\registerAuth\key;

use Exception;
use Firebase\JWT\Key;
use function base64_decode;
use function base64_encode;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function random_bytes;

final class JwtKey{

	private static Key $key;

	public static function initFromFile(string $filePath) : void{
		if(!file_exists($filePath)){
			while(true){
				try{
					$keyBytes = random_bytes(32);
					break;
				}catch(Exception){
					// попробовать снова, если random_bytes не удалось сгенерировать рандомные байты
					continue;
				}
			}

			file_put_contents($filePath, base64_encode($keyBytes));
		}else{
			$keyBytes = base64_decode(file_get_contents($filePath));
		}

		self::$key = new Key($keyBytes, 'HS256');
	}

	public static function get() : Key{
		return self::$key;
	}

	private function __construct(){
		// класс сугубо статический
	}
}