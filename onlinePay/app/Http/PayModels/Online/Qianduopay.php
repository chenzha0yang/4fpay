<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Qianduopay extends ApiModel
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
        self::$isAPP = true;
        self::$unit = 2;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $data['asynchCallBack']  = $ServerUrl;
        $data['currency']  = "cny";
        $data['money']  = $amount*100;
        $data['orderNo']  = $order;
        $data['payType']  = $bank;
        $data['synchCallBack']  = $returnUrl;

        $json   = json_encode($data);
        $post['token']  = $payConf['business_num'];
        $post['data']  = $json;
        $pay_key = openssl_pkey_get_private($payConf['rsa_private_key']);
        openssl_sign($json, $sign, $pay_key);
        $post['sign']         = base64_encode($sign);
        $post['orderNo'] = $data['orderNo'];
        $post['money'] = $data['money'];
        unset($reqData);
        return $post;
    }

    public static function getQrCode($req)
    {
        $data = json_decode($req, true);
        if($data['code'] == '200'){
            $data['image'] = $data['data']['image'];
        }
        return $data;
    }


    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
            $arr['payMoney'] = $arr['payMoney'] / 100;
        return $arr;
    }

    public static function signOther($mod, $data, $payConf)
    {
        $sign = $data['sign'];
        //对返回数据按 ascii 方式排序   注意：如果值为空  不参与签名
        $pub_key = openssl_get_publickey($payConf['public_key']);
        $res = openssl_verify($data['data'], base64_decode($sign), $pub_key, OPENSSL_PKCS1_PADDING);
        if ($res && $data['orderStatus'] == "3") {
            return true;
        } else {
            return false;
        }
    }

}