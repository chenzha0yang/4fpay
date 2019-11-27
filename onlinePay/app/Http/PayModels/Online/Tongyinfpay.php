<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tongyinfpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //TODO: do something

        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$resType = 'other';
        self::$unit = 2;
        self::$postType = true;
        self::$isAPP = true;

        $ids               = explode('@', $payConf['business_num']);
        $data                    = [];
        $data['orgId']    = $ids[1]; //商户号
        $data['requestId']     = $order; //订单号
        if ($payConf['pay_way'] == 1) {
            $data['productId'] = '0500';
        } else {
            $data['productId'] = '0100';
        }
        date_default_timezone_set('Asia/Shanghai'); // 需要北京时区
        $data['timestamp']    = date('YmdHis', time());
        $data['dataSignType'] = '0';

        $busData['merno']      = $ids[0];
        $busData['bus_no']     = $bank;
        $busData['amount']     = $amount * 100;
        $busData['goods_info'] = $order;
        $busData['order_id']   = $order;
        $busData['notify_url'] = $ServerUrl;
        $busData['return_url'] = $returnUrl;
        $data['businessData']  = json_encode($busData);

        $signStr                 = self::getSignStr($data, true, true);
        $data['signData']     = strtoupper(self::getMd5Sign("{$signStr}", $payConf['md5_private_key']));
        $data['amount'] = $amount * 100;
        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $res = json_decode($response, true);
        if (isset($res['result'])) {
            $result    = json_decode($res['result'], true);
            $res['url'] = $result['url'];
        }
        return $res;
    }
    public static function getRequestByType($queryData)
    {
        unset($queryData['amount']);
        return $queryData;
    }

    public static function getVerifyResult ($request)
    {
        $data = $request->all();
        $data['amount'] = $data['amount'] / 100;
        return $data;
    }


    public static function signOther($model, $data, $payConf)
    {
        $sign    = $data['sign_data'];
        unset($data['sign_data']);
        $signArr = self::getSignStr($data);
        $mySign = md5($signArr . $payConf['md5_private_key']); #生成签名
        if ($sign == $mySign && $data['trade_status'] == '0') {
            return true;
        } else {
            return false;
        }
    }

}
