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
    public static $header = array();// 传递数据
    public static $method = 'POST'; // 请求类型 GET POST HEADER
    public static $https = true;   // 规避HTTPS
    public static $headerToArray = false;   // 将头信息转换为数组
    private static $channel = null;   // Curl通道

    private static function set()
    {
        self::$channel = curl_init();// Curl通道
        if (self::$https) {
            curl_setopt(self::$channel, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt(self::$channel, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        if (is_string(self::$method) && strtoupper(self::$method) === 'GET') {
            self::parseHttpData();
        }

        curl_setopt(self::$channel, CURLOPT_URL, self::$url);
        curl_setopt(self::$channel, CURLOPT_TIMEOUT, self::$timeout);// 设置超时值

        if (is_string(self::$method) && strtoupper(self::$method) === 'HEADER') {
            if (self::$headerToArray) {
                curl_setopt(self::$channel, CURLOPT_HEADER, true);
            }
            curl_setopt(self::$channel, CURLOPT_HTTPHEADER, self::$header);
            curl_setopt(self::$channel, CURLOPT_POSTFIELDS, self::$request);

        } else {

            curl_setopt(self::$channel, CURLOPT_HEADER, false);

            if (is_string(self::$method) && strtoupper(self::$method) === 'POST') {

                curl_setopt(self::$channel, CURLOPT_POST, true); // 使用POST请求方式
                curl_setopt(self::$channel, CURLOPT_POSTFIELDS, self::$request);// 传递数据

            }
        }

        curl_setopt(self::$channel, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * 执行 Curl
     *
     * @return bool|object|array
     */
    public static function Request()
    {
        self::set();
        $response = curl_exec(self::$channel);
        if (self::$headerToArray) {
            if (strtoupper(self::$method) === 'HEADER') {
                $headerSize = curl_getinfo(self::$channel, CURLINFO_HEADER_SIZE);
                self::disconnection();
                return self::parseHttpResponse($headerSize, $response);
            }
        }

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
     * @return bool|object|array
     */
    public static function sendRequest()
    {
        self::set();
        $response = curl_exec(self::$channel);
        if (self::$headerToArray) {
            if (strtoupper(self::$method) === 'HEADER') {
                $headerSize = curl_getinfo(self::$channel, CURLINFO_HEADER_SIZE);
                self::disconnection();
                return self::parseHttpResponse($headerSize, $response);
            }
        }

        $code = curl_getinfo(self::$channel, CURLINFO_HTTP_CODE);
        if ($code === 200) {

            self::disconnection();
            return $response;

        }
        self::disconnection();
        return $code;
    }

    /**
     *
     * get 请求方式 格式化参数
     */
    private static function parseHttpData()
    {
        $queryString = http_build_query(self::$request);
        self::$url .= "?{$queryString}";
    }

    private static function parseHttpResponse($headerSize, $response)
    {
        $headers = array();
        $headerText = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        $status = 0;
        foreach (explode("\r\n", $headerText) as $i => $line) {
            if (!$line) {
                continue;
            }
            if ($i === 0) {
                list ($protocol, $status) = explode(' ', $line);
                $status = $status;
            } else {
                list ($key, $value) = explode(': ', $line);
                $headers[strtolower($key)] = urldecode($value);
            }
        }
        $result = array("status" => $status, "headers" => $headers, "body" => $body);
        return $result;
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


    // Xml 转 一维数组
    public static function xmlToData($xml, $true)
    {
        return (array)simplexml_load_string($xml);
    }

    // Xml 转 数组, 不包括根键
    public static function xmlToArray($xml)
    {
        $arr = self::xmlToArrayRooted($xml);
        $key = array_keys($arr);
        return $arr[$key[0]];
    }

    // Xml 转 数组, 包括根键
    public static function xmlToArrayRooted($xml)
    {
        $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
        if (preg_match_all($reg, $xml, $matches)) {
            $count = count($matches[0]);
            $arr = array();
            for ($i = 0; $i < $count; $i++) {
                $key = $matches[1][$i];
                $val = self::xmlToArrayRooted($matches[2][$i]);  // 递归
                if (array_key_exists($key, $arr)) {
                    if (is_array($arr[$key])) {
                        if (!array_key_exists(0, $arr[$key])) {
                            $arr[$key] = array($arr[$key]);
                        }
                    } else {
                        $arr[$key] = array($arr[$key]);
                    }
                    $arr[$key][] = $val;
                } else {
                    $arr[$key] = $val;
                }
            }
            return $arr;
        } else {
            return $xml;
        }
    }

    //字符串替换
    public static function strToData($str, $rule = '')
    {
        if (is_array($rule)) {
            foreach ($rule as $key => $val) {
                $str = str_replace($val, '', $str);
            }
        } else {
            $str = str_replace($rule, '', $str);
        }

        return $str;
    }
}