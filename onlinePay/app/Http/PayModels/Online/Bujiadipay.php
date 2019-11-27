<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Bujiadipay extends ApiModel
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


        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$postType = true;
        self::$resType  = 'other';

        $post = array();
        //TODO: do something
        $data['merchant_id'] = $payConf['business_num'];   //商户号
        $data['service'] = 'service.ffff.pay';
        $data['request_time'] = date('YmdHis');
        $data['nonce_str'] = rand(11111111111,9999999909999);
        $data['version'] = 'V1.0';


        $arr['order_no'] = $order;
        $arr['amount'] = sprintf('%.2f',$amount);
        $arr['notify_url'] = $ServerUrl;
        $arr['return_url'] = $returnUrl;
        $arr['currency'] = 'CNY';
        $arr['trade_code'] = $bank;
        $arr['client_ip'] = self::getIp();
        $arr['terminal_type'] = 'wap';
        $data['sign_type'] = 'MD5';

        $data['sign'] =strtoupper( md5('amount='.$arr['amount'].'&client_ip='.$arr['client_ip'].'&currency='.$arr['currency'].'&merchant_id='.$data['merchant_id'].'&nonce_str='.$data['nonce_str'].'&notify_url='.$arr['notify_url'].'&order_no='.$arr['order_no']
            .'&request_time='.$data['request_time'].'&return_url='.$arr['return_url'].'&service='.$data['service'].'&terminal_type='.$arr['terminal_type'].'&trade_code='.$arr['trade_code'].'&version='.$data['version'].'&key='.$payConf['md5_private_key']));
        $post= $data;
        $post['data']= $arr;


        $post['order_no'] = $arr['order_no'];
        $post['amount']     = $arr['amount'];
        unset($reqData);
        return $post;
    }

    public static function getRequestByType($post)
    {
         unset($post['amount']);
         unset($post['order_no']);

         $data = json_encode($post);
        return $data;
    }


    public static function getVerifyResult($request, $mod)
    {
        $a=$request->getContent();

        $data = json_decode($a,true);
        $data['data'] = json_decode($data['data'],true);
            $arr['amount']  = $data['data']['amount'];
            $arr['order_no']  = $data['data']['order_no'];
        return $arr;
    }


    public static function getQrCode($request)
    {
        $data = json_decode($request,true);
        if($data['resp_code'] == '1000'){
            $arr['url']  = $data['data']['pay_info'];

        }else{
            $arr['code']  = $data['resp_code'];
            $arr['msg']  = $data['resp_message'];
        }
        return $arr;
    }

    //回调处理
    public static function SignOther($mod, $post,$payConf)
    {
        $post     = file_get_contents("php://input");
        $arr     = json_decode($post, true);
        $arr['data']     = json_decode($arr['data'], true);
        $sign = $arr['sign'];
        $data['order_no'] = $arr['data']['order_no'];
        $data['trade_no'] = $arr['data']['trade_no'];
        $data['amount'] = $arr['data']['amount'];
        $data['pay_amount'] = $arr['data']['pay_amount'];
        $data['trade_code'] = $arr['data']['trade_code'];
        $data['status'] = $arr['data']['status'];
        $data['complete_time'] = $arr['data']['complete_time'];
        $data['merchant_id'] = $arr['merchant_id'];
        $data['response_time'] = $arr['response_time'];
        $data['nonce_str'] = $arr['nonce_str'];
        $data['version'] = $arr['version'];
        $signStr = self::getSignStr($data,true,true);
        $isSign = strtoupper(md5($signStr.'&key='.$payConf['md5_private_key']));
        if (strtoupper($sign) == $isSign && $data['status'] == '1000') {
            return true;
        }
        return false;
    }

}