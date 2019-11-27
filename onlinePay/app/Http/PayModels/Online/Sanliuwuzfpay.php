<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Sanliuwuzfpay extends ApiModel
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

        self::$unit=2;
        self::$reqType= 'curl';
        self::$payWay=$payConf['pay_way'];
        self::$isAPP=true;
        self::$resType='other';
        //TODO: do something
        $data['merchID'] = $payConf['business_num'];
        $data['timestamp'] =self::msectime();
        $data['payType'] = $bank;
        $data['merchOrderNo'] = $order;
        $data['amount'] = $amount*100;
        $data['notifyUrl'] = $ServerUrl;

       $signStr=self::getSignStr($data,true,true,false);
        $data['sign']          = md5($signStr.$payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
     if ($result['msg']=='SUCCESS'){
        $result['payUrl']=$result['data']['payUrl'];
       }
        return $result;

    }


    public static function getVerifyResult($request)
    {
        $data                = $request->all();
        $data['amount']   = $data['data']['amount'] / 100;
        $data['merchOrderNo']=$data['data']['merchOrderNo'];
        return $data;
    }
    //返回当前的毫秒时间戳
    public static function msectime() {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }

    /**
     * @param $type    string 模型名
     * @param $data    array  回调数据
     * @param $payConf array  商户信息
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $arr=$data['data'];
        unset($data['data']);
        $str= array_merge ($data,$arr);
        $signStr=self::getSignStr( $str,true,true,false);
        $mysign    = md5($signStr.$payConf['md5_private_key']);
        if(strtolower($mysign) == strtolower($sign) && $data['code']=='1' ){
            return true;
        }else{
            return false;
        }
    }
}