<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Sophiaquickpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $UserName = '';
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
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';

        if(!isset($payConf['md5_private_key'])){
            echo 'md5密钥不能为空!请检查绑定资料!';exit();
        }

        if(!$piKey = openssl_pkey_get_private($payConf['rsa_private_key'])){
            echo '私钥格式错误!请检查格式!';exit();
        }

        self::$isAPP = true;
        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$method  = 'header';

        $data['tranCode']   = '1101'; //交易码
        $data['agtId']      = $payConf['business_num']; //商户号
        $data['orderAmt']   = strval($amount * 100); //订单总金额
        $data['orderId']    = $order; //商户订单号
        $data['goodsName']  = self::String2Hex('SophiaGoods'); //商品简单描述
        $data['notifyUrl']  = $ServerUrl; //URL 地址
        $data['nonceStr']   = self::nonceStr(); //随机字符串
        $data['stlType']    = 'T1'; //结算类型
        $data['uId']     = self::$UserName; //交易 ip
        $data['termIp']     = '103.73.165.251';//self::getIp(); //交易 ip
        $data['payChannel'] = $bank; //支付渠道
        $signStr                 = self::getSignStr($data, true, true);
        $sign         = strtoupper(md5($signStr . "&key=" . $payConf['md5_private_key']));
        $res = openssl_get_privatekey($payConf['rsa_private_key']);
        openssl_sign($sign, $signa, $res, 'SHA256');
        $data['sign'] = base64_encode($signa);
        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data);

        $postParm = array(
            'REQ_HEAD' => array('sign' => $sign),
            'REQ_BODY' => $data,
        );

        $jsonData = json_encode($postParm);
        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ];
        $postData['data']         = $jsonData;
        $postData['httpHeaders']  = $header;
        $postData['orderId'] = $data['orderId'];
        $postData['orderAmt']  = $data['orderAmt'];
        unset($reqData);
        return $postData;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        $string = '';
        $hex    = $data['REP_BODY']['rspmsg'];
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        if ($data['REP_BODY']['rspcode'] == '000000' && $data['REP_BODY']['orderState'] == '00') {
            if((int)self::$payWay === 6 || (int)self::$payWay === 7){
                $data['payUrl'] = $data['REP_BODY']['qrcode'];
            }else{
                $data['payUrl'] = $data['REP_BODY']['codeUrl'];
            }
        }else{
            $data['code'] = $data['REP_BODY']['rspcode'];
            $data['msg'] = $string;
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $data =  json_decode($arr,true);
        $sign    = $data['REP_HEAD']['sign'];
        $result  = $data['REP_BODY'];
        $result['orderId'] = $result['orderId'];
        $result['orderAmt']  = $result['orderAmt'] / 100;
        return $result;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $json = file_get_contents('php://input');  //获取POST流
        $data=json_decode($json,true);
        $sign    = $data['REP_HEAD']['sign'];
        $result  = $data['REP_BODY'];
        $orderid = $result['orderId'];
        $amount  = $result['orderAmt'] / 100;
        $signStr    = self::getSignStr($result, true, true);
        $signTrue         = strtoupper(md5($signStr . "&key=" . $payConf['md5_private_key']));
        $publicKey = openssl_get_publickey($payConf['public_key']);
        $res = openssl_verify($signTrue,base64_decode($sign),  $publicKey, 'SHA256');

        if ($res &&  $result['orderState'] == '01') {
            return true;
        }
        return false;
    }

    public static function nonceStr($len = 12)
    {
        $arr = array(
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
            'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y', 'Z',
        );
        $str = '';
        for ($i = 1; $i <= $len; $i++) {
            $str .= $arr[mt_rand(0, 35)];
        }
        return $str;
    }

    public static  function String2Hex($string)
    {
        $hex = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }
}