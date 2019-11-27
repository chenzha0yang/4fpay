<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Vvvpay extends ApiModel
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

        if($payConf['pay_way'] == '1'){
            $bank = '2100';
        }

        $data = array();
        $data['appId'] = $payConf['message1'];// 产品id
        $data['timeStamp'] = time();//支付时间戳。通常为 13 位长度，后续校验需用到
        $data['totalFee'] = $amount * 100;//商品价格，以分为单位
        $signStr = self::getSignStr($data, false);
        $postKey = self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']);
        $data['partnerId'] = $payConf['business_num'];//商户id
        $data['imsi'] = '';//安卓 imsi 或者 ios 版本 号
        $data['deviceCode'] = '';//设备号(安卓 imei， ios32 位字符串)
        $data['channelOrderId'] = $order;//订单号
        $data['platform'] = '';//客户端平台(0:ios;1 安卓)
        $data['body'] = 'phone';//商品名称
        $data['detail'] = '';//商品详细描述
        $data['attach'] = '';//透传字段，支付通知里原样返回
        $data['payType'] = $bank;//支付类型:
        $data['notifyUrl'] = $ServerUrl;//支付完成通知回调 url
        $data['returnUrl'] = $returnUrl;
        if($payConf['pay_way'] == '1'){//网银
            $data['spUserId'] = time().rand(10000, 99999);//支付用户id，唯一性，
            $data['bankSegment'] = $bank;//银行代号
        }

        $data["sign"] = $postKey;
        self::$method = 'get';
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';
        self::$unit = 2;
        self::$isAPP = true;

        unset($reqData);
        return $data;
    }

    /**
     * @param $response
     * @return array
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response,true);
        $res = [];
        if(isset($result['return_code']) && $result['return_code'] == '0'){
            if(isset($result['payParam']['pay_info']) && !empty($result['payParam']['pay_info']) ){
                $res['pay_info'] = $result['payParam']['pay_info'];
            }else{
                $res['pay_info'] = $result['payParam']['code_img_url'];
            }
        } else {
            $res['return_code'] = $result['return_code'];
            $res['return_msg'] = $result['return_msg'];
        }
        return $res;
    }
}