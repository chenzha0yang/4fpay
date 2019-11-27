<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Hengszfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $resData = [];

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

        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $data['price'] = number_format($amount, 2);
        $data['order_id'] = $order;
        $data['mark'] = 'goodsName';
        $data['notify_url'] = $ServerUrl;
        $data['app_id'] = $payConf['business_num'];
        $data['time'] = time();
        $str = self::getSignStr($data);
        $data['sign'] = md5(strtoupper($str . '&key=' . $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['Status'] == '1') {
            if (self::$isAPP) {
                $result['payUrl'] =  $result['Result']['payurl'];
            }else{
                $result['qrcode'] =  $result['Result']['payurl'];
            }
        }
        return $result;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        $data = json_decode(stripslashes($arr['return_type']),true);
        return $data;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $str = self::getSignStr($data);
        $mySign = md5(strtoupper($str . '&key=' . $payConf['md5_private_key']));
        if ($sign == $mySign) {
            return true;
        } else {
            return true;
        }
    }
}