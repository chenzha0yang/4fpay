<?php
namespace App\Http\PayModels\Online;


use App\ApiModel;

class Xinfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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

        $data = array();
        $data['versionId']      = "1.0";//服务版本号必输1.0
        $data['orderAmount']    = $amount * 100;//订单金额必输以分为单位
        $data['orderDate']      = date("YmdHms");//订单日期必输yyyyMMddHHmmss
        $data['currency']       = "RMB";// 货币类型必输RMB：人民币其他币种代号另行提供8
        $data['transType']      = "008";//交易类别必输默认填写008
        $data['asynNotifyUrl']  = $ServerUrl;//异步通知URL
        $data['synNotifyUrl']   = $returnUrl;//同步返回URL
        $data['signType']       = "MD5";//加密方式必输MD5
        $data['merId']          = $payConf['business_num'];//商户编号
        $data['prdOrdNo']       = $order;//商户订单号必输
        $data['receivableType'] = "D00";//到账类型必输D00,T01,D01
        $data['prdAmt']         = "1";// 商品价格必输以分为单位13
        $data['prdName']        = "1";// 商品名称必输50

        if($payConf['pay_way'] == '1'){
            $data['payMode'] = '00020';//支付方式必输支付方式00020-银行卡00024-支付宝Wap
            $data['tranChannel'] = $bank;
            $data['prdDisUrl'] = "1";
            $data['prdShortName'] = "1";
            $data['prdDesc'] = "prdDesc";
            $data['pnum'] = "1";
        } else {
            self::$reqType = 'curl';
            self::$payWay = $payConf['pay_way'];
            self::$unit = '2';
            self::$httpBuildQuery = true;
            self::$resType = 'other';
            $data['payMode'] = $bank;
        }

        $signStr = self::getSignStr($data, false, true);
        $sign = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        $data['signData'] = $sign;//签名
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
        if(!isset($result['retCode']) && empty($result['retCode'])){
            $result['retCode'] = $result['code'];
            $result['retMsg']  = $result['serviceName'];
        }
        return $result;
    }
}