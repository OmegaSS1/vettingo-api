<?php

declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\JWT;

class TokenJWT {

	/**
	 * Summary of getToken
	 * @param int $userId
	 * @param int $minutes
	 * @return string
	 */
    public function getToken(int $userId, int $minutes = 30): string{
		$payload = [
			'iss'   => IP,
			'sub'  => $userId,
			'iat'  => time(),
			'exp'  => time() + 60 * $minutes
		];
		return (new JWT())->encode($payload, ENV['KEY']);
	}
}
