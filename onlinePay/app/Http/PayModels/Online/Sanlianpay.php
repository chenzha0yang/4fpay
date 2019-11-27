<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Sanlianpay extends ApiModel
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
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $data                 = [];
        $data['sendTime']     = date('Y-m-d H:i:s');
        $data['amount']       = $amount;
        $data['out_trade_no'] = $order;
        $data['partner_id']   = $payConf['business_num'];
        $data['notify_url']   = $ServerUrl;
        $data['payType']      = $bank;
        ksort($data);
        $json   = json_encode($data, JSON_UNESCAPED_SLASHES);
        $rsaMsg = self::encrypt($json, $payConf['public_key']);
        $post = [
            'partner_id' => $data['partner_id'],
            'rsamsg'     => urlencode($rsaMsg),
            'md5msg'     => md5($json . $payConf['md5_private_key']),
            'username'   => isset($reqData['username'])?$reqData['username']:"chomngzhi",
            'ip'         => self::getIp()
        ];
        unset($reqData);
        return $post;
    }

    public static function getQrCode($res)
    {
        $arr = json_decode($res,true);
        if ($arr['status'] == 0) {
            echo urldecode($arr['payUrl']);exit;
        } else {
            echo $res;exit;
        }
    }

    public static function encrypt($originalData, $key)
    {
        $crypto = '';
        foreach (str_split($originalData, 117) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, openssl_pkey_get_public($key), OPENSSL_PKCS1_PADDING);
            $crypto .= $encryptData;
        }
        return base64_encode($crypto);
    }
}

