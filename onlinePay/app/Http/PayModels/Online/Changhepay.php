<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Changhepay extends ApiModel
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
     * @param array $reqData 接口传递的参数
     * @param array $payConf
     * @return array
     */
    public static function getAllInfo($reqData, $payConf)
    {
        /**
         * 参数赋值，方法间传递数组
         */
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址

        self::$isAPP = true;

        if ($payConf['pay_way'] == 1) {
            $bank = '105'; //网银支付
        }
        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';
        self::$postType = true;

        $data = [
            'merNum' => $payConf['business_num'], //商户号
            'payType' => $bank, //请求接口名称
            'orderNum' => $order, //版本号
            'orderMoney' => $amount, //签名
            'noticeUrl' => $ServerUrl
        ];

        $signStr = "payType={$data['payType']}&merNum={$data['merNum']}&orderMoney={$data['orderMoney']}&orderNum={$data['orderNum']}&noticeUrl={$data['noticeUrl']}";
        $data['sign'] = self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }

    public static function getRequestByType($data)
    {
        return json_encode($data);
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['status'] == '0') {
            $result['qrcode'] = $result['data']['payUrl'];
        }
        return $result;
    }
}