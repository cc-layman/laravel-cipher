<?php

namespace Layman\LaravelCipher\Config;

readonly class ReplayConfig
{
    public function __construct(
        public bool $enabled = true,
        public int $ttl = 300,
    ) {

    }
}
