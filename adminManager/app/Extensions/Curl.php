<?php

namespace App\Extensions;

/**
 * Class Curl
 * @package App\Http\Controllers
 *          Curl::$url = $url;
 *          Curl::$method = $method;
 *          Curl::$request = $data;
 *          Curl::$timeout = 10;
 *          Curl::Request();
 *
 *
 */
class Curl
{
    public static $url = null;   // 请求Url地址
    public static $timeout = 30;     // 设置超时值，默认为30秒
    public static $request = array();// 传递数据
    public static $method = 'POST'; // 请求类型
    public static $https = true;    // 规避证书
    private static $channel = null;   // Curl通道

    private static function set()
    {
        self::$channel = curl_init();// Curl通道
        curl_setopt(self::$channel, CURLOPT_URL, self::$url);
        curl_setopt(self::$channel, CURLOPT_TIMEOUT, self::$timeout);// 设置超时值
        curl_setopt(self::$channel, CURLOPT_HEADER, false);
        if (self::$https) {
            curl_setopt(self::$channel, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt(self::$channel, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        if (is_string(self::$method) && strtoupper(self::$method) === 'POST') {
            curl_setopt(self::$channel, CURLOPT_POST, true); // 使用POST请求方式
            curl_setopt(self::$channel, CURLOPT_POSTFIELDS, self::$request);// 传递数据
        }
        curl_setopt(self::$channel, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * 执行 Curl
     *
     * @return bool|object
     */
    public static function Request()
    {
        self::set();
        $response = curl_exec(self::$channel);
        if (curl_getinfo(self::$channel, CURLINFO_HTTP_CODE) === 200) {
            self::disconnection();
            return $response;
        }
        self::disconnection();
        return false;
    }

    /**
     * 执行 Curl
     *
     * @return bool|object
     */
    public static function sendRequest()
    {
        self::set();
        $response = curl_exec(self::$channel);
        $code = curl_getinfo(self::$channel, CURLINFO_HTTP_CODE);

        if ($code === 200) {
            self::disconnection();
            return $response;
        }
        self::disconnection();
        return $code;
    }

    /**
     * 关闭Curl请求通道并重置参数
     */
    private static function disconnection()
    {
        curl_close(self::$channel);
        self::$url = null;
        self::$channel = null;
        self::$request = array();
    }
}