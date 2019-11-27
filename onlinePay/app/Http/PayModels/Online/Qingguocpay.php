<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Qingguocpay extends ApiModel
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
//        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
//        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$unit    = 2;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$postType = true;
        if($payConf['is_app'] == 1){
            self::$isAPP = true;
        }
        $data = array(
            'appid'  => $payConf['business_num'],               //商户号
            'method' => $payConf['message1'],    	            //接口名称
            'data' => array(
                'store_id'     => '', 	  	                    //多个门店可选传，如不传系统默认已创建最早的门店为主
                'total'        => $amount,                      //总金额 单位:分
                'nonce_str'    => self::randStr(16),       //随机字符串 字符范围a-zA-Z0-9
                'body'         => 'VIP',                        //商品名称
                'out_trade_no' => $order,                       //订单号
            ),
        );
        if($payConf['pay_way'] == 2 && $payConf['message1'] == 'wx_micropay'){  //微信条码
            $data['data']['auth_code'] = $bank;
        }
        if($payConf['pay_way'] == 3 && $payConf['message1'] == 'ali_micropay'){  //支付宝条码
            $data['data']['auth_code'] = $bank;
        }
        $signStr = self::getSignStr($data['data'],true,true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=",$payConf['md5_private_key']));
        $jsonData = json_encode($data);
        //组合新数组
        $post = [];
        $post['json'] = $jsonData;
        $post['out_trade_no'] = $data['data']['out_trade_no'];
        $post['total'] = $data['data']['total'];

        unset($reqData);
        return $post;
    }

    /**
     * 提交数据
     * @param $data
     * @return mixed
     */
    public static function getRequestByType($data)
    {
        return $data['json'];
    }

    /**
     * 二维码、链接处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){
        $responseData = json_decode($response,true);
        if($responseData['code'] == 100 && $responseData['data']['result_code'] == '0000'){
            $data['code_url'] = $responseData['data']['code_url'];
        }else{
            $data['code']     = $responseData['code'];
            $data['msg']      = $responseData['msg'];
        }

        return $data;
    }

}