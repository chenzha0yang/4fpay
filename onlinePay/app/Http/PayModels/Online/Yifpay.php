<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yifpay extends ApiModel
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

        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';

        //TODO: do something
        $data['fxid'] = $payConf['business_num'];
        $data['fxddh'] = $order;
        $data['fxdesc'] = 'goodsName';
        $data['fxfee'] = sprintf('%.2f',$amount);
        $data['fxnotifyurl'] = $ServerUrl;
        $data['fxbackurl'] = $returnUrl;
        $data['fxpay'] = $bank;
        if ($payConf['pay_way'] == '1') {
            $data['fxpay'] = 'wyzf';
            $data['fxbankcode'] = $bank;
        }
        $data['fxsign'] = md5($data['fxid'] . $data['fxddh'] . $data['fxfee'] . $data['fxnotifyurl'] . $payConf['md5_private_key']);
        $data['fxip'] = self::getIp();

        unset($reqData);
        return $data;
    }

    /****
     * 返回信息处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['status'] == 1) {
            $result['payUrl'] = $result['payurl'];
        }
        return $result;
    }

    /**
     * 回调处理
     * @param $mod
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['fxsign'];
        $signTrue = md5($data['fxstatus'] . $data['fxid'] . $data['fxddh'] . $data['fxfee'] . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['fxstatus'] == "1") {
            return true;
        } else {
            return false;
        }
    }
}