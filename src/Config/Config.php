<?php

namespace Layman\LaravelCipher\Config;

use InvalidArgumentException;

readonly class Config
{
    public function __construct(
        public AesConfig $aes,
        public RsaConfig $rsa,
        public ReplayConfig $replay,
        public SignatureConfig $signature,
    ) {
    }

    public static function fromArray(array $config): self
    {
        return new self(
            aes: new AesConfig(
                auth: $config['aes']['auth'] ?? 'aes-256-gcm',
                options: $config['aes']['options'] ?? OPENSSL_RAW_DATA,
                keyLength: $config['aes']['key_length'] ?? 32,
                ivLength: $config['aes']['iv_length'] ?? 12,
            ),
            rsa: new RsaConfig(
                dirPermission: $config['rsa']['dir_permission'] ?? 0755,
                privatePath: $config['rsa']['private_path'] ?? storage_path('app/ciphers/private.pem'),
                publicPath: $config['rsa']['public_path'] ?? storage_path('app/ciphers/public.pem'),
                padding: $config['rsa']['padding'] ?? OPENSSL_PKCS1_OAEP_PADDING,
                algo: $config['rsa']['algo'] ?? OPENSSL_ALGO_SHA256,
            ),
            replay: new ReplayConfig(
                enabled: $config['replay']['enabled'] ?? true,
                ttl: $config['replay']['ttl'] ?? 300,
            ),
            signature: new SignatureConfig(
                enabled: $config['signature']['enabled'] ?? true,
                separator: $config['signature']['separator'] ?? 300,
            ),
        );
    }

    public function toArrayRecursive($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->toArrayRecursive($value);
            }
        }

        return $data;
    }


    public function options(int $key): string
    {
        return match ($key) {
            OPENSSL_RAW_DATA => 'raw',
            default => throw new InvalidArgumentException('Unsupported RSA padding.'),
        };
    }

    public function padding(int $key): string
    {
        return match ($key) {
            OPENSSL_PKCS1_OAEP_PADDING => 'oaep',
            default => throw new InvalidArgumentException('Unsupported RSA padding.'),
        };
    }

    public function algo(int $key): string
    {
        return match ($key) {
            OPENSSL_ALGO_MD5 => 'md5',
            OPENSSL_ALGO_SHA256 => 'sha256',
            OPENSSL_ALGO_SHA512 => 'sha512',
            default => throw new InvalidArgumentException('Unsupported RSA algorithm.'),
        };
    }
}
