<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yunjupay extends ApiModel
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

        self::$method  = 'get';
        self::$unit    = 2;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        $data = array(
            'out_trade_no'  => $order,
            'mer_id'        => $payConf['business_num'],   //商户号
            'goods_name'    => 'vip',
            'total_fee'     => $amount * 100,
            'callback_url'  => $returnUrl,
            'notify_url'    => $ServerUrl,
            'attach'        => 'vip',
            'nonce_str'     => self::randStr(30),
            'pay_type'      => $bank,
            'player_id'     => $order,
            'player_ip'     => request()->ip(),
        );

        $signStr      = 'mer_id='.$data['mer_id'].'&nonce_str='.$data['nonce_str'].'&out_trade_no='.$data['out_trade_no'].'&total_fee='.$data['total_fee'];
        $data['sign'] = self::getMd5Sign("{$signStr}&key=",$payConf['md5_private_key']); //MD5签名

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response){
        $responseData = json_decode($response,true);
        if($responseData['status'] == '0'){
            $data['code_url'] = $responseData['code_url'];
//            $data['code_img_url'] = $responseData['code_img_url'];
        }else{
            $data['status'] = $responseData['status'];
            $data['msg'] = $responseData['msg'];
        }

        return $data;
    }
}