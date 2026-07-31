<?php

namespace App\Traits;

trait HashIp
{
    protected function hashIp(?string $ip): ?string
    {
        if (!$ip) return null;
        $salt = config('app.ip_hash_salt');
        if (!$salt) $salt = config('app.key');
        return hash_hmac('sha256', $ip, $salt);
    }
}
