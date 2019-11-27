<?php

namespace App\Http\PayModels\Online;

use App\Extensions\File;
use App\ApiModel;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;

class Ruiyzfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $signStr = '';


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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址
        //TODO: do something

        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$postType = true;

        $data['payAmount'] = $amount;
        $data['commercialOrderNo'] = $order;
        $data['callBackUrl'] = $ServerUrl;
        $data['notifyUrl'] = $returnUrl;
        $data['payType'] = $bank;
        $data['ip'] = self::getIp();

        $json = json_encode($data);
        $req['parameter'] = base64_encode(openssl_encrypt($json, 'AES-256-ECB', $payConf['md5_private_key'], OPENSSL_RAW_DATA));
        $req['sign'] = md5($json);
        $req['platformno'] = $payConf['business_num'];
        $req['payAmount'] = $amount;
        $req['commercialOrderNo'] = $order;

        unset($reqData);
        return $req;
    }

    public static function getRequestByType($req)
    {
        unset($req['payAmount'],$req['commercialOrderNo']);
        return $req;
    }

    //回调处理数据 解密
    public static function getVerifyResult($request, $mod)
    {
        $data = $request->all();
        $bankOrder = PayOrder::getOrderData($data['commercialOrderNo']);//根据订单号 获取入款注单数据
        if (empty($bankOrder)) {
            //查询不到订单号时不插入回调日志，pay_id / pay_way 方式为0 ，关联字段不能为空
            File::logResult($data);
            return trans("success.{$mod}");
        }
        $payConf   = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
        $json = openssl_decrypt(base64_decode($data['parameter']), 'AES-256-ECB', $payConf['md5_private_key'], OPENSSL_RAW_DATA);
        self::$signStr = $json;
        $array = json_decode($json,true);
        return $array;
    }


    public static function signOther($mod, $data, $payConf)
    {
        $sign = $data['sign'];
        $signstr = md5(self::$signStr);
        if (strtoupper($sign) == strtoupper($signstr) && $data['result'] == 'success') {
            return true;
        } else {
            return false;
        }
    }

}