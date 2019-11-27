<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Wantzfvpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $UserName = '';
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
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['pay_way'] != 6 && $payConf['pay_way'] != 7) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method  = 'header';
        self::$resType = 'other';
        $payInfo = explode('@', $payConf['business_num']);
        if(!isset($payInfo[1])){
            echo '商户号请填入格式为：商户号@应用编号 的商户信息';exit;
        }
        $data['merchant_code'] = $payInfo[0];//商户号
        $data['appno_no'] = $payInfo[1];
        $data['pay_type'] = $bank;//银行编码
        if($payConf['pay_way'] == 1){
            $data['bank_code'] = $bank;
            $data['pay_type'] = 'wangguan';
        }
        $data['order_no'] = $order;//订单号
        $data['order_amount'] = sprintf('%.2f',$amount);//订单金额
        $data['order_time'] = date('YmdHis',time());
        $data['product_name'] = 'test';
        $data['product_code'] = '111';
        $data['user_no'] = self::$UserName;
        $data['return_url'] = $returnUrl;
        $data['notify_url'] = $ServerUrl;
        if(self::$payWay == 2 && self::$isAPP){
            $data['merchant_ip'] = self::getIp();
        }

        $signStr =  self::getSignStr($data,true,true);
        $sign = strtoupper(md5($signStr . "&key=" . $payConf['md5_private_key']));
        $post['transdata'] = urlencode(json_encode($data));
        $post['signtype'] = 'MD5';
        $post['sign'] = urlencode($sign);

        $posts['order_no']       = $data['order_no'];
        $posts['order_amount']      = $data['order_amount'];
        $posts['data']        = json_encode($post);
        $posts['httpHeaders'] = [
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen(json_encode($post)),
        ];

        unset($reqData);
        return $posts;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['payment']) {
            if(isset($result['html'])){
                echo $result['html'];die;
            }else if (isset($result['payUrl'])) {
                $res['payUrl'] = $result['payUrl'];
            } else {
	            $res['status'] = $result['payment'];
	            $res['message'] = $result['message'];
            }
        } else {
	        $res['status'] = $result['payment'];
	        $res['message'] = $result['message'];
        }
        return $res;
    }

    //回调金额化分为元
    public static function getVerifyResult($request, $mod)
    {
        $res = $request->getContent();
        $data = json_decode($res, true);
        $transdata = json_decode(urldecode($data['transdata']),true);
        return $transdata;
    }


    /**
     * @param $type
     * @param $datas
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $datas, $payConf)
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $transdata = json_decode(urldecode($data['transdata']),true);
        $sign = urldecode($data['sign']);
        $signStr =  self::getSignStr($transdata,true,true);
        $signTure = strtoupper(md5($signStr . "&key=" . $payConf['md5_private_key']));
        if (strtoupper($sign) == strtoupper($signTure)) {
            return true;
        } else {
            return false;
        }
    }
}