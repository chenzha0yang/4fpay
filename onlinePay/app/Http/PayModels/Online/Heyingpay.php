<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Heyingpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $resData = [];

    /**
     * @param array       $reqData 接口传递的参数
     * @param array $payConf
     * @return array
     */
    public static function getAllInfo($reqData, $payConf)
    {
        date_default_timezone_set('Asia/Shanghai');
        /**
         * 参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$postType  = true;
        self::$resType = 'other';


        $data['partner_id']   = $payConf['business_num'];
        $data['payType']      = $bank;
        $data['amount']       = $amount;
        $data['out_trade_no'] = $order;
        $data['sendTime']     = date('Y-m-d h:i:s');
        $data['notify_url']   = $ServerUrl;
        ksort($data);
        $json = json_encode($data,JSON_UNESCAPED_SLASHES);
        $pub_key = $payConf['public_key'];
        $rsamsg = self::encrypt($json,$pub_key);
        $post = [
            'partner_id' => $payConf['business_num'],
            'rsamsg'     => urlencode($rsamsg),
            'md5msg'     => md5($json.$payConf['md5_private_key']),
            'amount'     => $amount,
            'out_trade_no' => $order
        ];
        unset($reqData);
        return $post;
    }

    public static function getRequestByType($data)
    {
        $post = [
            'partner_id' => $data['partner_id'],
            'rsamsg'     => $data['rsamsg'],
            'md5msg'     => $data['md5msg']
        ];
        return $post;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['msg'] == '预下单成功') {
            echo urldecode($result['payUrl']);
        }
        return $result;
    }

    public static function encrypt($originalData,$key){
        $crypto = '';
        foreach (str_split($originalData, 117) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, $key,OPENSSL_PKCS1_PADDING);
            $crypto .= $encryptData;
        }
        return base64_encode($crypto);
    }

}