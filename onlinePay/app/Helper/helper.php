<?php

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

if (!function_exists('configFormat')) {
    /**
     * 解密数据库配置
     */
    function configFormat()
    {
        $crypt = new \Illuminate\Encryption\Encrypter(hex2bin(config("app.cbc")), config("app.cipher"));
        foreach (config('database.connections') as $key => $config) {
            if (!empty($config['username'])) {
                config([
                    "database.connections.{$key}.username" => $crypt->decrypt($config['username'])
                ]);
            }
            if (!empty($config['password'])) {
                config([
                    "database.connections.{$key}.password" => $crypt->decrypt($config['password'])
                ]);
            }
        }
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

if (!function_exists('reduceArray')) {
    /**
     * @param $arr
     * 将多维数组转化成一维数组
     * @return array
     */
    function reduceArray($arr) {
        $return = [];
        array_walk_recursive($arr, function ($x) use (&$return) {
            $return[] = $x;
        });
        return $return;
    }
}

if (!function_exists('objectToArray')) {
    /**
     * 对象转数组
     *
     * @param $object
     * @return array
     */
    function objectToArray($object)
    {
        $array = [];
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        } else {
            $array = $object;
        }
        return $array;
    }
}


