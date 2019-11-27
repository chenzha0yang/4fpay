<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Luzhoutdpay extends ApiModel
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

        self::$unit = 2;
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        $data = [
            'versionId'         => '1',                   //版本号
            'orderAmount'       => $amount*100,                   //金额
            'orderDate'         => date('YmdHis'),
            'currency'          => 'RMB',
            'transType'         => '0008',
            'asynNotifyUrl'     => $ServerUrl,                //异步通知地址
            'synNotifyUrl'      => $returnUrl,
            'signType'          => 'MD5',
            'merId'             => $payConf['business_num'],  //商户号
            'prdOrdNo'          => $order,                    //订单号
            'payMode'           => $bank,                     //支付方式
            'receivableType'    => 'D00',
            'prdAmt'            => $amount*100,
            'prdName'           => 'VIP',
        ];

        $signStr = self::getSignStr($data,false,true);
        $data['signData'] = self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    /**
     * 返回结果 - 二维码处理
     * @param $res
     * @return mixed
     */
    public static function getQrCode($res){
        $result = json_decode($res,true);
        if ($result["retCode"] == "1"){
            $data['qrcode'] = $result['qrcode'];
            //$data['htmlText'] = $result['htmlText'];
        }else{
            $data['retCode'] = $result['retCode'];
            $data['retMsg']  = $result['retMsg'];
        }
        return $data;
    }

}