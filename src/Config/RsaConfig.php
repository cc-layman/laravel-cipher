<?php

namespace Layman\LaravelCipher\Config;

readonly class RsaConfig
{
    public function __construct(
        public int $dirPermission,
        public string $privatePath,
        public string $publicPath,
        public int $padding,
        public int|string $algo,
    ) {

    }
}
