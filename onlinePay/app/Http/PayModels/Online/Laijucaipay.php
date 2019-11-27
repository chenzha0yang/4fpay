<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Laijucaipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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

        $data                = [];
        $merId               = explode('@', $payConf['business_num'])[0];
        $sp                  = explode('@', $payConf['business_num'])[1];
        $data['version']     = '1.0';
        $data['charset']     = 'UTF-8';
        $data['merId']       = $merId;
        $data['orderTime']   = date('YmdHis');
        $data['transCode']   = 'T02002';
        $data['signType']    = 'MD5';
        $data['productName'] = 'SP:1301';
        if ($payConf['pay_way'] == 2) {
            $data['productName'] = $sp;
        }
        $data['transactionId'] = $order;
        $data['orderAmount']   = sprintf('%.2f', $amount);
        $data['payType']       = $bank;
        $data['bgUrl']         = $ServerUrl;
        $data['pageUrl']       = $returnUrl;
        $str                   = self::getSignStr($data, true, true);
        $data['sign']          = strtoupper(self::getMd5Sign($str . '&key=', $payConf['md5_private_key']));
        $data['mch_create_ip'] = '106.2.176.22';

        unset($reqData);
        return $data;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['signData'];
        unset($data['signData']);
        $str = self::getSignStr($data,true,true);
        $mySign = strtoupper(md5($str . '&key=' . $payConf['md5_private_key']));
        if ($mySign == $sign && $data['retCode'] == 'RC0000') {
//            $this->update_order($uid, $data['transactionId'], $data['orderAmount']);
            return true;
        } else {
            return false;
        }
    }
}