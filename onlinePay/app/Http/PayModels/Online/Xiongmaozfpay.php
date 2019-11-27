<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xiongmaozfpay extends ApiModel
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

        self::$isAPP = true;
        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method = 'post';
        self::$resType = 'other';

        $data['mch_id']     = $payConf['business_num'];//商户号
        $data['out_trade_no']     = $order;//订单号
        $data['total_fee']       = $amount * 100;//订单金额
        $data['notify_url']   = $ServerUrl;
        $str='';
         foreach ($data as $v){
           $str.=$v;
        }
        $data['sign']            = md5($str . $payConf['md5_private_key']);
        $data['callback_url'] = $returnUrl;
        $data['trade_type']     = $bank;//银行编码
        $data['ip'] = self::getIp();

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['status'] == '1') {
            $data['payUrl'] = $data['data']['payurl'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        $res['total_fee']=$res['total_fee']/100;
        return $res;
    }

    public static function SignOther($type, $datas,$payConf)
    {
        $post = file_get_contents('php://input');
        $data  = json_decode($post,true);
        $sign = $data['sign'];
        $signStr=$data['result_code'].$data['mch_id'].$data['out_trade_no'].$data['transaction_id'].$data['total_fee'];
        $signTrue = md5($signStr .$payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['result_code'] == 'SUCCESS') {
            return true;
        }
        return false;
    }


}