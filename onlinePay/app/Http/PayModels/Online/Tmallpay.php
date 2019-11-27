<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tmallpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$method = 'get';

        $data['app_id'] = $payConf['business_num'];//商户号
        $data['device'] = $bank;//银行编码
        $data['order_no'] = $order;//订单号
        $data['amount'] = $amount*100;//订单金额
        $data['return_url'] = $returnUrl;
        $data['notify_url'] = $ServerUrl;
        $signStr =  'app_id='.$data['app_id'].'&amount='.$data['amount'].'&order_no='.$data['order_no'].'&device='.$data['device'].'&app_secret='.$payConf['md5_private_key'].'&notify_url='.$data['notify_url'];
        $data['sign'] = md5($signStr);

        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['amount'])) {
            $arr['amount'] = $arr['amount'] / 100;
        }
        return $arr;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr =  'status='.$data['status'].'&amount='.$data['amount'].'&order_no='.$data['order_no'].'&order_status='.$data['order_status'].'&pay_time='.$data['pay_time'].'&app_secret='.$payConf['md5_private_key'];
        $signTrue = md5($signStr);
        
        if (strtoupper($sign) == strtoupper($signTrue)  && $data['status'] == '200') {
            return true;
        }
        return false;
    }


}