<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Extensions\File;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;
use Illuminate\Http\Request;

class Renrenzfverpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        $payInfo = explode('@', $payConf['business_num']);
        if(!isset($payInfo[1])){
            echo '商户资料绑定错误,请参考:商户ID@AES密钥';exit();
        }

        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$method  = 'header';

        $clienttype = 'PC';
        if($payConf['is_app'] == 1){
            $clienttype = 'H5';
        }

        //仅支持微信0 支付宝1  云闪付3
        switch ($payConf['pay_way']) {
            case '3':
                $orderchannel = '1';
                break;
            case '6':
                $orderchannel = '3';
                break;
            default:
                $orderchannel = '0';
                break;
        }

        $data['merchantorderid']   = $order;
        $data['orderchannel']     = $orderchannel;
        $data['trscode']           = $bank;
        $data['applyamount']       = $amount;
        $data['web_username']     = self::$UserName;
        $data['clienttype']         = $clienttype;
        $data['callbackurl'] = $ServerUrl;

        $jsonData = json_encode($data);
        $publicData['data'] = base64_encode(openssl_encrypt($jsonData, 'AES-128-ECB', $payInfo[1], OPENSSL_RAW_DATA));
        $publicData['merchantid']      = $payInfo[0];
        $publicData['timestamp'] = time();
        $publicData['sign'] = md5($publicData['merchantid'] . $publicData['timestamp'] . $payConf['md5_private_key']);

        $postJson = json_encode($publicData);
        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($postJson),
        ];
        $postData['data']         = $postJson;
        $postData['httpHeaders']  = $header;
        $postData['merchantorderid'] = $data['merchantorderid'];
        $postData['applyamount']  = $data['applyamount'];
        unset($reqData);
        return $postData;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == '0') {
            $data['qrCode'] = $data['data']['payurl'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        if(isset($res['merchantorderid'])){
            $bankOrder = PayOrder::getOrderData($res['merchantorderid']);//根据订单号 获取入款注单数据
            if (empty($bankOrder)) {
                //查询不到订单号时不插入回调日志，pay_id / pay_way 方式为0 ，关联字段不能为空
                File::logResult($request->all());
                return trans("success.{$mod}");
            }
            $payConf = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
            $payInfo = explode('@', $payConf['business_num']);
            $decryptData = openssl_decrypt(base64_decode($res['data']), 'AES-128-ECB', $payInfo[1], OPENSSL_RAW_DATA);
            $decryptData = json_decode($decryptData, 1, 512, JSON_BIGINT_AS_STRING);
            $data['merchantorderid'] = $res['merchantorderid'];
            $data['applyamount'] = $decryptData['applyamount'];
        }else{
            $data['merchantorderid'] = '';
            $data['applyamount'] = '';
        }
        return $data;
    }

    public static function signOther($model, $datas, $payConf)
    {
        $json = file_get_contents('php://input');  //获取POST流
        $data=json_decode($json,true);
        $payInfo = explode('@', $payConf['business_num']);
        $sign     = $data['sign'];
        $decryptData = openssl_decrypt(base64_decode($data['data']), 'AES-128-ECB', $payInfo[1], OPENSSL_RAW_DATA);
        $decryptData = json_decode($decryptData, 1, 512, JSON_BIGINT_AS_STRING);
        $signTrue  = md5($data['merchantorderid'] . $data['timestamp'] . $payConf['md5_private_key']);
        if (strtoupper($signTrue) == strtoupper($sign) && $decryptData['status'] == 2) {
            return true;
        }
        return false;
    }
}