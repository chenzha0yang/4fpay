<?php

namespace App\Http\PayModels\Online;

use App\Extensions\File;
use App\ApiModel;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;

class Ugpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $success = false; // 验证回调是否成功

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


        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$postType = true;
        self::$resType = 'other';



        //TODO: do something
        $keys= explode('@', $payConf['md5_private_key']);
        $data['merchCardNo'] = $keys[1];
        $data['charset'] = 'UTF-8';
        $data['goodsName'] = 'goods_name';
        $data['merchNo'] = $payConf['business_num'];
        $data['random'] = self::randomStr(4);
        $data['version'] = 'V1.0.1';
        $data['amount'] = $amount;
        $data['merchOrderSn'] = $order;
        $data['payCallBackUrl'] = $ServerUrl;
        $data['payViewUrl'] = $returnUrl;
        ksort($data);
        $json =str_replace("\\/", "/",json_encode($data));
        $data['sign'] = strtoupper(md5($json.$keys[0])); //MD5签名
        $arrJsonStr = json_encode($data);

        $dataStr = self::publicEncrypt($payConf['public_key'],$arrJsonStr);
        $request["data"] = urlencode($dataStr);
        $request["merchNo"] = $data["merchNo"];
        $request["version"] = $data["version"];
        $request["merchOrderSn"] = $order;
        $request["amount"] = $amount;

        unset($reqData);
        return $request;
    }

    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['message']=='SUCCESS'){
            if(isset($result['data']['webRrcode'])){
                $result['payUrl']=$result['data']['webRrcode'];
            }
            if(isset($result['data']['h5PayUrl'])){
                $result['payUrl']=$result['data']['h5PayUrl'];
            }
            if(isset($result['data']['h5PayPrefix'])){
                $result['payUrl']=$result['data']['h5PayPrefix'];
            }
        }
        return $result;
    }

    //回调处理数据 解密
    public static function getVerifyResult($request, $mod)
    {
        $data = $request->all();
        $bankOrder = PayOrder::getOrderData($data['merchOrderSn']);//根据订单号 获取入款注单数据
        if (empty($bankOrder)) {
            //查询不到订单号时不插入回调日志，pay_id / pay_way 方式为0 ，关联字段不能为空
            File::logResult($data);
            return trans("success.{$mod}");
        }
        $payConf   = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
        $dataJsonStr = self::privateDecrypt($data["data"],$payConf['rsa_private_key']);
        //json字符转转数组
        $dataArr = json_decode($dataJsonStr,true);
        //取出秘钥栏第三段desKey秘钥
        $keys= explode('@', $payConf['md5_private_key']);
        if(self::verify($dataArr,$keys[2])){
            self::$success = true;
        }else{
            self::$success = false;
        }
        return $dataArr;
    }

    public static function SignOther($type, $data, $payConf)
    {
        if (self::$success) {
            return true;
        }else{
            return false;
        }
    }

    //回调验签
    public static function verify($result,$md5){
        $signStr = $result['sign'];
        $sign_array = array();
        foreach ($result as $k => $v) {
            if ($k !== 'sign'){
                $sign_array[$k] = $v."";
            }
        }
        ksort($sign_array);
        $signJsonStr =  str_replace("\\/", "/", json_encode($sign_array));
        $sign  = md5($signJsonStr.$md5);
        if($signStr != $sign){
            return false;
        }
        return true;
    }

    //私钥解密
    public static function privateDecrypt($data,$privateKey){
        //读取秘钥
        $pr_key = openssl_pkey_get_private($privateKey);
        if ($pr_key == false){
            echo "打开密钥出错";
            die;
        }
        $data = self::url_safe_base64_decode($data);
        $crypto = '';
        //分段解密
        foreach (str_split($data, 128) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, $pr_key);
            $crypto .= $decryptData;
        }
        return $crypto;
    }

    //公钥加密
    public static function publicEncrypt($publicKey, $data) {

        $key = openssl_get_publickey($publicKey);

        $original_arr = str_split($data,117);
        foreach($original_arr as $o) {
            $sub_enc = null;
            openssl_public_encrypt($o,$sub_enc,$key);
            $original_enc_arr[] = $sub_enc;
        }
        openssl_free_key($key);
        $original_enc_str = self::url_safe_base64_encode(implode('',$original_enc_arr));
        return $original_enc_str;
    }


    public static function url_safe_base64_encode($data)
    {
        return str_replace(array('+','/','='), array( '-','_',), base64_encode($data));
    }

    public static function url_safe_base64_decode($data){
        $base_64 = str_replace(array('-','_'), array('+','/'), $data);
        return base64_decode($base_64);
    }

    public static function getRequestByType($req)
    {
        unset($req['merchOrderSn'],$req['amount']);
        return http_build_query($req);
    }

    //取随机数处理
    public static function randomStr($length = 6){
        // 密码字符集，可任意添加你需要的字符
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $tNonceStr = "";
        for ($i = 0; $i < $length; $i ++) {
            $tNonceStr .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $tNonceStr;
    }

}