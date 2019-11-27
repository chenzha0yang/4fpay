<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xielupay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$unit = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$postType = true;
        self::$resType = 'other';

        $data = array(
            'mch_id'           => $payConf['business_num'],
            'nonce_str'        => self::randStr(16),
            'body'             => 'miaosu',
            'detail'           => '',
            'out_trade_no'     => $order,
            'total_fee'        => $amount * 100,
            'fee_type'         => 'CNY',
            'spbill_create_ip' => self::getClientIP(),
            'trade_type'       => $bank,
            'notify_url'       => $ServerUrl,
        );
        if ($payConf['pay_way'] == 2) {
            $data['limit_pay'] = 'no_credit';
        }
        $signStr      = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    public static function getRequestByType($data)
    {
        $xml = self::arrayToXml($data);
        return $xml;
    }

    public static function getQrcode($req)
    {
        $reqArr = self::xmlToArray($req);
        return $reqArr;
    }

    /**
     * @return array|false|string
     * 获取客户端真实IP
     */
    public static function getClientIP()
    {
        global $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";
        return $ip;
    }


    public static function arrayToXml($data)
    {
        if (!is_array($data) || count($data) <= 0) {
            return false;
        }
        $xml = "<xml>";
        foreach ($data as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * [将xml转为array]
     * @param  string 	$xml xml字符串或者xml文件名
     * @param  bool 	$isFile 传入的是否是xml文件名
     * @return array    转换得到的数组
     */
    public static function xmlToArray($xml,$isFile = false){
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        if($isFile){
            if(!file_exists($xml)) return false;
            $xmlStr = file_get_contents($xml);
        }else{
            $xmlStr = $xml;
        }
        $result= json_decode(json_encode(simplexml_load_string($xmlStr, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;
    }
}