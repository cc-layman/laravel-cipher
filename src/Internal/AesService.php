<?php

namespace Layman\LaravelCipher\Internal;

use Layman\LaravelCipher\Config\Config;
use RuntimeException;

class AesService
{
    public function __construct(
        protected Config $config,
    ) {

    }

    /**
     * 生成随机 AES Key
     *
     * @return string
     */
    private function generateKey(): string
    {
        return random_bytes($this->config->aes->keyLength);
    }

    /**
     * 生成随机 IV
     *
     * @return string
     */
    private function generateIv(): string
    {
        return random_bytes($this->config->aes->ivLength);
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

        $ciphertext = openssl_encrypt($plaintext, $this->config->aes->auth, $key, $this->config->aes->options, $iv, $tag);
        if ($ciphertext === false) {
            throw new RuntimeException('AES encryption failed.');
        }
        return [
            'key'        => base64_encode($key),
            'iv'         => base64_encode($iv),
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
        $plaintext = openssl_decrypt(base64_decode($ciphertext), $this->config->aes->auth, base64_decode($key), $this->config->aes->options, base64_decode($iv), base64_decode($tag));

        if ($plaintext === false) {
            throw new RuntimeException('AES decryption failed or authentication tag verification failed.');
        }

        return $plaintext;
    }
}
