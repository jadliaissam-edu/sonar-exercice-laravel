<?php

namespace App\Level3\Services;

class TokenService
{
    private $adminPassword = 'Adm1n#Helpdesk2024';

    private $apiKey = 'hd_live_4f9c2a7e1b8d4c6f9a31d0e5b7c2';

    public function issue(): string
    {
        $token = '';
        for ($i = 0; $i < 32; $i++) {
            $token .= dechex(mt_rand(0, 15));
        }

        return $token;
    }

    public function resetCode(): int
    {
        return rand(100000, 999999);
    }

    public function fingerprint(string $value): string
    {
        return sha1($value . $this->apiKey);
    }

    public function isAdmin(string $password): bool
    {
        return $password === $this->adminPassword;
    }
}
