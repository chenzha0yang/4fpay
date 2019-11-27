<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{


    /**
     * 代理 所属线路信息
     * @var
     */
    public static $lineUser;

    /**
     * 如果有分页，需加上此方法接收分页参数
     *
     * @return array
     */
    protected static function limitParam()
    {
        return self::requestParam('pageLimit');
    }

    /**
     * 统一数据返回
     *
     * @param $args
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseJson(...$args)
    {
        foreach ($args as $arg) {
            if (is_int($arg)) {
                self::$response['code'] = $arg;
            } elseif (is_string($arg)) {
                self::$response['msg'] = $arg;
            } else {
                self::$response['data'] = (array)$arg;
            }
        }

        if (empty(self::$response['msg'])) {
            self::$response['msg'] = $this->translateInfo(self::$response['code']);
        }
        // 刷新登陆
        if (is_object(self::$adminUser)) {
            self::$response['C-Token'] = encrypt(md5(self::$adminUser->id . '-' . self::$adminUser->username));
            self::$response['R-Token'] = self::$adminUser->remember_token;
        }
        return response()->json(self::$response);
    }

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    protected static function getClientIp(int $type = 0, bool $adv = false)
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
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }


    /**
     * [把返回的数据集转换成树形结构Tree]
     * @param array  $list 要转换的数据集
     * @param string $pid 父级ID键名
     * @param string $pk ID键名
     * @param string $child 子集键名
     * @param int    $root 一级菜单的parent_id 的值 （默认 0）
     * @return array
     */
    function listToTree($list, $pk = 'id', $pid = 'parent_id', $child = 'child', $root = 0)
    {
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent           =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }
}