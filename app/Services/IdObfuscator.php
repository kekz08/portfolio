<?php

namespace App\Services;

use App\Exceptions\InvalidEncryptedIdException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class IdObfuscator
{
    public function encode(int $id): string
    {
        $encrypted = Crypt::encryptString((string) $id);

        return strtr($encrypted, [
            '+' => '-',
            '/' => '_',
            '=' => '~',
        ]);
    }

    public function decode(string $encryptedId): int
    {
        $normalized = strtr($encryptedId, [
            '-' => '+',
            '_' => '/',
            '~' => '=',
        ]);

        try {
            $value = Crypt::decryptString($normalized);
        } catch (DecryptException $exception) {
            throw new InvalidEncryptedIdException();
        }

        if (!ctype_digit($value)) {
            throw new InvalidEncryptedIdException();
        }

        return (int) $value;
    }
}
