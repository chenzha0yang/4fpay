<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\CallbackMsg;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;

class Chaojixingpay extends ApiModel
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
        $aa = explode('@', $payConf['business_num']);

        $data['merId']    = $aa[0];//商户号
        $data['terId']    = $aa[1];//终端号

        if(!isset($aa[1])){
            echo '绑定格式错误!请参考:商户号@终端号';exit();
        }

        if(!openssl_get_publickey($payConf['public_key'])){
            echo '公钥绑定错误!';exit();
        }

        if(!openssl_get_privatekey($payConf['rsa_private_key'])){
            echo '私钥绑定错误!';exit();
        }

        $data['businessOrdid'] = $order;
        $data['orderName'] = 'goods';
        $data['payType']    = $bank;


        $data['asynURL']   = $ServerUrl;
        $data['syncURL'] = $returnUrl;
        $data['appSence'] = '1001'; //pc
        if ($payConf['is_app'] == 1){
            $data['appSence'] = '1002'; //h5
        }
        $data['tradeMoney']      = $amount * 100;

        $data_json = json_encode($data,JSON_UNESCAPED_UNICODE);
        $Split = str_split($data_json, 64);
        $encParam_encrypted = '';
        foreach ($Split as $Part) {
            openssl_public_encrypt($Part,$PartialData,openssl_get_publickey($payConf['public_key']));
            $t = strlen($PartialData);
            $encParam_encrypted .= $PartialData;
        }

        $encParam = base64_encode($encParam_encrypted);//加密的业务参数
        openssl_sign($encParam_encrypted, $sign_info, openssl_get_privatekey($payConf['rsa_private_key']));
        $sign = base64_encode($sign_info);//加密业务参数的签名

        $res['sign']    = $sign;
        $res['version'] = '1.0.9';
        $res['merId'] = $data['merId'];
        $res['encParam'] = $encParam;

        unset($reqData);
        return $res;
    }

    /**
     * @param $request
     * @return mixed
     */
    public static function getVerifyResult($request)
    {
        $data      = $request->all();
        if(isset($data['orderId'])){
            $bankOrder = PayOrder::getOrderData($data['orderId']);//根据订单号 获取入款注单数据
            if (!isset($bankOrder->merchant_id)) {
                CallbackMsg::addDebugLogs($data);
            }
            $payConf   = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
            $priKey = openssl_get_privatekey($payConf['rsa_private_key']);
            $res = base64_decode($data['encParam']);
            $Split = str_split($res, 128);
            $back='';
            foreach($Split as $k=>$v){
                openssl_private_decrypt($v, $decrypted, $priKey);
                $back.= $decrypted;
            }
            $backToArray = json_decode($back, true);
            $data['money'] = $backToArray['money']/100;
        }else{
            $data['orderId'] = '';
            $data['money'] = '';
        }
        return $data;
    }

    public static function SignOther($mod, $data, $payConf)
    {
        $priKey = openssl_get_privatekey($payConf['rsa_private_key']);
        $pubkey = openssl_get_publickey($payConf['public_key']);
        $res = base64_decode($data['encParam']);

        $Split = str_split($res, 128);
        $back='';
        foreach($Split as $k=>$v){
            openssl_private_decrypt($v, $decrypted, $priKey);
            $back.= $decrypted;
        }
        $backToArray = json_decode($back, true);
        $rst = openssl_verify($res,base64_decode($data['sign']),$pubkey);
        if ($rst && $backToArray['order_state'] == '1003') {
            return true;
        } else {
            return false;
        }
    }
}