<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huiftpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    /**
     * @param array       $reqData 接口传递的参数
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
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];



        $data['Appid'] = $payConf['business_num'];
        $data['Version'] = '1.0.0.1';
        $data['Out_Trade_No'] = $order;
        $data['Total_Amount'] = sprintf('%.2f',$amount);
        $data['Strict_Price'] = '1';
        $data['Method'] = $bank;
        $data['Notify_Url'] = $ServerUrl;
        $data['Return_Url'] = $returnUrl;

        $signStr = self::getSignStr($data, true , true);
        $arr['TransCode'] = '130101';
        $arr['SignKey'] = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);
        $post = $arr;
        $post['Body'] = $data;

        $post['Out_Trade_No']  = $data['Out_Trade_No'];
        $post['Total_Amount']  = $data['Total_Amount'];
        unset($reqData);
        return $post;

    }


    public static function getQrCode($resp)
    {
        $result = json_decode($resp, true);

        if ($result['ResultCode'] == '0') {
            $result['url'] = $result['Body']['Pay_Url'];
        }
        return $result;
    }

    //回调处理
    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['SignKey'];
        unset($data['SignKey']);
        unset($data['Sign_Type']);
        $signStr = self::getSignStr($data, true , true);
        $isSign = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);

        if ($sign == $isSign && $data['Status_Msg'] == 'SUCCESS') {
            return true;
        }
        return false;
    }

}