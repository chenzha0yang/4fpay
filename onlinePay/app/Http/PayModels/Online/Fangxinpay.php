<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Fangxinpay extends ApiModel
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

        $data['pay_memberid'] = $payConf['business_num'];

        $data['pay_amount']    = sprintf('%.2f', $amount);
        $data['pay_orderid']   = $order;
        $data['pay_applydate'] = date('Y-m-d H:i:s', time());
        if ((int)$payConf['pay_way'] === 1) {
            $data['pay_bankcode'] = $bank;
            $data['cashier']      = '5';
        } else {
            $data['pay_bankcode'] = '0';
            $data['cashier']      = $bank;
        }
        $data['tongdao']         = '0';
        $data['pay_notifyurl']   = $ServerUrl;
        $data['pay_callbackurl'] = $returnUrl;
        $signStr                 = '';
        $signKey                 = ['pay_memberid', 'pay_orderid', 'pay_amount', 'pay_applydate', 'pay_bankcode', 'pay_notifyurl', 'pay_callbackurl'];
        ksort($data);
        foreach ($data as $key => $value) {
            if (in_array($key, $signKey, true)) {
                $signStr .= $key . '=>' . $value . '&';
            }
        }
        $data['pay_md5sign'] = strtoupper(md5($signStr . 'key=' . $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signData = ['memberid','orderid','amount','datetime','returncode'];
        $signStr  = '';
        ksort($data);
        foreach ($data as $key => $value){
            if(in_array($key,$signData,true)){
                $signStr .= $key.'=>'.$value.'&';
            }
        }
        $signTrue = strtoupper(md5($signStr . 'key=' . $payConf['md5_private_key']));
        if (strtoupper($sign) == strtoupper($signTrue) && $data['returncode'] == '00') {
            return true;
        }
        return false;
    }


}