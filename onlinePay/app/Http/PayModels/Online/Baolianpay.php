<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\CallbackMsg;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;

class Baolianpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $sign = false;

    public static $imgSrc = false;
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        if ($payConf['pay_way'] == 9 || $payConf['is_app'] == 1) {
            self::$reqType = 'form';
        } else {
            self::$reqType = 'curl';
            self::$payWay  = $payConf['pay_way'];
            self::$resType = 'other';
            self::$postType = true;
        }
        if ($bank =='WEIXINWAP'){
               self::$imgSrc = true;
        }

        $data                   = array();
        $data['p1_mchtid']      = $payConf['business_num']; // 商户ID
        $data['p2_paytype']     = $bank; //支付方式
        $data['p3_paymoney']    = $amount; //支付金额
        $data['p4_orderno']     = $order; //商户平台唯一订单号
        $data['p5_callbackurl'] = $ServerUrl; //商户异步回调通知地址
        $data['p6_notifyurl']   = $returnUrl; //商户同步通知地址
        $data['p7_version']     = 'v2.9'; //版本号
        $data['p8_signtype']    = '2'; //签名加密方式
        $data['p9_attach']      = ''; //备注
        $data['p10_appname']    = ''; //分成标识
        $data['p11_isshow']     = '0'; //是否显示收银台
        $data['p12_orderip']    = self::getIp(); //签名加密方式
        $signStr      = self::getSignStr($data, false, false);
        $sign                   = md5($signStr . $payConf['md5_private_key']);
        $data['sign']           = $sign;
        $jsonStr                = json_encode($data);
        //Rsa 公钥加密
        $original_arr = str_split($jsonStr, 64);
        foreach ($original_arr as $o) {
            $sub_enc = null;
            openssl_public_encrypt($o, $sub_enc, openssl_get_publickey($payConf['public_key']));
            $original_enc_arr[] = $sub_enc;
        }
        $reqdata = urlencode(base64_encode(implode('', $original_enc_arr)));
        //请求参数
        $post["mchtid"]      = $payConf['business_num'];
        $post["reqdata"]     = $reqdata;
        $post['p4_orderno'] = $order;
        $post['p3_paymoney'] = $amount;

        unset($reqData);
        return $post;
    }
    public static function getQrCode($response)
    {
        $arr = json_decode($response, true);

        if (isset($arr['data'])) {
            return $arr['data'];
        }
        return $arr;
    }
    public static function getRequestByType($data)
    {
        unset($data['p4_orderno'],$data['p3_paymoney']);
        return http_build_query($data);
    }


    public static function getVerifyResult($request, $mod)
    {
        $data      = $request->all();
        $bankOrder = PayOrder::getOrderData($data['ordernumber'] ?? "");//根据订单号 获取入款注单数据
        if (!isset($bankOrder->merchant_id)) {
            CallbackMsg::addDebugLogs($data);
        }
        $payConf   = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息

        $reqdata = $data["reqdata"];
        $return['ordernumber'] = $data['ordernumber'];


        if(strpos($reqdata,"%")){
            $reqdata =  urldecode($reqdata);
        }
        $bdata   = base64_decode($reqdata);

        $arr  = '';
        //分段解密
        foreach (str_split($bdata, 128) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, openssl_pkey_get_private($payConf['rsa_private_key']));
            $arr .= $decryptData;
        }

        if ($arr) {

            $arr     = json_decode($arr, true);
            $return['paymoney'] = $arr['paymoney'];
            $signArr = array();
            foreach ($arr as $k => $v) {
                if ($k != 'attach' && $k != 'sysnumber' && $k != "sign") {
                    $signArr[$k] = $v . "";
                }
            }
            $signStr = self::getSignStr($signArr, false, false);

            $sign    = md5($signStr . $payConf['md5_private_key']);
            if ($arr["sign"] == $sign && $arr["orderstatus"] == '1') {
                self::$sign = true;
                return $return;
            } else {
                self::$sign = false;
                return $return;
            }
        } else {
            self::$sign = false;
            return $return;
        }
    }

    public static function signOther($mod, $data, $payConf)
    {
        return self::$sign;
    }


}
