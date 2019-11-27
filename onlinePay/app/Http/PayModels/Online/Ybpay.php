<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayOrder;
use App\Http\Models\PayMerchant;


class Ybpay extends ApiModel
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

        self::$unit = 2;

        $data['merId'] = $payConf['business_num'];
        $data['version'] = '1.0.9';
        $data['terId'] = $payConf['rsa_private_key'];
        $data['businessOrdid'] = $order;
        $data['orderName'] = 'chongzhi';
        $data['tradeMoney'] = number_format($amount, 2, '', '');//分为单位
        if($payConf['pay_way'] == '1'){
            $bank = "1003";
        }
        $reqJson = array(
            'childMerchant' => rand(1000000000,9999999999),
            'registerTime' => date('YmdHis')
        );
        $selfParam = array(
            'terId'        => $data['terId'],
            'businessOrdid'=> $data['businessOrdid'],
            'orderName'    => $data['orderName'],
            'tradeMoney'   => $data['tradeMoney'],
            'payType'      => $bank,
            'asynURL'      => $ServerUrl,
            'syncURL'      => $returnUrl,
            'appSence'     => '1001',
            'reqJson' 	   => json_encode($reqJson)
        );
        $enc_json = json_encode($selfParam,JSON_UNESCAPED_UNICODE);
        $Split = str_split($enc_json, 64);
        $encParam_encrypted = '';
        ForEach($Split as $Part){
            openssl_public_encrypt($Part,$PartialData,$payConf['public_key']);//服务器公钥加密
            $t = strlen($PartialData);
            $encParam_encrypted .= $PartialData;
        }
        $encParam = base64_encode(($encParam_encrypted));//加密的业务参数
        openssl_sign($encParam_encrypted, $sign_info, $payConf['md5_private_key']);
        $sign = base64_encode($sign_info);//加密业务参数的签名

        $post_data['merId'] = $payConf['business_num'];
        $post_data['version'] = '1.0.9';
        $post_data['encParam'] = $encParam;
        $post_data['sign'] = $sign;

        unset($reqData);
        return $post_data;
    }

//回调金额化分为元
    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        $bankOrder = PayOrder::getOrderData($arr['orderId']);//根据订单号 获取入款注单数据
        $payConf   = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
        $res = json_decode(self::decrypt($arr['encParam'],$payConf['md5_private_key']),true);

        $data['tradeAmount'] = $res['money']/100;
        $data['orderId'] = $arr['orderId'];
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $res = json_decode(self::decrypt($data['encParam'],$payConf['md5_private_key']),true);
        if ($res['order_state']=='1003' && openssl_verify(base64_decode($data['encParam']),base64_decode($data['sign']),$payConf['public_key'])) {
            return true;
        } else {
            return false;
        }
    }

    //rsa解密
    public static function decrypt($data,$pay_Key) {
        $priKey= openssl_get_privatekey($pay_Key);
        $data=base64_decode($data);
        $Split = str_split($data, 128);
        $back='';
        foreach($Split as $k=>$v){
            openssl_private_decrypt($v, $decrypted, $priKey);
            $back.= $decrypted;
        }
        return $back;
    }
}