<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Abpay extends ApiModel
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
        //TODO: do something
        self::$unit = 2;

        $data['order_no'] = $order;//订单号
        $data['order_money'] = $amount*100;//订单金额
        $data['channel'] = $bank;//银行编码
        $data['sync_url'] = $returnUrl;
        $data['async_url'] = $ServerUrl;
        $data['extend'] = 'ab';

        $postData['merchantNo'] = $payConf['business_num'];
        $postData['businessType'] = 'order';
        $postData['timeStamp'] = time();
        $postData['ipAddr'] = self::getIp();
        $postData['data'] = base64_encode(json_encode($data));

        $signStr = self::getSignStr($postData,true,true);
        $postData['sign'] = strtoupper(md5($signStr.'&key='.$payConf['md5_private_key']));

        unset($reqData);
        return $postData;
    }

    //回调金额化分为元
    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        $data['order_no'] = $res['order_no'];
        $data['order_money'] = $res['order_money']/100;
        return $data;
    }

    public static function SignOther($type, $post, $payConf)
    {
        $post = file_get_contents('php://input');
        $data  = json_decode($post,true);
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data,true,true);
        $signTrue = md5($signStr);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['order_state'] == '82002') {
            return true;
        }
        return false;
    }

}