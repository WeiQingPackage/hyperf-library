<?php
/**
 * User: weiqing
 * Date: 2021/11/29
 */

namespace WeiQing\Library\Extend;

use Hyperf\Context\Context;

class CommonExtend
{
    public static function make(): CommonExtend
    {
        return new self();
    }

    /**
     * 获取服务器IP
     * @return mixed|string
     */
    public function getServerLocalIp(){
        $ip = '127.0.0.1';
        $ips = array_values(swoole_get_local_ip());
        foreach ($ips as $v) {
            if ($v && $v != $ip) {
                $ip = $v;
                break;
            }
        }
        return $ip;
    }

    /**
     * 获取客户端IP
     * @return mixed|string
     */
    public function getClientIp()
    {
        $request = Context::get(\Psr\Http\Message\ServerRequestInterface::class);
        $ip_addr = $request->getHeaderLine('x-forwarded-for');
        if ($this->verifyIp($ip_addr)) {
            return $ip_addr;
        }
        $ip_addr = $request->getHeaderLine('remote-host');
        if ($this->verifyIp($ip_addr)) {
            return $ip_addr;
        }
        $ip_addr = $request->getHeaderLine('x-real-ip');
        if ($this->verifyIp($ip_addr)) {
            return $ip_addr;
        }
        $ip_addr = $request->getServerParams()['remote_addr'] ?? '0.0.0.0';
        if ($this->verifyIp($ip_addr)) {
            return $ip_addr;
        }
        return '0.0.0.0';
    }

    /**
     * 验证IP格式是否正确
     * @param $realip
     * @return mixed
     */
    public function verifyIp($realip)
    {
        return filter_var($realip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * 获取唯一ID
     * @return string
     */
    public function uuid(){
        $uuid = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
        return strtoupper($uuid);
    }

    /**
     * 加密密码
     * @param string $string
     * @param string $salt
     * @return string
     */
    public function getSaltMd5Encrypt(string $string,string $salt): string
    {
        $tmp = md5($string).$salt;
        return md5(md5($tmp).md5($salt));
    }
}