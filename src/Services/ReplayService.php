<?php

namespace Layman\LaravelCipher\Services;

use RuntimeException;

class ReplayService extends Service
{
    /**
     * 校验请求是否有效（基于 timestamp）
     *
     * @param int $timestamp
     *
     * @return void
     */
    public function validate(int $timestamp): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        if ($timestamp <= 0) {
            throw new RuntimeException('Invalid timestamp.');
        }

        if (abs(time() - $timestamp) > $this->getTtl()) {
            throw new RuntimeException('Request expired.');
        }
    }

    /**
     * 获取有效时间（秒）
     *
     * @return int
     */
    private function getTtl(): int
    {
        return (int)$this->replay['ttl'];
    }

    /**
     * 是否启用防重放检查
     *
     * @return bool
     */
    private function isEnabled(): bool
    {
        return (bool)$this->replay['enabled'];
    }
}
