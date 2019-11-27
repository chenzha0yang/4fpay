<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Xhbpay extends ApiModel
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

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$postType = true;

        $data       = array(
            'merchno'    => $payConf['business_num'],
            'amount'     => $amount,
            'traceno'    => $order,
            'payType'    => $bank,
            'notifyUrl'  => $ServerUrl,
            'goodsName'  => 'chongzhi',
            'settleType' => '1',
            'remark'     => "pk",
        );

        ksort($data);
        $signStr = '';
        foreach ($data as $key => $val) {
            $signStr = $signStr . $key . "=" . iconv('UTF-8', 'GB2312', $val) . "&";
        }
        $data['signature']   = strtoupper(md5($signStr . $payConf['md5_private_key']));
        $data['c']           = $signStr . 'signature' . '=' . $data['signature'];

        unset($reqData);
        return $data;
    }

    public static function getRequestByType($data)
    {
        return $data['c'];
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $data   = iconv('GB2312', 'UTF-8', $response);
        $result = json_decode($data, true);
        if ($result['respCode'] == '00') {
            if (self::$isAPP) {
                $result['payurl'] = $result['barCode'];
            } else {
                $result['qrcode'] = $result['barCode'];
            }
        }
        return $result;
    }

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        header('Content-Type: text/html; charset=gb2312');
        $orderstatus = $data['status']; //支付状态
        if ($payConf['pay_type'] == 100) {
            unset($data['code']);
        }
        //签名操作
        ksort($data);
        $a = '';
        foreach ($data as $x => $x_value) {
            if ($x_value && $x != 'signature') {
                $a = $a . $x . "=" . $x_value . "&";
            }
        }
        $b = md5($a . $payConf['md5_private_key']);
        $d = strtoupper($b);
        $c = $data['signature'];
        if (strtolower($d) == strtolower($c) && $orderstatus == "1") {
            return true;
        } else {
            return false;
        }
    }
}