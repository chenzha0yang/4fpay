<?php

namespace App;

use App\Extensions\RedisConPool;
use App\Models\Config\ApiClients;

class AdminClient
{
    /**
     * userId
     * @var [type]
     */
    protected $userId;

    /**
     * 缓存key前缀
     * @var string
     */
    protected $cacheKeyPrefix = 'api:client:info:';

    /**
     * 初始化
     * @param [type] $app_id [description]
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * 获取当前对象
     * @param  string $userId userid
     * @return object
     */
    public static function getInstance($userId)
    {
        static $_instances = [];

        if (array_key_exists($userId, $_instances))
            return $_instances[$userId];

        return $_instances[$userId] = new self($userId);
    }

    /**
     * 获取app信息
     * @return AppModel
     */
    public function info()
    {
        $cacheKey = $this->cacheKeyPrefix . $this->userId;

        $client = RedisConPool::get($cacheKey);
        if(empty($client)){
            $client = ApiClients::where(['revoked' => 1, 'user_id' => (int)$this->userId])->first();
            RedisConPool::setEx($cacheKey, 60, json_encode($client));
        } else {
            $client = json_decode($client);
        }

        return $client;
    }
}