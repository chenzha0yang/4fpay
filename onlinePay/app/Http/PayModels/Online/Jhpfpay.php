<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jhpfpay extends ApiModel
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
        self::$unit = 2;
        if($payConf['pay_way'] != '1'){
            self::$reqType = 'curl';
            self::$payWay = $payConf['pay_way'];
            self::$resType = 'other';
        }
        $data['service'] = $bank;
        $data['version'] = '2.0';//版本号
        $data['charset'] = 'UTF-8';//字符集
        $data['sign_type'] = 'MD5';//签名方式
        $data['mch_id'] = $payConf['business_num'];//商户号
        $data['out_trade_no'] = $order;//订单
        $data['device_info'] = '';//设备号
        $data['body'] = 'vivo';//商品描述
        $data['attach'] = 'X20';//附加信息
        $data['total_fee'] = $amount*100;
        $data['mch_create_ip'] = '127.0.0.1';
        $data['notify_url'] = $ServerUrl;
        $data['time_start'] = '';//订单生成时间
        $data['time_expire'] = '';//订单超时时间
        $data['op_user_id'] = '';//操作员
        $data['goods_tag'] = '';//商品标记
        $data['product_id'] = '';//商品ID
        $data['nonce_str'] = self::randStr();//随机字符串
        $data['limit_credit_pay'] = '';//是否限制信用卡
        $signStr = self::getSignStr($data, false);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}", $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }
        //随机数
       public static function randStr($length = 30){ 
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789'; 
            $password = ''; 
            for ( $i = 0; $i < $length; $i++ ){ 
                $password .= $chars[ mt_rand(0, strlen($chars) - 1) ]; 
            } 
            return $password; 
        }

        public static function getQrCode($response)
        {
            $result = json_decode($response,true);
            if($result['pcwap'] == "0"){
                $result['pay_info'] = $result['pay_info'];
            }
            return $result;
        }

}