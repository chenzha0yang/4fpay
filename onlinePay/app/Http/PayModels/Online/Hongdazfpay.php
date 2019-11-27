<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;
use function Couchbase\defaultDecoder;

class Hongdazfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $extend = [];

    /**
     * @param array       $reqData 接口传递的参数
     * @param array $payConf
     * @return array
     */
    public static function getAllInfo($reqData, $payConf)
    {
        /**
         * 参数赋值，方法间传递数组
         */
        $order  = $reqData['order'];
        $amount = $reqData['amount'];
        $bank   = $reqData['bank'];
        $type   = 'pc';
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
            $type        = 'h5';
        }

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $data         = array(
            'pid'          => $payConf['business_num'],
            'money'        => sprintf('%.2f', $amount),
            'out_order_id' => $order,
            'extend'       => '',
            'channel'      => $bank,
            'terminal'     => $type
        );
        self::$extend = [
            'pid'   => $payConf['business_num'],
            'key'   => $payConf['md5_private_key'],
            'url'   => 'http://22w.91wlny.com.cn/pay/Queryorder',
            'count' => 0
        ];

        $signStr      = $data['pid'] . $data['money'] . $data['out_order_id'] . $data['extend'] . $payConf['md5_private_key'];
        $data['sign'] = md5($signStr);
        unset($reqData);
        return $data;
    }

    public static function getQrCode($req)
    {

        $data = json_decode($req, true);
        if ($data['error'] === 0) {
            //成功，返回整理好的数组
            $return['error']        = $data['error'];
            $return['payurl']       = $data['data']['payurl'];
            $return['out_order_id'] = $data['data']['out_order_id'];
            $return['price']        = $data['data']['price'];
            return $return;
        } elseif ($data['error'] === 2) {
            //请求成功但获取二维码失败、重新请求获取二维码
            $url  = self::$extend['url'];
            $key  = self::$extend['key'];
            $pid  = self::$extend['pid'];
            $sign = md5($pid . $data['data']['out_order_id'] . $key);

            if (self::$extend['count'] === 3) {
                //最多三次请求，不成功返回错误信息
                return $data;
            }
            //请求次数+1
            self::$extend['count']++;
            $post['pid']          = $pid;
            $post['out_order_id'] = $data['data']['out_order_id'];
            $post['sign']         = $sign;

            Curl::$request = $post;//提交数据
            Curl::$url     = $url;//查询二维码网关
            Curl::$method  = self::$method;//提交方式

            //暂停5s
            sleep(5);
            //请求
            $result = Curl::Request();
            return self::getQrCode($result);
        } else {
            //其余状态返回错误信息数组
            return $data;
        }

    }
}