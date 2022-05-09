<?php

namespace WeiQing\Library\Extend;

use Hyperf\Redis\RedisFactory;
use Lysice\HyperfRedisLock\LockTimeoutException;
use Lysice\HyperfRedisLock\RedisLock;

class RedisLockExtend
{
    /**
     * @var
     */
    public $redis;

    public function __construct($redisFactory)
    {
        $this->redis = $redisFactory;
    }

    public static function make(): RedisLockExtend
    {
        return new self(RedisFactory::class);
    }


    /**
     * 阻塞锁
     * @param $lockName
     * @param $timeOut
     * @param $callback
     * @return bool|mixed
     * @throws \Exception
     */
    public function lock($lockName, $timeOut, $callback)
    {
        try {
            // 初始化RedisLock 参数:redis实例 锁名称 超时时间
            $lock = new RedisLock($this->redis, $lockName, $timeOut);
            // 阻塞式
            return $lock->block($timeOut, $callback);
        }catch (LockTimeoutException $lockTimeoutException) {
            var_dump('lock timeout');
            throw new \Exception("获取锁超时: {$timeOut}秒");
        }
    }

    /**
     * 非阻塞锁
     * @param $lockName
     * @param $timeOut
     * @param $callback
     * @return bool|mixed
     * @throws \Exception
     */
    public function glock($lockName,$timeOut, $callback)
    {
        // 初始化RedisLock 参数:redis实例 锁名称 超时时间
        $lock = new RedisLock($this->redis, $lockName, $timeOut);
        // 非阻塞式获取锁
        $res = $lock->get($callback);
        if (!$res) {
            throw new \Exception("请求失败，请重新尝试");
        }
        return $res;
    }
}