<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Sanliuyipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false


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
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址


        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method  = 'get';
        self::$resType = 'other';
        self::$httpBuildQuery = 'true';

        $data['shop_phone']      = $payConf['business_num']; //商户号
        $data['passageway_code'] = $bank;
        $data['payment']         = sprintf('%.2f', $amount);
        $data['order_number']    = $order;

        $signStr      = self::getSignStr($data);
        $data['sign'] = hash('sha256', $signStr . $payConf['md5_private_key']);
        $data['name'] = $order;
        if ($payConf['pay_way'] == 1 || $payConf['pay_way'] == 9) {
            if ($payConf['is_pc_wap'] == 2) {
                $data['device_type'] = 'mobile';
            } else {
                $data['device_type'] = 'pc';
            }
        }
        $data['notify_url']  = $ServerUrl;
        $data['return_url']  = $returnUrl;

        $post['payment'] = sprintf('%.2f', $amount);
        $post['order_number'] = $order;
        $post['data']        = $data;
        $post['httpHeaders'] = [
            "Content-type: application/json;charset=utf-8",
            "Accept: application/json"
        ];
        unset($reqData);
        return $post;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['code'] == '1') {
            $result['qrcode'] = $result['data'];
        }
        return $result;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $signStr  = 'order_number=' . $data['order_number'] . '&platform_order_number=' . $data['platform_order_number'] . '&money=' . $data['money'] . '&order_money=' . $data['order_money'] . '&poundage=' . $data['poundage'] .
            '&pay_state=' . $data['pay_state'] . '&pay_time=' . $data['pay_time'];
        $signTrue = hash('sha256', $signStr . $payConf['md5_private_key']);
        $sign      = $data['sign']; //取SIGN
        if ($sign == $signTrue && $data['pay_state'] == 'success') {
            return true;
        } else {
            return true;
        }
    }
}