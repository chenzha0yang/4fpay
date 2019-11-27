<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Gemapay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

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
//        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType = 'other';
        self::$isAPP = true;
        if($payConf['is_app'] == 1){
            $data['is_mobile'] = 1;
        }else{
            $data['is_mobile'] = 0;
        }
        $data = array(
            'pay_type'  => $bank,
            'mch_id'    => $payConf['business_num'],   //商户号
            'amount'    => $amount,
            'order_id'  => $order,
            'version'   => 'v1',
            'cb_url'    => $ServerUrl,
            'desc'      => $order,
        );
        //参数排序
        ksort( $data);
        //字符串拼接
        $signStr      = '';
        foreach ( $data as $k => $v ) {
            $signStr = $signStr . $k . $v ;
        }
        $signStr = $signStr . $payConf['md5_private_key'] ;
        $data['sign'] = sha1( $signStr );

        unset($reqData);
        return $data;
    }

    /**
     * 二维码链接地址处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){
        $responseData = json_decode($response,true);
        if($responseData['code'] == 'A00000' && $responseData['msg'] == 'success'){
            $data['pay_url'] = $responseData['data']['pay_url'];
        }else{
            $data['code'] = $responseData['code'];
            $data['msg']  = $responseData['msg'];
        }

        return $data;
    }
}