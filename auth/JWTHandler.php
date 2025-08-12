<?php
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class JWTHandler {
    private static $secret = "your_secret_key"; // same key used for encoding

    public static function decode($token) {
        return JWT::decode($token, new Key(self::$secret, 'HS256'));
    }
}
