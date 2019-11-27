<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Wangwangpay extends ApiModel
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

        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$postType = true;

        //TODO: do something
        $data = [
            'version'       => '1.0.0',                   //版本号
            'merId'         => $payConf['business_num'],  //商户号
            'orderNo'       => $order,                    //订单号
            'orderAmt'      => $amount,                   //金额
            'thirdChannel'  => $bank,                     //支付方式
            'remark1'       => 'remark1',
            'remark2'       => 'remark2',
            'notifyUrl'     => $ServerUrl,                //异步通知地址
            'callbackUrl'   => $returnUrl,
        ];
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
            $data['payprod'] = '10';
        }else{
            $data['payprod'] = '11';
        }

        $signStr = self::getSignStr($data,true,true);
        $data['sign'] = self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']);
        $result['data']     = json_encode($data);
        $result['orderNo']  = $data['orderNo'];
        $result['orderAmt'] = $data['orderAmt'];

        unset($reqData);
        return $result;
    }

    /**
     * 提交请求数据
     * @param $data
     * @return mixed
     */
    public static function getRequestByType($data)
    {
        return $data['data'];
    }

    /**
     * 返回结果 - 二维码处理
     * @param $res
     * @return mixed
     */
    public static function getQrCode($res){
        $responseData = json_decode($res,true);
        if ($responseData["respCode"] == "0000"){
            $data['jumpUrl'] = $responseData['jumpUrl'];
        }else{
            $data['respCode'] = $responseData['respCode'];
            $data['respMsg']  = $responseData['respMsg'];
        }
        return $data;
    }

}