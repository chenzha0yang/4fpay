<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Snapay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl'];  // 异步通知地址
        $returnUrl = $reqData['returnUrl'];  // 同步通知地址

        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$method  = 'header';

        //TODO: do something

        if($payConf['pay_way'] == '3'){
            $payType = '1';
        }elseif($payConf['pay_way'] == '2'){
            $payType = '2';
        }elseif($payConf['pay_way'] == '1'){
            $payType = '3';
        }elseif($payConf['pay_way'] == '4'){
            $payType = '4';
        }elseif($payConf['pay_way'] == '9'){
            $payType = '5';
        }elseif($payConf['pay_way'] == '7'){
            $payType = '6';
        }elseif($payConf['pay_way'] == '6'){
            $payType = '7';
        }

        if($payType == '3') {
        self::$isAPP = true ;
       }

        $data['version'] = '1.5';
        $data['merchno'] = $payConf['business_num'];
        $data['goodsName'] = 'goodsName';
        $data['traceno'] = $order;
        $data['amount'] = sprintf('%.2f',$amount);
        $data['payType'] = $payType; //1、支付宝/2、微信；3、网银支付；4、QQ 支付； 5、快捷支付；6、京东钱包；7、银联钱包）
        $data['interfacetype'] = $bank; //（1：扫码； 3：App；4：WAP；5：服务窗；6：直连；7：宝转卡;8：卡转卡）
        $data['settleType'] = '1';
        $data['paybank'] = $bank;
        $data['notifyUrl'] = $ServerUrl;
        $data['callbackUrl'] = $returnUrl;
        ksort($data, SORT_NATURAL | SORT_FLAG_CASE);
        $signStr = self::getSignStr($data, true , false);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        $jsonData = json_encode($data);
        $header                   = [
            'Content-Type: application/json;charset=utf-8',
        ];
        $postData['data']         = $jsonData;
        $postData['httpHeaders']  = $header;
        $postData['traceno'] = $data['traceno'];
        $postData['amount']  = $data['amount'];
        unset($reqData);
        return $postData;

    }

    //回调处理
    public static function SignOther($mod, $data, $payConf)
    {
        $post     = file_get_contents("php://input");
        $data     = json_decode($post, true);
        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data, SORT_NATURAL | SORT_FLAG_CASE);
        $signStr = self::getSignStr($data, true , false);
        $isSign = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));

        if (strtoupper($sign) == $isSign && $data['payStatus'] == '2') {
            return true;
        }
        return false;
    }

}



