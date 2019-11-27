<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Newtjpay extends ApiModel
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

    public static $myBank = '';

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

        $data['mchno']   = $payConf['business_num']; //商户编号
        $data['money']   = $amount; //订单金额
        $data['orderno'] = $order; //订单号
        if ($payConf['pay_way'] == '1') {
            $data['bankid']  = $bank;
            $data['payType'] = 'unionpay_bank';
        } else {
            $data['payType'] = $bank; //支付类型
        }
        $data['notifyUrl'] = $ServerUrl; //异步地址
        $data['returnUrl'] = $returnUrl; //同步地址
        $data['attach']    = time(); //该参数存在，回调通知时会原样返回给商户
        $signStr           = self::getSignStr($data, true, true);
        $signSource        = $signStr . '&key=' . $payConf['md5_private_key'];
        $data['sign']      = md5($signSource);
        if ($payConf['pay_way'] == '1' || $payConf['pay_way'] == '9') {
            self::$reqType = 'form';
        } else {
            self::$payWay  = $payConf['pay_way'];
            self::$reqType = 'curl';
            self::$resType = 'other';
            self::$isAPP = true;
        }
        self::$myBank = $data['payType'];
        unset($reqData);
        return $data;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $res = json_decode($response, true);
        if ($res['status'] == '1') {
            if (self::$myBank == 'unionpay' || self::$myBank == 'unionpay_bank' || self::$myBank == 'qqpay_wap' || self::$myBank == 'wxpay_wap' || self::$myBank == 'alipay_wap') {
                $url = $res['url'];
            }   else if (self::$myBank == 'unionpay_pc') {
                $url = !isset($res['code_img']) ? $res['code_img2'] : $res['code_img'];
            } else {
                $url = !isset($res['code_img2']) ? $res['url'] : $res['code_img2'];
            }
            $res['pay_url'] = $url;
            $res['msg'] = !isset($res['msg']) ? '请求成功' : $res['msg'];
        } else {
            $res['status'] = $res['status'];
            $res['msg'] = !isset($res['msg']) ? '请求失败' : $res['msg'];
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
        $sign      = $data['sign']; //取SIGN
        unset($data['sign']);
        $md5str  = self::getSignStr($data, true, true);
        $md5Key  = $md5str . "&key=" . $payConf['md5_private_key'];
        $signStr = md5($md5Key);
        if ($sign == $signStr && $data['result'] == '1') {
            return true;
        } else {
            return false;
        }
    }
}