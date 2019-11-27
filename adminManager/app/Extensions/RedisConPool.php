<?php
/**
 * 统一调用方式
 * 跟前台对应是否压缩或解压
 */

namespace App\Extensions;

use Illuminate\Support\Facades\Redis;

/**
 * Class RedisConPool
 *
 * @method static get($redis)
 * @method static set($redis, $data)
 * @method static setEx($redis, $time, $data)
 * @method static del($redis)
 * @method static keys($redis)
 * @method static type($redis)
 * @method static ttl($redis)
 * @method static strLen($redis)
 * @method static iNcr($redis)
 *
 * @method static sMembers($redis)
 * @method static sCard($redis)
 *
 * @method static hSet($redis, $key, $value)
 * @method static hGetAll($redis)
 * @method static hGet($redis, $key)
 * @method static hLen($redis)
 * @method static hDel($redis,...$args)
 *
 * @method static rPush($redis, $data)
 * @method static lTrim($redis, $start, $end)
 * @method static lLen($redis)
 * @method static lRange($redis, $start, $end)
 * @package App\Extensions
 */
class RedisConPool extends Redis
{

    public static $connection = 'default';

    public static $redisKey;

    private static $instance;

    private static $callFunc = [
        // 常规操作
        'get', 'set', 'setEx', 'del', 'keys', 'type', 'ttl','iNcr',
        // 集合类型
        'sMembers', 'strLen', 'sCard',
        // hash类型
        'hSet', 'hGetAll', 'hGet', 'hLen', 'hDel',
        // 列表类型
        'rPush', 'lTrim', 'lLen', 'lRange',
        //'flushAll', // 危险操作！！！
    ];

    // 禁止实例化
    final private function __construct()
    {
    }

    // 禁止克隆
    private function __clone()
    {
    }

    /**
     * 切换到默认redis链接
     */
    public static function setDefault()
    {
        self::$connection = 'default';
        self::$redisKey   = null;
    }

    /**
     * 设置redis链接
     *
     * @param $redis
     */
    private static function setConnection($redis)
    {
        if (is_array($redis) && !empty($redis['connect'])) {
            self::$connection = (string)$redis['connect'];
            self::$redisKey   = (string)$redis['key'];
        } else {
            self::$redisKey = (string)$redis;
        }

        self::$instance = Redis::connection(self::$connection);
    }

    /**
     * @param string $method
     * @param array $args
     * @return bool|mixed|string
     */
    public static function __callStatic($method, $args)
    {
        if (in_array($method, self::$callFunc, true)) {

            return self::callFunc($method, ...$args);

        } else {

            return false;
        }
    }

    /**
     * Redis其他方法
     *
     * @param $method
     * @param $redis
     * @return mixed
     */
    private static function callFunc($method, ...$redis)
    {
        self::setConnection($redis[0]);
        if (self::$redisKey) {
            $redis[0] = self::$redisKey;
            return self::$instance->{$method}(...$redis);
        }
        return self::$instance->{$method}();
    }

}
