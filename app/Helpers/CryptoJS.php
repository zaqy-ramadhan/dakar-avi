<?php
namespace App\Helpers;

class CryptoJS
{
    public static function decrypt($passphrase, $jsonString)
    {
        $json = json_decode($jsonString, true);

        if (!$json || !isset($json['ct'], $json['iv'], $json['s'])) {
            return null;
        }

        $salt = hex2bin(self::hexPad($json['s']));
        $iv = hex2bin(self::hexPad($json['iv']));
        $ciphertext = base64_decode($json['ct']);

        $key = hash_pbkdf2("sha512", $passphrase, $salt, 999, 32, true);

        return openssl_decrypt($ciphertext, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);
    }

    // Untuk konversi hex ke bin yang aman (hex tidak selalu lengkap 2 karakter per byte)
    protected static function hexPad($hex)
    {
        return strlen($hex) % 2 === 1 ? '0' . $hex : $hex;
    }
}
