<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Shunweipay extends ApiModel
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
        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method  = 'header';


        //TODO: do something
        $arr["client_num"] = $payConf['business_num']; //商户号
        $arr["order_num"] = $order;
        $arr["amount"] = (int)$amount*100;
        $arr["show_cashier_page"] = "0";//
        $arr["bank_code"] = $bank;
        $randStr = self::generateRandNum(count($arr) + 1);//随机字符串
        $arr["random_str"] = $randStr;
        $params = self::signStr($arr, $randStr);//待签名数组
        $jsonStr = json_encode($params, JSON_UNESCAPED_UNICODE);//待签名字符串
        $params["request_sign"] = md5($jsonStr.'BB6FFA40B3DE1D6FDA6AAA2BE54B44A3');//签名
        $params["callback_url"] = $ServerUrl;
//        $publicKey=openssl_pkey_get_public($payConf['public_key']);
        $publicKey = self::publicKeyStr($payConf['public_key']);
        $encryptData = self::encrypt($publicKey, json_encode($params));
//请求参数
        $req["request_body"] = urlencode($encryptData);
        $req["interface_version"] = md5("1.0.0".'H190927200159849229817');
//        dd(json_encode($req));
        $reqDataStr = self::get_sign($req);
//发起请求
//        dd($params,$publicKey,$encryptData,$reqDataStr);

        // dd("https://api.shunwpay.com/gateway/order/payment/ebank-htm", $reqDataStr, 'H190927200159849229817');
        $resultArr = self::streamContextCreate("https://api.shunwpay.com/gateway/order/payment/ebank-htm", $reqDataStr, 'H190927200159849229817');
        dd($resultArr);


        unset($reqData);
        return $req;
    }
    /**
     * 拼接公钥字符串
     * @param $publicStr
     * @return string
     */
    public static function publicKeyStr($publicStr){
        //公钥
        $public_key = "-----BEGIN PUBLIC KEY-----\r\n";
        foreach (str_split($publicStr,64) as $str){
            $public_key .= $str . "\r\n";
        }
        $public_key .="-----END PUBLIC KEY-----";

        return $public_key;

    }

    public static function streamContextCreate($url,$dataStr, $headerKey){
        $httpData["method"] = "POST";
        $httpData["header"] = "Content-type: application/x-www-form-urlencoded\r\n"."security_header_key: ".$headerKey."";
        $httpData["content"] = $dataStr;
        $http["http"]=$httpData;
        $resultData = "";
        try {
            $context = stream_context_create($http);
            $result = file_get_contents($url, false, $context);
            $resultData= json_decode($result,true);
        } catch (Exception $e) {
            throw $e;
        }
        return $resultData;
    }

    public static function get_sign($arr) {
        $signmd5="";
        foreach($arr as $x=>$x_value)
        {
            if(!$x_value==""||$x_value==0){
                if($signmd5==""){
                    $signmd5 =$signmd5.$x .'='. $x_value;
                }else{
                    $signmd5 = $signmd5.'&'.$x .'='. $x_value;
                }
            }
        }
        return $signmd5;
    }

    public static function encrypt($publicKey, $data) {

        $key = openssl_get_publickey($publicKey);

        $original_arr = str_split($data,117);
        foreach($original_arr as $o) {
            $sub_enc = null;
            openssl_public_encrypt($o,$sub_enc,$key);
            $original_enc_arr[] = $sub_enc;
        }

        openssl_free_key($key);
        $original_enc_str = base64_encode(implode('',$original_enc_arr));
        return $original_enc_str;
    }

    public static function signStr($arr, $randStr){
        ksort($arr);
        $randArr = str_split($randStr);
        $arrKey = array_keys($arr);
        $signArr = array();
        foreach ($randArr  as $value){
            $k = $arrKey[$value];
            $signArr[$k] = $arr[$k];
        }
        return $signArr;
    }

    public static function generateRandNum($n){
        $randArr = array();
        do{
            $num = rand(0, $n-1);
            if(!in_array($num, $randArr)){
                array_push($randArr, $num);
            }
        }while(count($randArr) < $n);
        return implode($randArr);
    }

}