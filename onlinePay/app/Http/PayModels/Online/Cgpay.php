<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Cgpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$unit    = pow(10, 8); // 单位 ： 其他
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method = 'header';

        $data['MerchantOrderId']     = $order;
        $data['OrderDescription']    = 'orderDesc';
        $data['Attach']              = 'Attach';
        $data['Amount']              = $amount * pow(10, 8);
        $data['OrderBuildTimeSpan']  = time();
        $data['OrderExpireTimeSpan'] = time() + 86400;
        $data['Symbol']              = 'CGP';
        $data['ReferUrl']            = $returnUrl;
        $data['CallBackUrl']         = $ServerUrl;
        $data['MerchantId']          = $payConf['business_num'];

        ksort($data);
        $signStr = '';
        foreach ($data as $val) {
            $signStr .= $val . ',';
        }

        $data['sign'] = md5($signStr . $payConf['md5_private_key']);

        $post['data']        = json_encode($data);
        $post['httpHeaders'] = array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen(json_encode($data))
        );
        $post['MerchantOrderId'] = $data['MerchantOrderId'];
        $post['Amount']     = $data['Amount'];


        unset($reqData);
        return $post;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['PayAmount'])) {
            $arr['PayAmount'] = $arr['PayAmount'] / pow(10, 8);
        }
        return $arr;
    }

    public static function signOther($mod, $data, $payConf)
    {
        $sign = $data['Sign'];
        unset($data['Sign']);

        ksort($data);
        $signStr = '';
        foreach ($data as $val) {
            $signStr .= $val . ',';
        }

        $mySign = md5($signStr . $payConf['md5_private_key']);

        if (strtoupper($sign) == strtoupper($mySign) )
        {
            return true;
        }
        return false;
    }


}