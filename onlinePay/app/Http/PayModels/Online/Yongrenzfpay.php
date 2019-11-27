<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Yongrenzfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $resData = [];

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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        $code = [
            '2' => 'WXWAP',
            '3' => 'ZFBWAP',
            '4' => 'QQWAP',
            '6' => 'UNIONWAP',
            '7' => 'JDWAP',
        ];
        $data['apiName']      = $bank;
        $data['apiVersion']   = '1.0.0.0';
        $data['platformID']   = $payConf['business_num'];
        $data['merchNo']      = $payConf['business_num'];
        $data['orderNo']      = $order;
        $data['tradeDate']    = date('Ymd');
        $data['amt']          = sprintf('%.2f', $amount);
        $data['merchUrl']     = $ServerUrl;
        $data['merchParam']   = $amount;
        $data['tradeSummary'] = 'goodsName';

        if (!self::$isAPP) {
            // 扫码
            $data['overTime'] = 300;
            $data['customerIP'] = self::getIp();

        }

        $str = self::getSignStr($data,true,false);
        $data['signMsg'] = md5($str . $payConf['md5_private_key']);

        if ($payConf['pay_way'] == '1') {
            $data['apiName'] = 'WEB_PAY_B2C';
            $data['bankCode'] = $bank;
        } elseif ($payConf['pay_way'] != '1' && self::$isAPP) {
            // h5 且不是网银
            $data['bankCode'] = $code[$payConf['pay_way']];
        }

        if (!self::$isAPP && $payConf['pay_way'] != '1') {
            // 扫码
            //TODO: do something
            self::$reqType = 'curl';
            self::$payWay  = $payConf['pay_way'];
            self::$httpBuildQuery  = true;
            self::$resType = 'other';
        }

        unset($reqData);
        return $data;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if($result['resultCode'] == '00'){
            $url = base64_decode($result['code']);
            $res['qrcode'] = $url;
        } else {
            $res['code'] = $result['resultCode'];
            $res['msg'] = $result['message'];
        }
        return $res;
    }


    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $str = "apiName={$data['apiName']}&notifyTime={$data['notifyTime']}&tradeAmt={$data['tradeAmt']}&merchNo={$data['merchNo']}&merchParam={$data['merchParam']}&orderNo={$data['orderNo']}&tradeDate={$data['tradeDate']}&accNo={$data['accNo']}&accDate={$data['accDate']}&orderStatus={$data['orderStatus']}";
        $mySign = strtoupper(md5($str . $payConf['md5_private_key']));
        if (strtoupper($data['signMsg']) == $mySign && $data['orderStatus'] == '1') {
            return true;
        } else {
            return false;
        }
    }
}