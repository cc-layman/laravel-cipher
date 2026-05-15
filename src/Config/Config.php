<?php

namespace Layman\LaravelCipher\Config;

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
}
