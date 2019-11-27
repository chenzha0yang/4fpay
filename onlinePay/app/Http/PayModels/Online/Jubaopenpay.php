<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jubaopenpay extends ApiModel
{

    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $signData = [];


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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$reqType = 'curl';
        self::$resType = 'json';
        self::$payWay = $payConf['pay_way'];

        $data = array();
        $data['pay_memberid'] = $payConf['business_num'];            //appid
        $data['pay_orderid'] = $order;          //商户平台订单号（需保证历史唯一性）
        $data['type_id'] = $bank;    //支付类型
        $data['pay_amount'] = $amount;                //金额
        $data['pay_date'] = date('Y-m-d H:i:s');                //订单时间
        $data['pay_notifyurl'] = $ServerUrl;                //异步通知地址

        $signStr = self::getSignStr($data, true, true); //true1 为空不参与签名，true2排序
        $data['pay_md5sign'] = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }


    public static function getVerifyResult($res){
        $resp = $res->getContent();
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($resp, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        self::$signData = $data;
        return $data;
    }


    /**
     * @param $model
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($model, $data, $payConf)
    {
        $xml = file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $xmlString = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $xmlarray   = json_decode(json_encode($xmlString),true);
        $sign = $xmlarray['pay_md5sign'];
        unset($xmlarray['pay_md5sign']);

        $signStr = self::getSignStr($xmlarray, true, true); //true1 为空不参与签名，true2排序
        $signTrue = strtoupper(md5($signStr . '&key=' .  $payConf['md5_private_key']));
        if (strtoupper($sign) == $signTrue  && $xmlarray['return_code'] == '000000') {
            return true;
        } else {
            return false;
        }
    }
}
