<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Ludepay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址

        //TODO: do something
        $data = array();
        if($payConf['pay_way'] == '1'){
            $data['service']='TRADE.B2C';
        }else{
            self::$payWay = $payConf['pay_way'];
            self::$reqType = 'curl';
            self::$httpBuildQuery = true;
            self::$resType = 'other';
            $data['service'] = 'TRADE.SCANPAY';//默认值
        }

        if(isset($reqData['isApp']) && $reqData['isApp'] == 1){
            self::$payWay = '';
            self::$reqType = 'form';
            $data['service'] = 'TRADE.H5PAY';//默认值
        }

        $data['version'] = '1.0.0.0';//默认值
        $data['merId'] = $payConf['business_num'];//可以为空
        if($payConf['pay_way'] != '1'){
            $data['typeId'] = $bank;
        }
        $data['tradeNo'] = $order;
        $data['tradeDate'] = date('Ymd',time());
        $data['amount'] = $amount;
        $data['notifyUrl'] = $ServerUrl;
        $data['extra'] = 'beizhuxiaoxi';
        $data['summary'] = 'beizhu';
        $data['expireTime'] = 120;
        $data['clientIp'] = '127.0.0.1';
        if($payConf['pay_way'] == '1'){
            $data['bankId'] = $bank;
        }
        $signStr = self::getSignStr($data, false);
        $data['sign']= self::getMd5Sign($signStr, $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }


    /**
     * @param $data
     * @return array
     */
    public static function getQrCode($data)
    {
        $res = [];
        $qrCode = '';
        $xml = simplexml_load_string($data);
        $result= json_decode(json_encode($xml),TRUE);
        if (isset($result['detail']['qrCode']) && !empty($result['detail']['qrCode'])) {
            $qrCode = base64_decode($result['detail']['qrCode']);
        }
        if (isset($qrCode) && !empty($qrCode)) {
            $res['qrCode'] = $qrCode;
        } else {
            if(isset($result['detail']['code']) || isset($result['detail']['desc']) ){
                $res['code'] = $result['detail']['code'];
                $res['desc'] = $result['detail']['desc'];
            }
        }
        return $res;
    }
}