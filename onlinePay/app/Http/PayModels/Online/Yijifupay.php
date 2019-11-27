<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yijifupay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something

        $data = array(
            "oid_partner" => $payConf['business_num'],
            "no_order"    => 'T' . $order,
            "money_order" => sprintf('%.2f', $amount),
            "time_order"  => date("YmdHis"),
            "pay_type"    => $bank,
            "notify_url"  => $ServerUrl,
            "return_url"  => $returnUrl,
            'user_id'     => $reqData['username'],
            'sign_type'   => 'MD5',
            'name_goods'  => 'goods'

        );
        if ($payConf['pay_way'] == 1) {
            $data['pay_bankcode'] = '11';
            $data['bank_type']    = $bank;
        }
        $md5str       = self::getSignStr($data, true, true);
        $sign         = self::getMd5Sign($md5str, $payConf['md5_private_key']);
        $data["sign"] = $sign;
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        $data['no_order'] = ltrim($res['no_order'],'T');
        $data['money_order'] = $res['money_order'];
        return $data;
    }

    //回调处理
    public static function SignOther($type, $res, $payConf)
    {
        $result = file_get_contents('php://input');
        $data = json_decode($result, true);
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr             = self::getSignStr($data, true, true);
        $mysign              = self::getMd5Sign($signStr, $payConf['md5_private_key']);
        if (strtolower($sign) == strtolower($mysign) && $data['result_pay'] == "SUCCESS") {
            return true;
        } else {
            return false;
        }
    }
}