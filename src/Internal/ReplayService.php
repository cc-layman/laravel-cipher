<?php

namespace Layman\LaravelCipher\Internal;

use Layman\LaravelCipher\Config\Config;
use RuntimeException;

class ReplayService
{
    public function __construct(
        protected Config $config,
    ) {

    }

    /**
     * 校验请求是否有效（基于 timestamp）
     *
     * @param int $timestamp
     *
     * @return void
     */
    public function validate(int $timestamp): void
    {
        if (!$this->config->replay->enabled) {
            return;
        }

        if ($timestamp <= 0) {
            throw new RuntimeException('Invalid timestamp.');
        }

        if (abs(time() - $timestamp) > $this->config->replay->ttl) {
            throw new RuntimeException('Request expired.');
        }
    }
}
