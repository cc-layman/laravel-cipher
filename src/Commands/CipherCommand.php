<?php

namespace Layman\LaravelCipher\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Layman\LaravelCipher\Config\Config;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CipherCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cipher:secret {bits=2048}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'openssl generate secret key';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $config = Config::fromArray(config('cipher', []));

        $dirPermission = $config->rsa->dirPermission;
        $privatePath   = $config->rsa->privatePath;
        $publicPath    = $config->rsa->publicPath;

        $privateDir = dirname($privatePath);
        $publicDir  = dirname($publicPath);

        if (!is_dir($privateDir)) {
            mkdir($privateDir, $dirPermission, true);
        }

        if (!is_dir($publicDir)) {
            mkdir($publicDir, $dirPermission, true);
        }

        if (File::exists($privatePath)) {
            File::delete($privatePath);
        }

        if (File::exists($publicPath)) {
            File::delete($publicPath);
        }

        try {
            $bits = (int)$this->argument('bits');

            $openssl = [
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
                "private_key_bits" => $bits,
            ];

            $secret = openssl_pkey_new($openssl);

            if ($secret === false) {
                $this->error('生成密钥失败：'.openssl_error_string());
                die();
            }

            openssl_pkey_export($secret, $privateKey);

            $keyDetails = openssl_pkey_get_details($secret);
            $publicKey  = $keyDetails['key'];

            file_put_contents($privatePath, $privateKey);
            file_put_contents($publicPath, $publicKey);

            $this->info('success');
        } catch (ProcessFailedException $exception) {
            $this->error("Command execution failed: {$exception->getMessage()}");
        }
    }

}
