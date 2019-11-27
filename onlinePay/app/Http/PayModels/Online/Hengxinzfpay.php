<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Hengxinzfpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        $payInfo = explode('@', $payConf['business_num']);
        $data['mchId']      = $payInfo[0];
        $data['appId']      = $payInfo[1];
        $data['mchOrderNo'] = $order;
        $data['amount']     = $amount * 100;
        $data['productId']  = $bank;
        $data['currency']   = 'cny';
        $data['subject']    = 'zhifu';
        $data['body']       = 'zhifu';
        //$data['clientIp']   = self::getIp();
        $data['notifyUrl']  = $ServerUrl;

        $str          = self::getSignStr($data,true,true);
        $data['sign'] = strtoupper(md5($str . '&key=' . $payConf['md5_private_key']));

        $res           = 'params=' . json_encode($data);
        self::$reqType = 'curl';
        self::$unit    = 2;
        self::$resType = 'other';
        self::$method  = 'header';
        self::$payWay   = $payConf['pay_way'];
        unset($reqData);
        $post['httpHeaders'] = [];
        $post['data']        = $res;
        $post['mchOrderNo']  = $data['mchOrderNo'];
        $post['amount']      = $data['amount'];
        return $post;
    }


    public static function getQrCode($result)
    {
        $resp = json_decode($result, true);
        if (isset($resp['retCode']) && $resp['retCode'] == 'SUCCESS') {
            $resp['codeUrl'] = $resp['payParams']['codeUrl'];
        }
        return $resp;
    }

    public static function getVerifyResult(Request $request, $mod)
    {
        $data = $request->all();
        $data['amount'] = $data['amount'] / 100;
        return $data;
    }

    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data);
        $signTrue = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key']));
        if (strtoupper($sign) == strtoupper($signTrue) && $data['status'] == 2){
            return true;
        } else {
            return false;
        }
    }
}