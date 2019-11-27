<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Yunszfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $pcWap = 2;

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
        self::$reqType        = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$unit           = 2;
        self::$method       = 'header';
        self::$resType = 'other';


        $data['version'] = 'v1.0';
        $data['userId'] = $payConf['business_num'];//商户号
        $data['type'] = $bank;//银行编码
        $data['requestNo'] = $order;//订单号
        $data['amount'] = $amount*100;//订单金额
        $data['redirectUrl'] = $returnUrl;
        $data['callBackURL'] = $ServerUrl;
        $signStr =  self::getSignStr($data, true, true);
        $data['sign'] = strtolower(md5($signStr . "&key=" . $payConf['md5_private_key']));

        $postData['data']     = json_encode($data);
        $postData['httpHeaders']   = array('Content-Type: application/json; charset=utf-8');
        $postData['requestNo'] = $order;
        $postData['amount'] = $amount*100;
        unset($reqData);
        return $postData;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['status'] == '1') {
            if (self::$isAPP) {
                $result['payurl'] = $result['payUrl'];
            } else {
                $result['qrcode'] = $result['payUrl'];
            }
        }
        return $result;
    }



    //回调金额处理
    public static function getVerifyResult(Request $request)
    {
        $res                = $request->getContent();
        $data = json_decode($res, true);
        $data['amount']     = $data['amount'] / 100;
        return $data;
    }

    //回调处理
    public static function SignOther($type, $datas, $payConf)
    {
        $post = file_get_contents('php://input');
        $data = json_decode($post,true);
        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['attachData']);
        $signStr =  self::getSignStr($data, true, true);
        $signTrue = strtolower(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        if (strtolower($sign) == $signTrue && $data['status'] == 3) {
            return true;
        } else {
            return false;
        }
    }
}
