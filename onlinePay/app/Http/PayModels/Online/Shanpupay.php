<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Shanpupay extends ApiModel
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
        self::$unit = 2;

        $data       = [
            'channelType' => '01',
            'orderCode'   => $order,
            'totalAmount' => $amount * 100,
            'body'        => 'food',
            'payMode'     => $bank,
            'clientIp'    => '104.149.171.221',
            'notifyUrl'   => $ServerUrl,
            'frontUrl'    => $returnUrl,
            'bankCode'    => '',
            'extend'      => 'hello world',
            'reqTime'     => date('YmdHis'),
        ];
        if($payConf['pay_way'] == 1){
            $data['payMode'] = '00000007';
            $data['bankCode'] = $bank;
        }
        $signStr  = urlencode(self::_toJson($data));
        $ret      = false;
        $strArray = str_split($signStr, 117);
        foreach ($strArray as $cip) {
            if (openssl_public_encrypt($cip, $result, openssl_get_publickey($payConf['public_key']), OPENSSL_PKCS1_PADDING)) {
                $ret .= $result;
            }
        }
        unset($data['frontUrl']);
        unset($data['body']);
        unset($data['extend']);
        $params = array(
            'charset'  => 'utf-8',
            'mid'      => $payConf['business_num'],
            'data'     => base64_encode($ret),
            'signType' => 'MD5',
            'sign'     => base64_encode(md5(urlencode(self::_toJson($data) . $payConf['md5_private_key']))),
        );
        return $params;
    }

    public static function _toJson($params)
    {
        $jsonStr = '{';
        foreach($params as $key => $val) {
            $jsonStr .= "\"{$key}\":\"{$val}\",";
        };
        $jsonStr = rtrim($jsonStr, ",") . "}";
        return $jsonStr;
    }

}