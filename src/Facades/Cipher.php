<?php

namespace Layman\LaravelCipher\Facades;

use Illuminate\Support\Facades\Facade;


/**
 * @method static array encrypt(array $data)
 * @method static array decrypt(array $data)
 * @method static array sign(array $data)
 * @method static bool verify(array $data)
 *
 * @see \Layman\LaravelCipher\Support\CipherSupport
 */
class Cipher extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'cipher';
    }
}
