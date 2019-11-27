<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Gaoshengpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //TODO: do something

        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$resType = 'other';
        self::$unit = 2;
        if ($payConf['is_app'] == '1') {
            self::$isAPP = true;
        }
        $data['appId'] = $payConf['business_num'];
        $data['outTradeNo'] = $order; //订单号
        $data['totalAmount'] = strval($amount*100); //金额
        $data['payType'] = $bank; //支付渠道
        $data['nonceStr'] = self::randStr();//随机字符串;
        $data['goodsInfo'] = 'goods';
        $data['notifyUrl'] = $ServerUrl;
        $data['returnUrl'] = $returnUrl; //异步通知地址
        $data['requestIp'] = self::getIp();
        ksort($data);
        $data['sign'] = strtoupper(md5(self::_encode($data).$payConf['md5_private_key']));

        $post  = array('reqData'=>self::_encode($data));

        $post['outTradeNo'] = $order; //订单号
        $post['totalAmount'] = strval($amount*100); //金额

        unset($reqData);
        return $post;
    }

    //随机数
    public static function randStr($length = 30)
    {
        $chars    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }


    public static function _encode($inPut){
        if(is_string($inPut)){
            $text = $inPut;
            $text = str_rePlace('\\', '\\\\', $text);
            $text = str_rePlace(
                array("\r", "\n", "\t", "\""),
                array('\r', '\n', '\t', '\\"'),
                $text);
            $text = str_rePlace("\\/", "/", $text);
            return '"' . $text . '"';
        }else if(is_array($inPut) || is_object($inPut)){
            $arr = array();
            $is_obj = is_object($inPut) || (array_keys($inPut) !== range(0, count($inPut) - 1));
            foreach($inPut as $k=>$v){
                if($is_obj){
                    $arr[] = self::_encode($k) . ':' . self::_encode($v);
                }else{
                    $arr[] = self::_encode($v);
                }
            }
            if($is_obj){
                $arr = str_rePlace("\\/", "/", $arr);
                return '{' . join(',', $arr) . '}';
            }else{
                $arr = str_rePlace("\\/", "/", $arr);
                return '[' . join(',', $arr) . ']';
            }
        }else{
            $inPut = str_rePlace("\\/", "/", $inPut);
            return $inPut . '';
        }
    }


    public static function getQrCode($response)
    {
        $res = json_decode($response, true);
        return $res;
    }


    public static function getVerifyResult ($request)
    {
        $res = $request->all();
        $data = json_decode($res['reqData'],true);
        $data['totalAmount'] = $data['totalAmount'] / 100;
        return $data;
    }


    public static function signOther($model, $data, $payConf)
    {
        $arr    = json_decode($data['reqData'],true);
        $sign = $arr['sign'];
        unset($arr['sign']);
        ksort($arr);
        $mysign = strtoupper(md5(self::_encode($arr).$payConf['md5_private_key']));
        if ($sign == $mysign && $arr['resultCode'] == '00') {
            return true;
        } else {
            return false;
        }
    }

}
