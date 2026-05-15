<?php

namespace Layman\LaravelCipher\Support;

use Illuminate\Support\Str;
use Layman\LaravelCipher\Config\Config;
use Layman\LaravelCipher\Contracts\CipherInterface;
use Layman\LaravelCipher\Internal\AesService;
use Layman\LaravelCipher\Internal\ReplayService;
use Layman\LaravelCipher\Internal\RsaService;
use RuntimeException;

class CipherSupport implements CipherInterface
{
    public function __construct(
        protected AesService $aesService,
        protected RsaService $rsaService,
        protected ReplayService $replayService,
        protected Config $config,
    ) {

    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function encrypt(array $data): array
    {
        $plaintext = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($plaintext === false) {
            throw new RuntimeException('JSON encode failed.');
        }

        $params = $this->aesService->encrypt($plaintext);

        $meta = [
            'key'       => $params['key'],
            'iv'        => $params['iv'],
            'timestamp' => time(),
            'nonce'     => Str::uuid(),
        ];

        $meta = json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($meta === false) {
            throw new RuntimeException('Meta JSON encode failed.');
        }

        $payload = [
            'data' => $params['ciphertext'],
            'tag'  => $params['tag'],
            'meta' => $this->rsaService->encrypt($meta),
        ];

        if ($this->config->signature->enabled) {
            $payload['signature'] = $this->rsaService->sign($payload);
        }

        return $payload;
    }

    /**
     * 解密
     *
     * @param array $data
     *
     * @return array
     */
    public function decrypt(array $data): array
    {
        foreach (['data', 'tag', 'meta'] as $field) {
            if (empty($data[$field])) {
                throw new RuntimeException("Missing field: {$field}");
            }
        }

        // 1. 验证签名
        if ($this->config->signature->enabled && !empty($data['signature'])) {
            if (!$this->rsaService->verify($data)) {
                throw new RuntimeException('Invalid signature.');
            }
        }

        // 2. RSA 解密 meta
        $meta = $this->rsaService->decrypt($data['meta']);
        $meta = json_decode($meta, true);

        if (!is_array($meta)) {
            throw new RuntimeException('Invalid meta.');
        }

        foreach (['key', 'iv', 'timestamp', 'nonce'] as $field) {
            if (!isset($meta[$field])) {
                throw new RuntimeException("Missing meta field: {$field}");
            }
        }

        // 3. 时间有效性校验
        $this->replayService->validate((int)$meta['timestamp']);

        // 4. AES-GCM 解密
        $plaintext = $this->aesService->decrypt($data['data'], $meta['key'], $meta['iv'], $data['tag']);

        // 5. JSON 解码
        $payload = json_decode($plaintext, true);

        if (!is_array($payload)) {
            throw new RuntimeException('Invalid JSON payload.');
        }

        return $payload;
    }

    /**
     * 签名
     *
     * @param array $data
     *
     * @return array
     */
    public function sign(array $data): array
    {
        foreach (['data', 'tag', 'meta'] as $field) {
            if (empty($data[$field])) {
                throw new RuntimeException("Missing field: {$field}");
            }
        }

        $data['signature'] = $this->rsaService->sign($data);

        return $data;
    }

    /**
     * 验签
     *
     * @param array $data
     *
     * @return bool
     */
    public function verify(array $data): bool
    {
        foreach (['data', 'tag', 'meta', 'signature'] as $field) {
            if (empty($data[$field])) {
                throw new RuntimeException("Missing field: {$field}");
            }
        }

        return $this->rsaService->verify($data);
    }
}
