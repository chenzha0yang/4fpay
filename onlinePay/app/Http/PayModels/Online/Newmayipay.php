<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Newmayipay extends ApiModel
{

    public static $action = 'formPost';//提交方式

    public static $header = ''; //自定义请求头

    public static $pc = ''; //pc端直接跳转链接

    public static $imgSrc = '';

    public static $changeUrl = ''; //自定义请求地址

    public static $amount = 0; // callback  amount

    public static function getData($reqData, $payConf)
    {
        static::$action = 'curlPost';

        /**
         * 参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        $data                = [];
        $data['app_id']      = $payConf['business_num'];//商户ID
        $data['nonce_str']   = '23232';//银行类型
        $data['trade_type']  = $bank;
        $data['total_amount']  = $amount*100;
        $data['out_trade_no']  = $order;
        $data['trade_time']  = date('Y-m-d H:i:s');
        $data['notify_url']  = $ServerUrl;
        $data['user_ip']  = self::getIp();
        $signStr = self::getSignStr($data,true,true);
        $data['sign'] = strtoupper(md5($signStr."&key=".$payConf['md5_private_key'] ));
        $post = json_encode($data);
        self::$header = [
            'Content-Type: application/json; charset=utf-8'
        ];

        unset($reqData);
        return $post;
    }


    public static function getQrCode($result)
    {
        $res = json_decode($result, true);
        if ($res['code'] == '0000' && $res['sub_code'] == '0000') {
            if(isset($res['code_wap']) && !empty($res['code_wap'])){
                static::$result['appPath'] = $res['code_wap'];
                static::$result['qrCode'] = $res['code_wap'];
            }else{
                static::$result['appPath'] = $res['code_url'];
                self::$pc = true;
            }
        } else {
            static::$result['msg'] = $res['sub_msg'];
            static::$result['code'] = $res['sub_code'];
        }

    }


    public static function callback($request)
    {

        echo 'success';

        $json = $request->getContent();
        $data = json_decode($json,true);//code,sub_code=='0000'才有返回
        if($data['code'] == '0000' && $data['sub_code'] == '0000') {
            $payConf = static::getPayConf($data['out_trade_no']);
            if (!$payConf) return false;

            $sign = $data['sign'];
            unset($data['sign'],$data['code'],$data['msg'],$data['sub_code'],$data['sub_msg']);
            $signStr = self::getSignStr($data,true,true);
            $signTrue = md5($signStr."&key=".$payConf['md5_private_key'] );

            if (strtoupper($sign) == strtoupper($signTrue) && $data['trade_status'] == 1) {
                static::$amount = $data['total_amount'] / 100;  //注意转换成元
                return true;
            } else {
                static::addCallbackMsg($request);
                return false;
            }
        }
        return false;
    }

}