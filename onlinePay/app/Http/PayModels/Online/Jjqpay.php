<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jjqpay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something
        self::$unit = "2";
        self::$reqType = "curl";
        self::$payWay = $payConf['pay_way'];
        if($payConf['is_app'] == '1'){
            self::$isAPP  = true;
            self::$resType = 'other';
        }

        $data = [];
        $data['inputCharset']     = "1";                                    //固定填1；1代表UTF-8
        $data['partnerId']        = $payConf['business_num'];               //商户号
        $data['returnUrl']        = $returnUrl;                             //同步通知地址
        $data['notifyUrl']        = $ServerUrl;                             //异步通知地址
        $data['orderNo']          = $order;                                 //订单号
        $data['orderAmount']      = $amount*100;                            //金额（分）
        $data['orderCurrency']    = "156";                                  //固定填156;人民币
        $data['orderDatetime']    = date("YmdHis",strtotime(" +12 hour"));  //时间（24小时）
        $data['subject']          = "";
        $data['body']             = $order;
        $data['extraCommonParam'] = "";
        $data['payMode']          = $bank;                                  //支付类型

        $data['ip'] = self::getIp();
        $signStr = self::getSignStr($data, true, true);
        $data['signType'] = "1";                                            //1代表RSA
        $data['signMsg'] = self::getRsaSign("{$signStr}", $payConf['rsa_private_key']);
        unset($reqData);
        return $data;
    }

    //回调金额化分为元
    public static function getVerifyResult($request, $mod)
    {
        $data = $request->all();
        $data['orderAmount'] = $data['orderAmount'] / 100;
        return $data;
    }

    //h5返回html页面
    public static function getQrCode($response)
    {
        $html = json_decode($response,true);
        echo $html['retHtml'];exit;
    }

    public static function SignOther($model, $param, $payConf){
        $signMsg = $param['signMsg'];
        unset($param['signMsg']);
        unset($param['signType']);
        ksort($param);
        $signStr = "";
        foreach ($param as $key => $value) {
            if ($value != "" && !is_array($value)) {
                $signStr .= $key . "=" . $value . "&";
            }
        }
        $signStr   = trim($signStr, "&");
        $publicKey = trim($payConf['public_key']);
        $res       = openssl_get_publickey($publicKey);

        $result    = openssl_verify($signStr, base64_decode($signMsg), $res);
        openssl_free_key($res);
        if($result){
            return true;
        }else{
            return false;
        }
    }
}