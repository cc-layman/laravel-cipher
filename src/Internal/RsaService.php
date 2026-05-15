<?php

namespace Layman\LaravelCipher\Internal;

use Layman\LaravelCipher\Config\Config;
use RuntimeException;

class RsaService
{
    public function __construct(
        protected Config $config,
    ) {

    }

    /**
     * 获取公钥内容
     *
     * @return string
     */
    public function getPublicKey(): string
    {
        $path = $this->config->rsa->publicPath;

        if (empty($path) || !file_exists($path)) {
            throw new RuntimeException('RSA public key not found.');
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new RuntimeException('Unable to read RSA public key.');
        }

        return $content;
    }

    /**
     * 获取私钥内容
     *
     * @return string
     */
    private function getPrivateKey(): string
    {
        $path = $this->config->rsa->privatePath;

        if (empty($path) || !file_exists($path)) {
            throw new RuntimeException('RSA private key not found.');
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new RuntimeException('Unable to read RSA private key.');
        }

        return $content;
    }

    /**
     * 获取 RSA 密钥位数
     *
     * @return int
     */
    public function getKeyBits(): int
    {
        $publicKey = openssl_pkey_get_public($this->getPublicKey());

        if ($publicKey === false) {
            throw new RuntimeException('Invalid RSA public key.');
        }

        $details = openssl_pkey_get_details($publicKey);

        return $details['bits'] ?? 0;
    }

    /**
     * 获取 RSA 可加密的最大明文长度（字节）
     *
     * @return int
     */
    public function getMaxEncryptLength(): int
    {
        $bytes = intdiv($this->getKeyBits(), 8);

        if ($this->config->rsa->padding === OPENSSL_PKCS1_OAEP_PADDING) {
            return $bytes - 42;
        }

        return $bytes - 11;
    }

    /**
     * 使用公钥加密
     *
     * @param string $plaintext 明文数据
     *
     * @return string
     */
    public function encrypt(string $plaintext): string
    {

        $success = openssl_public_encrypt($plaintext, $ciphertext, $this->getPublicKey(), $this->config->rsa->padding);

        if (empty($success)) {
            throw new RuntimeException('RSA public encryption failed: '.openssl_error_string());
        }

        return base64_encode($ciphertext);
    }

    /**
     * 使用私钥解密
     *
     * @param string $ciphertext 密文
     *
     * @return string
     */
    public function decrypt(string $ciphertext): string
    {
        $success = openssl_private_decrypt(base64_decode($ciphertext), $plaintext, $this->getPrivateKey(), $this->config->rsa->padding);

        if (empty($success)) {
            throw new RuntimeException('RSA private decryption failed: '.openssl_error_string());
        }

        return $plaintext;
    }

    /**
     * 使用私钥签名
     *
     * @param array $data 原始数据
     *
     * @return string
     */
    public function sign(array $data): string
    {
        $success = openssl_sign(implode($this->config->signature->separator, $data), $signature, $this->getPrivateKey(), $this->config->rsa->algo);

        if (empty($success)) {
            throw new RuntimeException('RSA signing failed: '.openssl_error_string());
        }

        return base64_encode($signature);
    }

    /**
     * 使用公钥验证签名
     *
     * @param array $data 原始数据
     */
    public function verify(array $data): bool
    {
        $signature = $data['signature'];
        unset($data['signature']);

        $result = openssl_verify(implode($this->config->signature->separator, $data), base64_decode($signature), $this->getPublicKey(), $this->config->rsa->algo);

        return $result === 1;
    }
}
