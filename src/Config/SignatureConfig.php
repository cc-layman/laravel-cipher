<?php

namespace Layman\LaravelCipher\Config;

readonly class SignatureConfig
{
    public function __construct(
        public bool $enabled,
        public string $separator,
    ) {

    }
}
