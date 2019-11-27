<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Applepay extends ApiModel
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
     * @param array $reqData       接口传递的参数
     * @param array $payConf
     * @return array
     * @internal param null|string $user
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
        $data = [];
        $data['mer_id'] = $payConf['business_num'];//商户号
        $data['out_trade_no'] = $order;//订单
        $data['pay_type'] = $bank;//支付类型
        if($payConf['pay_way'] == '1'){
            $data['pay_type'] = '008';//支付类型
        }
        $data['goods_name'] = 'vivo';//商品描述
        $data['total_fee'] = $amount;
        $data['callback_url'] = $ServerUrl;
        $data['notify_url'] = $returnUrl;
        $data['nonce_str'] = self::randStr();//随机字符串
        $data['attach'] = $order;
        $signStr = "mer_id=".$data['mer_id']."&nonce_str=".$data['nonce_str']."&out_trade_no=".$data['out_trade_no']."&total_fee=".$data['total_fee'];
        $data['sign'] = self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']);
        self::$reqType = 'curl';
        self::$isAPP = true;
        self::$resType = 'other';
        self::$httpBuildQuery = true;
        unset($reqData);
        return $data;
    }

    public static function randStr($length = 30){ 
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789'; 
        $password = ''; 
        for ( $i = 0; $i < $length; $i++ ){ 
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ]; 
        } 
        return $password; 
    }

    public static function getQrCode($res){
        $result = json_decode($res,true);
        if($result['status'] == '0'){
            if(!empty($result['code_url'])){
                $data['qrcodeurl'] = $result['code_url'];
            }elseif (!empty($result['code_img_url'])) {
                $data['qrcodeurl'] = $result['code_img_url'];
            }            
        }
        return $data;
    }
}
