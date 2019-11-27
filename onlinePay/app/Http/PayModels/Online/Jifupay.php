<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jifupay extends ApiModel
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

        self::$reqType = 'header';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$isAPP = true;
        $data = array(
            'version'       => '100',
            'appId'         => $payConf['rsa_private_key'],     //应用ID
            'appSecret'     => $payConf['public_key'],          //应用密钥
            'merId'         => $payConf['business_num'],        //商户号
            'bizCode'       => $payConf['message1'],            //业务编号
            'serviceType'   => $bank,                           //服务类别
            'orderNo'       => $order,
            'orderPrice'    => $amount,
        );
        //拼接字符串
        $signStr = utf8_encode(self::getSignStr($data,true,true));
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=",$payConf['md5_private_key'])); //MD5签名
        $data['goodsName']  = 'VIP';
        $data['goodsTag']   = $order;
        $data['orderTime']  = date('Y-m-d H:i:s');
        $data['terminalIp'] = request()->ip();
        $data['notifyUrl']  = $ServerUrl;
        //请求路径
        $url = $reqData['formUrl'];
        //发起请求
        $result = self::send($url,$data);
        //组合新数组
        $post['json'] = $result;
        $post['orderNo'] = $data['orderNo'];
        $post['orderPrice'] = $data['orderPrice'];

        unset($reqData);
        return $post;
    }

    /**
     * 发起请求
     * @param $url
     * @param $data
     * @return bool|string
     */
    public static function send($url, $data)
    {
        $postData = http_build_query($data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postData,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }

    /**
     * 二维码、链接处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){
        $responseData = json_decode($response['data']['json'],true);
        if($responseData['retCode'] == '200' && $responseData['status'] == '01'){
            $data['pay_url'] = $responseData['bodyMap']['pay_url'];
        }else{
            $data['retCode'] = $responseData['retCode'];
            $data['message'] = $responseData['message'];
        }

        return $data;
    }
}