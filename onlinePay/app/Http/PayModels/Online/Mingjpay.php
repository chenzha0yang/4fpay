<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Mingjpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something
        self::$reqType  = 'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$unit     = 2;
        self::$postType = true;

        $data = [];
        $data['orderNum']        = $order;
        $data['version']         = 'V3.0.0.0';
        $data['charset']         = 'UTF-8';
        $data['randomNum']       = (string) rand(1000,9999);
        $data['merchNo']         = $payConf['business_num'];
        $data['netwayCode']      = $bank;
        $data['amount']          = strval($amount*100);    // 单位:分
        $data['goodsName']       = 'vivo';
        $data['callBackUrl']     = $ServerUrl;
        $data['callBackViewUrl'] = $returnUrl;
        ksort($data);
        $jsonStr      = json_encode($data, 320);
        $data['sign'] = strtoupper(md5($jsonStr . $payConf['md5_private_key']));
        $json         = json_encode($data, 320);
        $encypt       = self::getRsaPublicSign($json,$payConf['public_key']);
        $crypto       = urlencode($encypt);

        $param['data']     = "data={$crypto}&merchNo={$data['merchNo']}&version={$data['version']}";
        $param['orderNum'] = $data['orderNum'];
        $param['amount']   = $data['amount'];
        unset($reqData);
        return $param;
    }

    //提交数据处理
    public static function getRequestByType($data)
    {
        return $data['data'];
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['amount'])) {
            $arr['amount'] = $arr['amount'] / 100;
        }
        return $arr;
    }

    //回调处理
    public static function SignOther($type, $data, $payConf)
    {
        $decode = $data['data'];
        $prKey = openssl_pkey_get_private($payConf['rsa_private_key']);
        $datas = base64_decode($decode);
        $crypto = '';
        //分段解密
        foreach (str_split($datas, 128) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, $prKey);
            $crypto .= $decryptData;
        }
        $array = json_decode($crypto, true);
        $signStr = $array['sign'];
        ksort($array);
        $signArray = array();
        foreach ( $array as $k => $v ) {
            if ( $k !== 'sign' ) {
                $signArray[$k] = $v;
            }
        }
        $md5 =  strtoupper(md5(json_encode($signArray, JSON_UNESCAPED_SLASHES) . $payConf['message1']));
        if ( $md5 == $signStr && $array['payStateCode'] == '00') {
            return true;
        }else{
            return false;
        }
    }
}