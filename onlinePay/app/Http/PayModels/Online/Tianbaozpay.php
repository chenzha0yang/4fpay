<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tianbaozpay extends ApiModel
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

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method  = 'header';

        $data['mid']         = $payConf['business_num'];
        $data['oid']         = $order;
        $data['amt']         = $amount;
        $data['way']         = $bank;
        $data['back']        = $returnUrl;
        $data['notify']      = $ServerUrl;
        $data['remark']      = $amount;
        $data['sign']        = md5($data['mid'] . '|' . $data['oid'] . '|' . $data['amt'] . '|' . $data['way'] . '|' . $data['back'] . '|' . $data['notify'] . '|' . $payConf['md5_private_key']);
        $post['data']        = json_encode($data);
        $post['httpHeaders'] = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($post['data'])
        ];
        $post['oid']         = $data['oid'];
        $post['amt']         = $data['amt'];
        unset($reqData);
        return $post;
    }

    //回调金额化分为元
    public static function getVerifyResult(Request $request, $mod)
    {
        $post     = file_get_contents("php://input");
        $data     = json_decode($post, true);
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $post     = file_get_contents("php://input");
        $data     = json_decode($post, true);
        $signTrue = md5($data['mid'] . '|' . $data['oid'] . '|' . $data['amt'] . '|' . $data['way'] . '|' . $data['code'] . '|' . $payConf['md5_private_key']);
        if (strtoupper($data['sign']) == strtoupper($signTrue) && $data['code'] == '100') {
            return true;
        }
        return false;
    }
}