<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Yilongpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$resType        = 'other';
        self::$httpBuildQuery = true;
        //TODO: do something

        $data = [
            'pay_version'       => '2.0',
            'pay_amount'        => sprintf('%.2f',$amount),
            'pay_bankcode'      => $bank,
            'pay_scene'         => $payConf['message1'],
            'pay_memberid'      => $payConf['business_num'],
            'pay_orderid'       => $order,
            'pay_notifyurl'     => $ServerUrl,
            'pay_callbackurl'   => $returnUrl,
            'pay_rand'          => self::randStr(32),
        ];
        $signStr                 = self::getSignStr($data, true ,false);
        $data['pay_sign']        = md5(md5($signStr.'&pay_key='.$payConf['md5_private_key']));
        $data['pay_returntype']  = 'JSON';
        $jsonData                = json_encode($data);
        $postData['body']        = base64_encode($jsonData);
        $postData['pay_orderid'] = $data['pay_orderid'];
        $postData['pay_amount']  = $data['pay_amount'];

        unset($reqData);
        return $postData;
    }

    /**
     * 返回结果 - 二维码处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){
        $backData = json_decode($response, true);
        if ($backData['return_state'] == 'ok') {
            $postData['return_payurl'] = $backData['return_payurl'];
        } else {
            $postData['string'] = $backData['string'];
            $postData['msg']    = $backData['msg'];
        }

        return $postData;
    }

    /**
     * 回掉特殊处理
     * @param $model
     * @param $data - 返回的数据 - array
     * @param $payConf
     * @return bool
     */
    public static function SignOther($model, $data, $payConf)
    {
        //获取数组中的数据 - 签名
        $callbackData   = [
            'amount'    => $data['amount'],
            'moeny'     => $data['moeny'],
            'bankcode'  => $data['bankcode'],
            'scene'     => $data['scene'],
            'memberid'  => $data['memberid'],
            'orderid'   => $data['orderid'],
            'rand'      => $data['rand'],
            'ontype'    => $data['ontype'],
            'key'       => $payConf['md5_private_key'],
        ];
        //签名
        $signStr         =  self::getSignStr($callbackData, true ,false);
        $sign            =  md5(md5($signStr));
        if($data['sign'] == $sign && $data['ontype'] == '102'){
            return true;
        }else{
            return false;
        }
    }

}