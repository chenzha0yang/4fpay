<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Wangzhepay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

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
        $data['appid'] = $payConf['business_num']; // 易信支付商户ID
        $data['out_trade_no'] = $order; // 订单号
        $data['money'] = $amount; //金额
        $data['title'] = 'phone'; //商品名称
        if($payConf['pay_way'] == '1'){
            $data['paytype'] = '6'; 
            //支付方式，1支付宝，2微信支付，3网银支付，4QQ支付
            $data['bankcode'] = $bank;
        }else{
            $data['paytype'] = $bank; //支付方式，1支付宝，2微信支付，3网银支付，4QQ支付
        }
        
        $data['notify_url'] = $ServerUrl; //回调URL，支付成功后将推送到此URL
        $data['return_url'] = $returnUrl; //跳转URL，支付成功后将跳转到此URL
        $sign = Strtolower(md5($data['appid'].$data['out_trade_no'].$data['money'].$data['paytype'].$payConf['md5_private_key']));
        $data['sign'] = $sign;

        unset($reqData);
        return $data;
    }

    /**回调处理
     * @param $request
     * @return mixed
     */
    public static function getVerifyResult($request)
    {
        $data = json_decode(base64_decode($request['data'], true),true);
        $res['money'] = sprintf("%.2f",$data['money']);
        $res['out_trade_no'] = $data['out_trade_no'];
        return $res;
    }
}