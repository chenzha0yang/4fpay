<?php

namespace App\Http\Controllers;

use App\Error;
use App\Server;

class ApiController extends Controller
{
    protected $apiResponse = [
        'status' => false,
        'code'   => 401,
    ];

    protected static $_Instance = null;

    public function runApi(&$params)
    {
        return $this->replaceArray(['code' => '1009']);
    }

    /**
     * 路由分发
     *
     * @return \App\response
     */
    public function distribution()
    {
        $server = new Server(new Error());

        return $server->run(static::class);
    }

    protected function replaceArray($array)
    {
        if (!empty($array['status'])) {
            $this->apiResponse['status'] = $array['status'];
        }

        if (!is_null($array['code'])) {
            $this->apiResponse['code'] = (int)$array['code'];
        }

        if (!empty($array['countPage'])) {
            $this->apiResponse['countPage'] = (int)$array['countPage'];
        }

        if (!empty($array['data'])) {
            $this->apiResponse['data'] = $array['data'];
        }

        return $this->apiResponse;

    }

    /**
     * 参数验证
     *
     * @param        $param
     * @param        $key
     * @param string $pattern
     * @param bool   $notNull
     *
     * @return array|string
     */
    public static function checkParameter($param, $key, string $pattern = '', bool $notNull = true)
    {
        // 不允许为空
        if ($notNull) {
            if (empty($param[$key])) {
                return true;
            }
        } else {
            // 允许为空
            if (empty($param[$key])) {
                return false;
            }
        }

        if (!empty($pattern) && !preg_match($pattern, $param[$key])) {
            return true;
        }

        return false;

    }

    /**
     * 获取客户端IP地址
     *
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv  是否进行高级模式获取（有可能被伪装）
     *
     * @return mixed
     */
    protected function getClientIp($type = 0, $adv = false)
    {
        $type = $type ? 1 : 0;
        static $ip = NULL;
        if ($ip !== NULL) return $ip[$type];
        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) unset($arr[$pos]);
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? [$ip, $long] : ['0.0.0.0', 0];
        return $ip[$type];
    }
}