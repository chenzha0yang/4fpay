<?php
/**
 * Created by PhpStorm.
 * User: JS-00036
 * Date: 2018/9/14
 * Time: 14:56
 */

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Juqianpay extends ApiModel
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
        $order = $reqData['order'];  //订单号
        $amount = $reqData['amount'];  //金额
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址
        self::$unit = 2;
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$postType = true;
        self::$payWay = $payConf['pay_way'];
        if($payConf['is_app'] == 1){
            self::$isAPP = true;
        }

        $data = array();
        $data['method'] = 'mbupay.alipay.sqm';#接口名称
        $data['version'] = '2.0.0';
        $data['appid'] = 'ca2018070910000146';#应用号
        $data['mch_id'] = $payConf['business_num']; //商户号
        $data['nonce_str'] = self::randStr(30); //随机字符串
        $data['body'] = "goods"; #商品名称
        $data['out_trade_no'] = $order; #商户订单号
        $data['total_fee'] = (string)($amount * 100);
        $data['notify_url'] = $ServerUrl; #通知地址
        $signStr = self::getSignStr($data, true, true); //排序拼接字符串
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}"."&key=", $payConf['md5_private_key'])); //加密
        //将数组转换成xml
        $xml = "<xml>";
        foreach ($data as $key => $val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        //将xml转换成数组
        $post = [];
        $post['xml'] = $xml;
        $post['out_trade_no'] = $data['out_trade_no'];
        $post['total_fee'] = $data['total_fee'];
        return $post;
    }

    /**
     * 提交数据
     * @param $data
     * @return mixed
     */
    public static function getRequestByType($data)
    {
        return $data['xml'];
    }

    /**
     * 二维码处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if($result['return_code'] == 'SUCCESS' && $result['result_code'] == "SUCCESS"){
            $data['s_code_url'] = $result['s_code_url'];
            $data['code_url'] = $result['code_url'];
        } else{
            $data['return_code'] = $result['return_code'];
            $data['return_msg'] = $result['return_msg'];
        }
        return $data;
    }
    /**
     * 回掉特殊处理
     * @param $mod
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($mod, $data, $payConf)
    {
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)));
        $sign = $result['sign'];
        unset($result['sign']);
        $signStr = self::getSignStr($result, true, true);
        $mysign = strtoupper(self::getMd5Sign("{$signStr}"."&key=", $payConf['md5_private_key'])); //加密
        if ($mysign == $sign && $result['result_code'] == 'SUCCESS' && $result['return_code'] == 'SUCCESS') {
            return true;
        }
        return false;
    }
}