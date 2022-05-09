<?php
/**
 * User: weiqing
 * Date: 2021/11/30
 */

namespace WeiQing\Library\Extend;

use Hyperf\Di\Annotation\Inject;
use PHPGangsta_GoogleAuthenticator;

class GoogleAuthExtend
{
    /**
     * @Inject()
     * @var PHPGangsta_GoogleAuthenticator
     */
    protected $auth;

    public static function make(): GoogleAuthExtend
    {
        return new self();
    }

    /**
     * @throws \Exception
     */
    public function createSecret(): string
    {
        try {
            return $this->auth->createSecret();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 获取数字
     * @param string $secret
     * @return string
     */
    public function getCode(string $secret): string
    {
        return $this->auth->getCode($secret);
    }

    /**
     * 验证验证码
     * @param string $secret
     * @param string $code
     * @return bool
     */
    public function verifyCode(string $secret, string $code): bool
    {
        return $this->auth->verifyCode($secret, $code);
    }

    /**
     * 二维码
     * @param string $name
     * @param string $secret
     * @param string $title
     * @return string
     */
    public function getQRCodeGoogleUrl(string $name, string $secret, string $title = ''): string
    {
        return "otpauth://totp/{$name}?secret={$secret}";
    }
}