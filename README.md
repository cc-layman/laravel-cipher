# Laravel Cipher

> 基于 AES-256-GCM + RSA 的 Laravel API 请求与响应加密扩展包。

## 设计目标

该扩展包主要解决以下问题：
- API 请求参数加密
- API 响应数据加密
- 请求完整性校验
- 响应签名验证
- 防重放攻击（timestamp）
- 前后端统一加密协议

---

## 加密协议设计

### 请求格式

```json
{
  "data": "Base64(AES-GCM Ciphertext)",
  "tag": "Base64(GCM Tag)",
  "meta": "Base64(RSA(key, iv, timestamp, nonce))",
  "signature": "可选"
}
```

### `meta` 解密后的内容

```json
{
  "key": "base64(aesKey)",
  "iv": "base64(iv)",
  "timestamp": 1710000000,
  "nonce": "uuid"
}
```


## 目录结构

```text
laravel-cipher/
├── composer.json
├── README.md
├── config/
│   └── cipher.php
├── src/
│   ├── CipherServiceProvider.php
│   ├── Facades/
│   │   └── Cipher.php
│   ├── Commands/
│   │   └── CipherCommand.php
│   ├── Contracts/
│   │   └── CipherInterface.php
│   ├── Services/
│   │   ├── CipherService.php
│   │   ├── RsaService.php
│   │   ├── AesService.php
│   │   ├── ReplayService.php
│   │   ├── Service.php
└── tests/
```

---

## 快速使用

### 安装

```bash
composer require layman/laravel-cipher
php artisan vendor:publish --tag=cipher
```

### 生成密钥

```bash
php artisan cipher:generate
```

### 加密

```php
use Layman\LaravelCipher\Facades\Cipher;

$payload = Cipher::encrypt([
    'name' => '张三',
    'age' => 18,
]);
```

### 解密

```php
use Layman\LaravelCipher\Facades\Cipher;

$payload = Cipher::decrypt($payload);
```

## 免责声明

- 扩展包作者不对本工具的安全性、完整性、可靠性、有效性、正确性或适用性做任何明示或暗示的保证，也不对本扩展包的使用造成的任何直接或间接的损失、责任、索赔、要求或诉讼承担任何责任。
- 扩展包作者保留随时修改、更新、删除权利，无需事先通知或承担任何义务。
- 使用者在下载、安装、运行或使用本扩展包时，即表示已阅读并同意本免责声明。如有异议，请立即停止使用本扩展包，并删除所有相关文件。

## 🙌 支持与贡献

欢迎提 Issue 或 PR 来改进此包。你的每一个建议和贡献，都是我们前进的动力！

如果你觉得 Laravel-Cipher 有帮助，别忘了点个 ⭐ Star 哦！



