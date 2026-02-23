<?php

namespace Infrastructure\Auth;

class JWT
{
    private string $secret;
    private string $algorithm;

    public function __construct()
    {
        $this->secret = config('app.jwt.secret');
        $this->algorithm = config('app.jwt.algorithm');
    }

    public function encode(array $payload): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => $this->algorithm
        ];

        // Agregar tiempo de expiración
        $payload['iat'] = time();
        $payload['exp'] = time() + config('app.jwt.expiration');

        $base64UrlHeader = $this->base64UrlEncode(json_encode($header));
        $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac(
            'sha256',
            $base64UrlHeader . "." . $base64UrlPayload,
            $this->secret,
            true
        );

        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public function decode(string $token): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [$base64UrlHeader, $base64UrlPayload, $base64UrlSignature] = $parts;

        // Verificar firma
        $signature = hash_hmac(
            'sha256',
            $base64UrlHeader . "." . $base64UrlPayload,
            $this->secret,
            true
        );

        $base64UrlSignatureCheck = $this->base64UrlEncode($signature);

        if ($base64UrlSignature !== $base64UrlSignatureCheck) {
            return null;
        }

        $payload = json_decode($this->base64UrlDecode($base64UrlPayload), true);

        // Verificar expiración
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    private function base64UrlEncode($data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode($data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
