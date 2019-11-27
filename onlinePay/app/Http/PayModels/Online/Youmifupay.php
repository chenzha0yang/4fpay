<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Youmifupay extends ApiModel
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
     * @param array $reqData 接口传递的参数
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
            if((int)$payConf['pay_way'] === 3){
                $data['bankCode'] = 'ALSP';
            }
        }

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$httpBuildQuery = true;

        $data = array(
            //接口名字
            'apiName' => $bank,
            //接口版本
            'apiVersion' => '1.0.0.0',
            //商户ID
            'platformID' => $payConf['business_num'],
            //商户账号
            'merchNo' => $payConf['business_num'],
            //商户订单号
            'orderNo' => $order,
            //交易日期
            'tradeDate' => date('Ymd'),
            //订单金额
            'amt' => sprintf('%.2f',$amount),
            //支付结果通知地址
            'merchUrl' => $ServerUrl,
            //交易摘要z
            'tradeSummary' => 'product',
            'customerIP' => self::getIp()
        );

        $string = '';
        foreach ($data as $key => $value) {
            $string .= $key . '=' . $value . '&';
        }
        $str = rtrim($string, '&');
        $data['signMsg'] = md5($str . $payConf['md5_private_key']);
        
        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        $data = json_decode(json_encode($xmlstring),true);
        if ($data['respData']['respCode'] == '00') {
            $codeUrl = base64_decode($data['respData']['codeUrl']);
            $data['qrCode'] = $codeUrl;
            $data['respCode'] = $data['respData']['respCode'];
            $data['respMsg'] = $data['respData']['respMsg'];
        }
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['signMsg'];

        $string = 'apiName='.$data['apiName'].'&notifyTime='.$data['notifyTime'].'&tradeAmt='.$data['tradeAmt'].'&merchNo='.$data['merchNo'].'&merchParam='.$data['merchParam']
            .'&orderNo='.$data['orderNo'].'&tradeDate='.$data['tradeDate'].'&accNo='.$data['accNo'].'&accDate='.$data['accDate'].'&orderStatus='.$data['orderStatus'];
        $signTrue = md5($string.$payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['orderStatus'] == '1') {
            return true;
        }
        return false;
    }


}