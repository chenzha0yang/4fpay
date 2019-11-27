<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Dianfupay extends ApiModel
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

        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $data = array(
            'merCode'      => $payConf['business_num'],   //商户号
            'orderId'       => $order,
            'title'     => 'goods',
            'payWay'      => $bank,
            'notifyUrl'     => $ServerUrl,
            'totalAmt'        => sprintf('%.1f',$amount),
            'version'    => 'v1.0.0',
            'tranTime'   => date('YmdHis'),
            'encodeType' => 'md5',
            'attach' => 'goods',
        );
        $signStr = "version={$data['version']}&orderId={$data['orderId']}&merCode={$data['merCode']}&payWay={$data['payWay']}&tranTime={$data['tranTime']}&totalAmt={$data['totalAmt']}&title={$data['title']}&encodeType={$data['encodeType']}&notifyUrl={$data['notifyUrl']}&attach={$data['attach']}";

        $data['signature'] = self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']); //MD5签名

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response) {
        echo $response;exit;
    }


    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign      = $data['sign']; //取SIGN
        $signStr = "orderId={$data['orderId']}&totalAmt={$data['totalAmt']}&status={$data['status']}&trandeNo={$data['trandeNo']}&encodeType={$data['encodeType']}&trandeTime={$data['trandeTime']}&attach={$data['attach']}";
        $mySign = self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']); //MD5签名
        if (strtolower($sign) == strtolower($mySign) && $data['status'] == 'Y') {
            return true;
        } else {
            return false;
        }
    }


}