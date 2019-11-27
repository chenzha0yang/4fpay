<?php

$httpServer = null;

if (!function_exists("replaceRedisKey")) {
    /**
     * 替换redisKey中的预留值位置
     *
     * @param $line
     * @param array $replace
     * @return mixed
     */
    function replaceRedisKey($line, array $replace = [])
    {
        if (empty($replace)) {
            return $line;
        }

        foreach ($replace as $key => $value) {
            $line = str_replace(
                [':' . $key, ':' . \Illuminate\Support\Str::upper($key), ':' . \Illuminate\Support\Str::ucfirst($key)],
                [$value, \Illuminate\Support\Str::upper($value), \Illuminate\Support\Str::ucfirst($value)],
                $line
            );
        }

        return $line;
    }
}

if (!function_exists("getRedis")) {
    /**
     * 获取redis连接以及key名
     *
     * @param string $key
     * @param array $replace
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    function getRedis(string $key, array $replace = [])
    {
        // 如果 redisKey 文件中不存在这个 key
        // 则返回 $key 作为 redisKey
        if (!config()->has("redisKey.{$key}")) {
            return $key;
        }

        // 如果 redisKey 文件中的这个 key 的值为字符串
        // 则返回对应的值作为 redisKey 可以设置替换值
        // 并且使用默认redis链接
        if (is_string(config("redisKey.{$key}"))) {
            return replaceRedisKey(config("redisKey.{$key}"), $replace);
        }

        return [
            "connect" => config("redisKey.{$key}.connect"),
            "key"     => replaceRedisKey(config("redisKey.{$key}.key"), $replace)
        ];
    }
}

if (!function_exists('curl')) {
    /**
     * 获取一个curl实例
     *
     * @return \App\Extensions\Curl|null
     */
    function curl()
    {
        return \App\Extensions\Curl::instance();
    }
}