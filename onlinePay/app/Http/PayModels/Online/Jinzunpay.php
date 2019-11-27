<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Jinzunpay extends ApiModel
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

        //TODO: do something
        if ($bank == '8007' || $bank == '8015' || $bank == '8003') {
            self::$resType = 'other';
        }
        self::$unit     = 2;
        self::$reqType  = 'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$isAPP    = true;
        self::$postType = true;

        $pid                = explode("@", $payConf['business_num']);
        $data['mchId']      = $pid['0'];//商户ID
        $data['appId']      = $pid['1'];//应用ID
        $data['mchOrderNo'] = $order;
        $data['currency']   = 'cny';//币种
        $data['amount']     = $amount * 100;//价格 (单位分)
        $data['clientIp']   = self::getIp();
        $data['device']     = 'WEB';
        $data['notifyUrl']  = $ServerUrl;//异步通知地址
        $data['returnUrl']  = '';
        $data['subject']    = 'honor';
        $data['body']       = 'i9';
        $data['productId']  = $bank;//支付方式
        $data['extra']      = '';
        $signStr            = self::getSignStr($data, true, true);
        $data['sign']       = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        $post['params']     = json_encode($data);
        $post['mchOrderNo'] = $order;
        $post['amount']     = $amount * 100;

        unset($reqData);
        return $post;
    }

    public static function getVerifyResult(Request $request, $mod)
    {
        $data = $request->all();
        if(isset($data['amount'])) {
            $data['amount'] = $data['amount'] / 100;
        }
        return $data;
    }

    public static function getRequestByType($data)
    {
        $post = $data;
        unset($post['mchOrderNo']);
        unset($post['amount']);
        $params = http_build_query($post);
        return $params;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['retCode'] == 'SUCCESS') {
            echo $result['payParams']['payUrl'];
            exit();
        }
        return $result;

    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);
        $mySign  = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));

        if (strtoupper($sign) == $mySign) {
            return true;
        }
        return false;
    }
}