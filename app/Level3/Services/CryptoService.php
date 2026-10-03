<?php

namespace App\Level3\Services;

class CryptoService
{
    public function encrypt(string $plain, string $key): string
    {
        $cipher = openssl_encrypt($plain, 'aes-128-ecb', $key, OPENSSL_RAW_DATA);

        return base64_encode($cipher);
    }

    public function decrypt(string $encoded, string $key): string
    {
        $plain = openssl_decrypt(base64_decode($encoded), 'aes-128-ecb', $key, OPENSSL_RAW_DATA);

        return $plain === false ? '' : $plain;
    }

    public function legacyEncrypt(string $plain, string $key, string $iv): string
    {
        return openssl_encrypt($plain, 'des-ede3-cbc', $key, 0, $iv);
    }
}
