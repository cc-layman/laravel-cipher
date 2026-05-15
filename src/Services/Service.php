<?php

namespace Layman\LaravelCipher\Services;

class Service
{
    protected array $rsa;
    protected array $aes;
    protected array $replay;
    protected array $signature;
    public function __construct()
    {
        $config          = config('cipher');
        $this->rsa       = $config['rsa'];
        $this->aes       = $config['aes'];
        $this->replay    = $config['replay'];
        $this->signature = $config['signature'];
    }
}
