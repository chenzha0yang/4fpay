<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Extensions\Curl;

class Defupay extends ApiModel
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


        //TODO: do something
        //self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$httpBuildQuery = true;
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        $params['isApp'] = 'web';
        if ($payConf['is_app'] == 1 && ($payConf['pay_way'] != 1 || $payConf['pay_way'] != 9)) {
            self::$isAPP = true;
            $params['isApp'] = 'H5';
        }

        $params['body'] = "test";
        $params['charset'] = "UTF-8";                           //参数编码字符集，必填
        $params['merchantId'] = $payConf['business_num'];                    //支付平台分配的商户ID，必填
        $params['notifyUrl'] = $ServerUrl;
        $params['orderNo'] = $order;                             //商户订单号，务必确保在系统中唯一，必填
        $params['paymentType'] = "1";                           //支付类型，固定值为1，必填
        $params['paymethod'] = 'directPay';             //支付方式，directPay：直连模式；bankPay：收银台模式，必填
        if($payConf['pay_way'] == 9 || $payConf['pay_way'] == 1){
            $params['paymethod'] = 'bankPay';
        }
        $params['returnUrl'] = $returnUrl;         //支付成功跳转URL，仅适用于支付成功后立即返回商户界面，必填
        $params['service'] = "online_pay";                      //固定值online_pay，表示网上支付，必填
        $params['title'] = 'test';                              //商品的名称，请勿包含字符，选填
        $params['totalFee'] = $amount;               //订单金额，单位为RMB元，必填
        $params['defaultbank'] = $bank;         //网银代码，当支付方式为bankPay时，该值为空；支付方式为directPay时该值必传
        if($params['paymethod'] == 'bankPay'){
            $params['defaultbank'] = '';
        }

        $signStr                 = self::getSignStr($params, true, true);
        $params['sign']            = strtoupper(sha1($signStr . $payConf['md5_private_key']));
        $params['signType'] = "SHA";//signType不参与加密，所以要放在最后
        $url = $reqData['formUrl'].$params['merchantId'].'-'.$params['orderNo'];

        //发起请求
        $result = self::postHtml($url,$params);

        $post['json'] = $result;
        $post['orderNo'] = $params['orderNo'];
        $post['totalFee'] = $params['totalFee'];

        unset($reqData);
        return $post;
    }

    public static function getQrCode($response)
    {
        $post = $response['data']['json'];

        if ($post) {
            echo $post;die;
        }else{
            $post['errCode'] = '500';
            $post['errMsg'] = '请求失败,请联系管理员';
        }
        return $post;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['signType']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = strtoupper(sha1($signStr . $payConf['md5_private_key']));
        if (strtoupper($sign) == strtoupper($signTrue)) {
            return true;
        }
        return false;
    }

    public static function postHtml($Url, $PostArry){
        if(!is_array($PostArry)){
            throw new Exception("无法识别的数据类型【PostArry】");
        }
        $FormString = "<body onLoad=\"document.actform.submit()\">正在处理，请稍候.....................<form  id=\"actform\" name=\"actform\" method=\"post\" action=\"" . $Url . "\">";
        foreach($PostArry as $key => $value){
            $FormString .="<input name=\"" . $key . "\" type=\"hidden\" value='" . $value . "'>\r\n";
        }
        $FormString .="</form></body>";
        return $FormString;
    }
}