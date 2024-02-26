<?php
namespace App\Captcha\Providers;

use App\Captcha\CaptchaProviderInterface;

class ProcaptchaProvider implements CaptchaProviderInterface
{
    private string $verifyUrl = 'https://api.procaptcha.io/siteverify';

    public function validate(string $response, string $secretKey, ?string $userIp = null): bool
    {
        if (empty($response)) {
            return false;
        }

        $data = [
            'captcha' => $response,
            'maxVerifiedTime' => 60,
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($this->verifyUrl, false, $context);
        if ($response === false) {
            return false;
        }

        $result = json_decode($response);
        return $result->success;
    }

    public function getName(): string
    {
        return 'Procaptcha';
    }

    public function getHomePageUrl(): string
    {
        return 'https://www.prosopo.io/';
    }

    public function getDocumentationUrl(): string
    {
        return 'https://docs.prosopo.io/';
    }
}
