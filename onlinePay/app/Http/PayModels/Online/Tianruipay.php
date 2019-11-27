<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tianruipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0;  //是否需要生成二维码 必加属性 2 3 4 5

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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';
        self::$httpBuildQuery = true;

        //TODO: do something

        $data = [
            'merchant_no'   => $payConf['business_num'],    //商户号
            'nonce_str'     => self::randstr(32),   //随机数
            'request_no'    => $order,                      //订单号
            'amount'        => $amount,                     //金额
            'request_time'  => time(),                      //订单生成时间
            'pay_channel'   => $bank,                       //支付类型
            'account_type'  => '1',                         //结算方式
            'ip_addr'       => '127.0.0.1',                 //终端客户IP地址
            'pay_notifyurl' => $ServerUrl,                  //异步通知地址
        ];
        if ($payConf['pay_way'] == 1) { //网银
            $data['pay_channel']   = "WYP";
            $data['bankname']      = $bank;
            $data['cardtype']      = "00";
            $data['clienttype']    = $payConf['message1'];
            $data['return_url']    = $returnUrl;
        }

        $signStr = self::getSignStr($data, true, true);
        $data['sign'] =  strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    /**
     * 二维码处理
     * @param $response
     * @return array
     */
    public static function getQrCode($response){
        //将json转换成数组
        $result = json_decode($response, true);
        //将二维数组转换成一维数组,赋值给语言包
        if($result['success'] == "true"){
            $data = [
                'bank_url'   => $result['data']['bank_url']
            ];
        }else{
            $data = [
                'status'  => $result['data']['status'],
                'message' => $result['data']['message']
            ];
        }

        return $data;
    }

    /**
     * 随机数
     * @param $length
     * @return string
     */
    public static function randstr($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $res = '';
        for ($i = 0; $i < $length; $i++) {
            $random = mt_rand(0, strlen($chars)-1);
            $res .= $chars{$random};
        }
        return $res;
    }
}