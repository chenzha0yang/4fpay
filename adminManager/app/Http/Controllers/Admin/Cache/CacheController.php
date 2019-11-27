<?php

namespace App\Http\Controllers\Admin\Cache;

use App\Extensions\RedisConPool;
use App\Extensions\Code;
use App\Http\Controllers\AdminController;

class CacheController extends AdminController
{
    private $options = [
        'key' => [
            'getCacheKey',
            'getRedisKey',
        ],
        'val' => [
            'getCacheVal',
            'getRedisVal',
        ],
        'del' => [
            'delCacheKey',
            'delRedisKey',
        ],
        'len' => [
            'lenCacheVal',
            'lenRedisVal',
        ]
    ];

    public function cacheOption($option)
    {
        if (in_array($option, array_keys($this->options))) {
            list($rule, $redisFunc) = $this->options[$option];
        } else {
            return $this->responseJson(Code::NO_THIS_OPTION);
        }

        $post = self::requestParam($rule);
        if ((int)$post['server'] === 1) {
            return $this->{$redisFunc}($post);
        } else {
            return $this->responseJson(Code::NO_THIS_OPTION);
        }
    }

    private function getRedisKey($post)
    {
        if ((int)$post['getKeyType'] != Code::KEY_ALL && empty($post['getKeyName'])) {
            return $this->responseJson(self::translateInfo(Code::KEY_NOT_EXISTS));
        }
        $redis['connect'] = $post['selectDB'];
        if (!empty($post['getKeyName'])) {
            $redis['key'] = $post['getKeyName'];
        }

        if ((int)$post['getKeyType'] === Code::KEY_ALL) {
            $redis['key'] = '*';
        } elseif ((int)$post['getKeyType'] === Code::KEY_PREFIX) {
            $redis['key'] .= '*';
        } else {
            $redis['key'] = "*{$redis['key']}";
        }

        $keys = RedisConPool::keys($redis);

        foreach ($keys as &$key) {
            $redis['key'] = $key;
            $type         = RedisConPool::type($redis);
            $key          .= " ({$this->translateInfo("redisType.{$type}", 'lang')})";
        }

        return $this->responseJson($keys);
    }

    private function delRedisKey($post)
    {
        $redis['connect'] = $post['selectDB'];
        $redis['key']     = $post['delKeyName'];

        if ((int)$post['delKeyType'] === Code::KEY_PREFIX) {
            $redis['key'] .= '*';
        } elseif ((int)$post['delKeyType'] === Code::KEY_SUFFIX) {
            $redis['key'] = "*{$redis['key']}";
        }

//        $redis['key'] = RedisConPool::keys($redis['key']);
        if (RedisConPool::del($redis)) {
            return $this->responseJson(Code::DELETE_SUCCESS);
        }

        return $this->responseJson(Code::REDIS_DEL_FAIL);
    }

    private function getRedisVal($post)
    {
        $redis['connect'] = $post['selectDB'];
        $redis['key']     = $post['getValName'];

        if (empty((int)$post['getValType'])) {
            $post['getValType'] = RedisConPool::type($redis);
        }
        $value = $this->getRedisValByType((int)$post['getValType'], $redis);
        $data  = $value;
        if ((int)$post['jsonType'] === Code::YES) {
            if (is_array($data) && in_array($post['getValType'], [Code::REDIS_TYPE_LIST, Code::REDIS_TYPE_HASH])) {
                foreach ($data as &$item) {
                    $item = json_decode($item, true);
                }
            } else {
                try {
                    $array = json_decode($value, true);
                    if ($array) {
                        $data = $array;
                    }
                } catch (\Exception $exception) {
                }
            }
        }

        if (is_string($data) || is_int($data)) {
            $data = [$data];
        }

        return $this->responseJson($data);
    }

    /**
     * 根据 key 类型获取值
     *
     * @param int $type
     * @param array $redis
     * @return bool|string
     */
    private function getRedisValByType(int $type, array $redis)
    {
        switch ($type) {
            // 字符串
            case Code::REDIS_TYPE_STR :
                return RedisConPool::get($redis);
                break;
            // 集合
            case Code::REDIS_TYPE_SET :
                return RedisConPool::sMembers($redis);
                break;
            // 列表
            case Code::REDIS_TYPE_LIST :
                return RedisConPool::lRange($redis, 0, -1);
                break;
            // 哈希表
            case Code::REDIS_TYPE_HASH :
                return RedisConPool::hGetAll($redis);
                break;
            // 默认字符串
            default :
                return RedisConPool::get($redis);
                break;
        }
    }

    private function lenRedisVal($post)
    {
        $redis['connect'] = $post['selectDB'];
        $redis['key']     = $post['lenName'];

        if (empty((int)$post['lenType'])) {
            $post['lenType'] = RedisConPool::type($redis);
        }

        return $this->responseJson([
            $post['lenName'],
            $this->lenRedisValByType((int)$post['lenType'], $redis)
        ]);
    }

    private function lenRedisValByType(int $type, array $redis)
    {
        switch ($type) {
            // 字符串
            case Code::REDIS_TYPE_STR :
                return RedisConPool::strLen($redis);
                break;
            // 集合
            case Code::REDIS_TYPE_SET :
                return RedisConPool::sCard($redis);
                break;
            // 列表
            case Code::REDIS_TYPE_LIST :
                return RedisConPool::lLen($redis);
                break;
            // 哈希表
            case Code::REDIS_TYPE_HASH :
                return RedisConPool::hLen($redis);
                break;
            // 默认字符串
            default :
                return RedisConPool::strLen($redis);
                break;
        }
    }
}