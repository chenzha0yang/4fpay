<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Junanpay extends ApiModel
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
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
//        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';



        $data['pay_memberid'] = $payConf['business_num'];//商户号
        $data['pay_orderid'] = $order;//订单
        $data['pay_applydate'] = date("Y-m-d h:i:s");//订单时间
        $data['pay_bankcode'] = $bank;//支付类型
        $data['pay_notifyurl'] = $ServerUrl;
        $data['pay_callbackurl'] = $returnUrl;
        $data['pay_amount'] = $amount;
        $string = '';
        ksort($data);
        foreach ($data as $key => $value) {
            $string .= $key."=>".$value."&";
        }
        $string = $string."key=".$payConf['md5_private_key'];
        $data['pay_md5sign'] = strtoupper(md5($string));//签名
        $data['pay_requestIp'] = self::getClientIP();
        $data['tongdao'] = 'XTFAH5D0';//支付宝 通道编码
        $data['return_type'] = '1'; //0直接支付  1返回json数据

        unset($reqData);
        return $data;
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

    public static function getQrCode($response)
    {
        if ($data = self::isJson($response,true)) {
            return $data;
        } else {
            $arr['code'] = -1;
            $arr['msg'] = substr($response,0,7);
            return $arr;
        }
    }

    /**
     * 判断字符串是否为 Json 格式
     *
     * @param  string  $data  Json 字符串
     * @param  bool    $assoc 是否返回关联数组。默认返回对象
     *
     * @return array|bool|object 成功返回转换后的对象或数组，失败返回 false
     */
    public static function isJson($data = '', $assoc = false) {
        $data = json_decode($data, $assoc);
        if ($data && (is_object($data)) || (is_array($data) && !empty($data))) {
            return $data;
        }
        return false;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $ReturnArray = array(
            "memberid"   => $data["memberid"], // 商户ID
            "orderid"    => $data["orderid"], // 订单号
            "amount"     => $data["amount"], // 交易金额
            "datetime"   => $data["datetime"], // 交易时间
            "returncode" => $data["returncode"]
        );
        ksort($ReturnArray);
        reset($ReturnArray);
        $md5str = "";
        foreach ($ReturnArray as $key => $val) {
            $md5str = $md5str.$key."=>".$val."&";
        }
        $sign = strtoupper(md5($md5str."key=".$payConf['md5_private_key']));
        if (strtoupper($sign) == strtoupper($data['sign'])) {
            return true;
        }
        return false;
    }

}