<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Newjnpay extends ApiModel
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


        //TODO: do something

        $data=array();
        $data['mchid'] = $payConf['business_num'];
        $data['orderno'] = $order;
        $data['money'] = $amount;
        $data['code'] = $bank;
        $data['notify'] = $ServerUrl;
        $data['key'] = $payConf['md5_private_key'];//二次加密密钥
        ksort($data);
        $str = self::encrypt($data);
        $prikey=openssl_pkey_get_private($payConf['rsa_private_key']);
        if ($prikey == false) {
            echo '私钥格式不对，请检查好后重新绑定！！！';exit;
        }
        openssl_private_encrypt($str,$sign,$prikey);//私钥加密
        $sign = base64_encode($sign);
        $native = [
            "mchid" => $payConf['business_num'],
            "sign" => $sign,
            "name" => $order,
            "attach" => $amount,
            'v'     => '2'
        ];
        unset($reqData);
        return $native;
    }
    public static function getVerifyResult($request, $mod)
    {
        $arr           = $request->all();
        $arr['order'] = $arr['name'];
        $arr['amount'] = $arr['attach'];
        return $arr;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = str_replace(" ","+",$data["sign"]); //加密密钥
        $prikey = openssl_pkey_get_private($payConf['rsa_private_key']);
        openssl_private_decrypt(base64_decode($sign), $decrypted, $prikey);//私钥解密
        $result = self::aesPayDecrypt($decrypted,$payConf['md5_private_key'],$data['mchid']);
        if (empty($result)) {
            return false;
        }else {
            $obj = json_decode($result, true);
            if ($obj["status"] == "success") {
                return true;
            }
        }
    }
    public static function aesPayDecrypt($sign,$key,$mchid)
    {
        $key = self::getPayAesKey($key,$mchid);

        return openssl_decrypt(base64_decode($sign), "AES-128-CBC",substr($key,0,16), OPENSSL_RAW_DATA,  substr($key,16,16));
    }

    public static function encrypt($data){
        $k = $data["key"].$data["mchid"];
        unset($data["key"]);
        unset($data["mchid"]);
        $key='';
        for($i = 0;$i<strlen($k);$i++){
            if(is_numeric($k[$i])){
                $m = $k[$i];
                if($m == 0) {
                    $key.="@";
                }
                else{
                    $key .= substr("a2x%e6t*iq&",$m,1);
                }
            }else{

                $key .= $k[$i];
            }
        }
        return base64_encode(openssl_encrypt(json_encode($data), "AES-128-CBC", substr($key,0,16), OPENSSL_RAW_DATA, substr($key,16,16)));
    }

    public static function getPayAesKey($ky,$mchid){
        $k = $ky.$mchid;
        $key='';
        for($i = 0;$i<strlen($k);$i++){
            if(is_numeric($k[$i])){
                $m = $k[$i];
                if($m == 0) {
                    $key.="@";
                }
                else{
                    $key .= substr("a2x%e6t*iq&",$m,1);
                }
            }else{
                $key .= $k[$i];
            }
        }
        return $key;
    }
}