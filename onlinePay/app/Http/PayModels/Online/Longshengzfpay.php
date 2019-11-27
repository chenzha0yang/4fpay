<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Longshengzfpay extends ApiModel
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
        if($payConf['pay_way'] != 9 || !self::$isAPP){
            self::$reqType = 'curl';
            self::$payWay  = $payConf['pay_way'];
            self::$resType = 'other';
            self::$httpBuildQuery = true;
        }
        
        $data['merchant_code'] = $payConf['business_num'];//商户号
        if((int)$payConf['pay_way'] === 1){
            $data['service_type'] = 'direct_pay';
            $data['bank_code'] = $bank;
        }else{
            $data['service_type'] = $bank;//银行编码
        }
        $data['interface_version'] = '3.0';
        $data['input_charset'] = 'UTF-8';
        $data['sign_type'] = 'RSA-S';
        $data['order_no'] = $order;//订单号
        $data['order_amount'] = sprintf('%.2f',$amount);//订单金额
        $data['order_time'] = date('Y-m-d H:i:s',time());
        $data['return_url'] = $returnUrl;
        $data['notify_url'] = $ServerUrl;
        $data['product_name'] = 'test';
        $data['client_ip'] = self::getIp();
        $data['extend_param'] = '';
        $data['extra_return_param'] = '';
        $data['pay_type'] = '';
        $data['product_code'] = '';
        $data['product_desc'] = '';
        $data['product_num'] = '';
        $data['redo_flag'] = '';
        $data['show_url'] = '';
        //$signStr =  $this->getSignStr($data);
        $signStr= "";

        if($data['bank_code'] != ""){
            $signStr = $signStr."bank_code=".$data['bank_code']."&";
        }
        if($data['client_ip'] != ""){
            $signStr = $signStr."client_ip=".$data['client_ip']."&";
        }
        if($data['extend_param'] != ""){
            $signStr = $signStr."extend_param=".$data['extend_param']."&";
        }
        if($data['extra_return_param'] != ""){
            $signStr = $signStr."extra_return_param=".$data['extra_return_param']."&";
        }

        $signStr = $signStr."input_charset=".$data['input_charset']."&";
        $signStr = $signStr."interface_version=".$data['interface_version']."&";
        $signStr = $signStr."merchant_code=".$data['merchant_code']."&";
        $signStr = $signStr."notify_url=".$data['notify_url']."&";
        $signStr = $signStr."order_amount=".$data['order_amount']."&";
        $signStr = $signStr."order_no=".$data['order_no']."&";
        $signStr = $signStr."order_time=".$data['order_time']."&";

        if($data['pay_type'] != ""){
            $signStr = $signStr."pay_type=".$data['pay_type']."&";
        }

        if($data['product_code'] != ""){
            $signStr = $signStr."product_code=".$data['product_code']."&";
        }
        if($data['product_desc'] != ""){
            $signStr = $signStr."product_desc=".$data['product_desc']."&";
        }

        $signStr = $signStr."product_name=".$data['product_name']."&";

        if($data['product_num'] != ""){
            $signStr = $signStr."product_num=".$data['product_num']."&";
        }
        if($data['redo_flag'] != ""){
            $signStr = $signStr."redo_flag=".$data['redo_flag']."&";
        }
        if($data['return_url'] != ""){
            $signStr = $signStr."return_url=".$data['return_url']."&";
        }

        $signStr = $signStr."service_type=".$data['service_type'];

        if($data['show_url'] != ""){

            $signStr = $signStr."&show_url=".$data['show_url'];
        }

        //私钥加密
        $pi= openssl_get_privatekey($payConf['rsa_private_key']);
        if(!$pi){
            echo '私钥绑定错误 注意rsa格式！';exit();
        }

        openssl_sign($signStr,$sign_info,$pi,OPENSSL_ALGO_MD5);
        $data['sign'] = base64_encode($sign_info);

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['response']['resp_code'] == 'SUCCESS') {
            $data['qrCode'] = $data['response']['qrcode'];
            $data['resp_code'] = $data['response']['resp_code'];
            $data['resp_msg'] = 'ok';
        }
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {   
        $sign_str = base64_decode($data["sign"]);
        unset($data['sign']);
        $signStr = "";

        if($data['bank_seq_no'] != ""){
            $signStr = $signStr."bank_seq_no=".$data['bank_seq_no']."&";
        }
        if($data['extra_return_param'] != ""){
            $signStr = $signStr."extra_return_param=".$data['extra_return_param']."&";
        }
        $signStr = $signStr."interface_version=".$data['interface_version']."&";
        $signStr = $signStr."merchant_code=".$data['merchant_code']."&";
        $signStr = $signStr."notify_id=".$data['notify_id']."&";
        $signStr = $signStr."notify_type=".$data['notify_type']."&";
        $signStr = $signStr."order_amount=".$data['order_amount']."&";
        $signStr = $signStr."order_no=".$data['order_no']."&";
        $signStr = $signStr."order_time=".$data['order_time']."&";
        $signStr = $signStr."trade_no=".$data['trade_no']."&";
        $signStr = $signStr."trade_status=".$data['trade_status']."&";
        $signStr = $signStr."trade_time=".$data['trade_time'];

        //公钥解密
        $pu = openssl_get_publickey($payConf['public_key']);
        $flag = openssl_verify($signStr,$sign_str,$pu,OPENSSL_ALGO_MD5);

        if ($flag  && $data['trade_status'] = 'SUCCESS') {
            return true;
        }
        return false;
    }


}