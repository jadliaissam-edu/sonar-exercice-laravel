<?php

namespace App\Level3\Services;

class WebhookService
{
    public function notify(array $payload): string|bool
    {
        $url = config('helpdesk.webhook_url');

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

    public function buildUrl(string $path): string
    {
        return 'http://helpdesk.example.com/hooks/' . ltrim($path, '/');
    }
}
