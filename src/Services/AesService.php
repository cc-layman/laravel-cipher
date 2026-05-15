<?php

namespace Layman\LaravelCipher\Services;

use RuntimeException;

class AesService extends Service
{
    /**
     * 生成随机 AES Key
     *
     * @return string
     */
    private function generateKey(): string
    {
        return random_bytes($this->aes['key_length']);
    }

    /**
     * 生成随机 IV
     *
     * @return string
     */
    private function generateIv(): string
    {
        return random_bytes($this->aes['iv_length']);
    }

    /**
     * AES 加密
     *
     * @param string $plaintext 原始明文
     *
     * @return array
     */
    public function encrypt(string $plaintext): array
    {
        $key = $this->generateKey();
        $iv  = $this->generateIv();
        $tag = '';

        $ciphertext = openssl_encrypt($plaintext, $this->aes['auth'], $key, $this->aes['options'], $iv, $tag);
        if ($ciphertext === false) {
            throw new RuntimeException('AES encryption failed.');
        }

        return [
            'key'        => $key,
            'iv'         => $iv,
            'tag'        => base64_encode($tag),
            'ciphertext' => base64_encode($ciphertext),
        ];
    }

    /**
     * AES 解密
     *
     * @param string $ciphertext 二进制密文
     * @param string $key        AES Key
     * @param string $iv         IV
     * @param string $tag        GCM Tag
     */
    public function decrypt(string $ciphertext, string $key, string $iv, string $tag): string
    {
        $plaintext = openssl_decrypt($ciphertext, $this->aes['auth'], $key, $this->aes['options'], $iv, $tag);

        if ($plaintext === false) {
            throw new RuntimeException('AES decryption failed or authentication tag verification failed.');
        }

        return $plaintext;
    }
}
