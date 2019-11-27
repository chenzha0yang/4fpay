<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yunweipay extends ApiModel
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

        $data['version']    = '1.0';//版本号
        $data['customerid'] = $payConf['business_num'];//合作方商户编号 
        $data['sdorderno']   = $order;//订单号
        $data['total_fee']  = number_format($amount,2,'.','');//交易金额,两位小数
        if((int)$payConf['pay_way'] === 1){
            $data['paytype']  = 'bank';//支付编号
            $data['bankcode'] = $bank;
        }else{
            $data['paytype']  = $bank;//支付编号
            $data['bankcode'] = '';
        }
        $data['notifyurl'] = $ServerUrl;//异步通知 URL
        $data['returnurl'] = $returnUrl;//同步通知
        $data['remark']    = $order;
        $data['get_code']  = '';
        $signval="version=".$data['version'].'&customerid='.$data['customerid'].'&total_fee='.$data['total_fee'].'&sdorderno='.$data['sdorderno'].'&notifyurl='.$data['notifyurl'].'&returnurl='.$data['returnurl'].'&'.$payConf['md5_private_key'];
        $data['sign'] = md5($signval);

        unset($reqData);
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $md5Str = "customerid=".$data['customerid'].'&status='.$data['status'].'&sdpayno='.$data['sdpayno'].'&sdorderno='.$data['sdorderno'].'&total_fee='.$data['total_fee'].'&paytype='.$data['paytype'].'&'.$payConf['md5_private_key'];
        $signTrue = md5($md5Str);
        
        if (strtoupper($sign) == strtoupper($signTrue) && $data['status'] == '1') {
            return true;
        }
        return false;
    }


}