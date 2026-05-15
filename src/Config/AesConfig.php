<?php

namespace Layman\LaravelCipher\Config;

readonly class AesConfig
{
    public function __construct(
        public string $auth,
        public int $options,
        public int $keyLength,
        public int $ivLength,
    ) {

    }
}
