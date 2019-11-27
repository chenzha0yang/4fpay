<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xinqidianpay extends ApiModel
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
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];

        $data['version']       = '1.0';
        $data['charSet']       = 'UTF8';
        $data['remark']        = 'zqd';
        $data['returnurl']     = $returnUrl;
        $data['merchno']       = $payConf['business_num'];
        $data['traceno']       = $order;
        $data['paytype']       = $bank;
        $data['commodityname'] = 'xqd';
        $data['total_fee']     = $amount * 100;
        $data['notifyurl']     = $ServerUrl;
        $signStr               = self::getSignStr($data,true,true);
        $data['sign']          = strtoupper(md5(md5($signStr . '&key=' . $payConf['md5_private_key'])));

        $post = json_encode($data);

        unset($reqData);
        return $post;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['respCode'] == "00") {
            $data['qrCode'] = $data['barUrl'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        $res['total_fee'] = $res['total_fee']/100;
        return $res;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $json = file_get_contents('php://input');  //获取POST流
        $data=json_decode($json,true);
      
        $sign    = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data,true,true);
        $signTrue  = strtoupper(md5(md5($signStr . '&key=' . $payConf['md5_private_key'])));
        if (strtoupper($sign) == strtoupper($signTrue)  && $data['respCode'] == '0000') {
            return true;
        }
        return false;
    }


}