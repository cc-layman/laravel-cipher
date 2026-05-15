<?php

namespace Layman\LaravelCipher\Contracts;
interface CipherInterface
{
    /**
     * 加密
     *
     * @param array $data
     *
     * @return array
     */
    public function encrypt(array $data): array;

    /**
     * 解密
     *
     * @param array $data
     *
     * @return array
     */
    public function decrypt(array $data): array;

    /**
     * 签名
     *
     * @param array $data
     *
     * @return array
     */
    public function sign(array $data): array;

    /**
     * 验签
     *
     * @param array $data
     *
     * @return bool
     */
    public function verify(array $data): bool;
}
