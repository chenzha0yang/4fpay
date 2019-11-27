<?php

namespace App\Http\PayModels\Online;

use App\Extensions\File;
use App\ApiModel;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;

class Xinkuaipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $array = [];

    public static $changeUrl = true;

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

        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$method  = 'header';


        //TODO: do something
        $data['ip'] = self::getIp();
        $data['outerOrderId'] = $order;
        $data['username'] = $payConf['business_num'];  //商户号
        $data['submitAmount'] = $amount;
        $data['payType'] = $bank;
        $data['callbackUrl'] = $ServerUrl;

        $req['data']=base64_encode(openssl_encrypt(json_encode($data), 'AES-128-ECB', $payConf['md5_private_key'], OPENSSL_RAW_DATA));
        $req['timestamp'] =time();
        $req['username'] =  $payConf['business_num'];
        $req['remark'] =  $order;
        $req['sign'] = md5($req['username'].$req['timestamp'].$req['data'].$payConf['md5_private_key']);

        $postData['data']         = json_encode($req);
        $postData['httpHeaders']  = [
            'Content-Type: application/json',
        ];
        $postData['outerOrderId'] = $data['outerOrderId'];
        $postData['submitAmount']  = $data['submitAmount'];
        $postData['queryUrl'] = $reqData['formUrl'].'/pay/api/create';
        unset($reqData);
        return $postData;

    }

    //回调处理数据 解密
    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $data = json_decode($arr, true);
        $bankOrder = PayOrder::getOrderData($data['remark']);//根据订单号 获取入款注单数据
        if (empty($bankOrder)) {
            //查询不到订单号时不插入回调日志，pay_id / pay_way 方式为0 ，关联字段不能为空
            File::logResult($arr);
            return trans("success.{$mod}");
        }
        $payConf   = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息

        $json = openssl_decrypt(base64_decode($data['data']), 'AES-128-ECB', $payConf['md5_private_key'], OPENSSL_RAW_DATA);
        $array = json_decode($json,true);
        $array['submitAmount']  = sprintf('%.3f', $array['submitAmount']);
        $array['sign']  = $data['sign'];
        $array['timestamp'] = $data['timestamp'];
        self::$array = $array;
        return $array;
    }

    //回调处理
    public static function SignOther($mod, $data, $payConf)
    {
        $data = self::$array;
        $sign = $data['sign'];

        $isSign = md5($data['orderId'].$data['outerOrderId'].$data['submitAmount'].$data['timestamp']);
        if (strtoupper($sign) == strtoupper($isSign) && $data['status'] == '1') {
            return true;
        }
        return false;
    }

}