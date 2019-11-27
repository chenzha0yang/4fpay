<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Extensions\Curl;

class Weifuvpay extends ApiModel
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

        $data['version']      = '1.0';//版本号
        $data['charset']      = 'UTF-8';//字符集
        $data['sign_type']    = 'MD5';//签名方式
        $data['mch_id']       = $payConf['business_num'];//商户号
        $data['out_trade_no'] = $order;//订单
        $data['device_info']  = '';//设备号
        if ($payConf['pay_way'] == 1) {
            $data['bank_code'] = $bank;
        }
        $data['body']             = 'apple';//商品描述
        $data['attach']           = '';//附加信息
        $data['total_fee']        = $amount * 100;
        $data['mch_create_ip']    = '127.0.0.1';
        $data['notify_url']       = $ServerUrl;
        $data['time_start']       = '';//订单生成时间
        $data['time_expire']      = '';//订单超时时间
        $data['op_user_id']       = '';//操作员
        $data['goods_tag']        = '';//商品标记
        $data['product_id']       = '';//商品ID
        $data['nonce_str']        = self::randStr(10);//随机字符串
        $data['limit_credit_pay'] = '';//是否限制信用卡
        $signStr                  = self::getSignStr($data, true, true);
        $data['sign']             = strtoupper(self::getMd5Sign($signStr . '&key=', $payConf['md5_private_key']));;//签名
        // 获取token
        $token_url              = 'http://api.lmcf1.com/api/auth/access-token';
        $token_data['appid']    = $data['mch_id'];
        $token_data['secretid'] = $payConf['md5_private_key'];
        $tokurl                 = $token_url . "?appid=" . $token_data['appid'] . "&secretid=" . $token_data['secretid'];
        $curl                   = curl_init();
        curl_setopt($curl, CURLOPT_URL, $tokurl);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $datat = curl_exec($curl);
        curl_close($curl);
        libxml_disable_entity_loader(true);
        $res   = json_decode(json_encode(simplexml_load_string($datat, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $token = $res['token'];
        $xml   = "<xml>";
        foreach ($data as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml      .= "</xml>";
        $header[] = "Content-type: application/xml";
        // 使用Http header设置 token
        $header[1]          = "Authorization: Bearer " . $token;
        $reqData['formUrl'] .= '?token=' . $token;

        Curl::$header  = $header;
        Curl::$request = $xml;//提交数据
        Curl::$url     = $reqData['formUrl'];//支付网关
        Curl::$method  = 'header';//提交方式
        $response      = Curl::Request();
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode(simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if ($result['status'] == '0' && $result['result_code'] == '0') {
            echo "<script type='text/javascript'>window.location.replace('{$result['redirect_url']}');</script>";
            exit();
        } else {
            echo json_encode($result);
        }
        unset($reqData);
        return $data;
    }


    public static function randStr($password)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        for ($i = 0; $i < 32; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    public static function getVerifyResult($request)
    {
        $data = $request->getContent();
        ibxml_disable_entity_loader(true);
        $body                   = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        self::$resData          = $body;
        $result['out_trade_no'] = $body['out_trade_no'];  //订单号
        $result['total_fee']    = sprintf("%.2f", $body['total_fee'] / 100);  //金额
        return $result;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $body = self::$resData;
        $sign = $body['sign'];
        unset($body['sign']);
        $signStr = self::getSignStr($body, true, true);
        $mySign  = strtoupper(self::getMd5Sign($signStr . "&key=", $payConf['md5_private_key']));
        if ($body["status"] == '0' && $sign == $mySign) {
            return true;
        } else {
            return false;
        }
    }

}