<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jinzzfpay extends ApiModel
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

        self::$isAPP = true;
        self::$unit = 2;
        self::$reqType = 'fileGet';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];

        $data['merch_id']      = $payConf['business_num'];
        $data['version']      = "10";
        $data['signtype']      = "0";
        $data['nonce_str']      = '12fwf54DSFDS';
        $data['timestamp']      = time() * 1000;
        $data['detail']      = 'nick';
        $data['out_trade_no']      = $order;
        $data['money']      = (int)$amount*100;
        $data['channel']      = $bank;
        $data['callback_url']      = $ServerUrl;
        $data['ip'] = self::getIp();
        $signStr = self::getSignStr($data, true , true);
        $data['sign'] = strtoupper(md5($signStr.'&key='.$payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['result'] == 'SUCCESS') {
            $res = json_decode(base64_decode($data['body']),true);
        }else{
            $res['result']=$data['result'];
            $res['msg']=$data['msg'];
        }
        return $res;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        $arr['money'] =  $arr['money']/100;
        return $arr;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true , true);
        $signTrue = strtoupper(md5($signStr.'&key='.$payConf['md5_private_key']));
        if (strtoupper($sign) == $signTrue && $data['status'] =='1') {
            return true;
        }
        return false;
    }


}