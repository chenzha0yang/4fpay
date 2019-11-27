<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jufupay extends ApiModel
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
        $data['body']             = "";
        $data['extraCommonParam'] = "";
        
        if ( $payConf['pay_way'] == "1" ) {
            $data['payMode']      = "3";                                    //支付类型
            $data['bnkCd']        = $bank;                                  //银行编码（网银必填）
            $data['cardNo']       = "jf";
            $data['accTyp']       = "0";
        } else {
            $data['payMode']      = $bank;                                  //支付类型
        }

        $data['ip'] = self::getIp();
        $signStr = self::getSignStr($data, true, true);
        $data['signType'] = "1";                                            //1代表RSA
        $data['signMsg'] = self::getRsaSign("{$signStr}", $payConf['rsa_private_key']);
        unset($reqData);
        return $data;
    }

    //ip
    public static function getIp() 
    {
        if(!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"]))
        {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        else
        {
            $cip = '';
        }
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);
        return $cip;
    }
}