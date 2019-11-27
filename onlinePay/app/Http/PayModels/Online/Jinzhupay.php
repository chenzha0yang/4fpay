<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jinzhupay extends ApiModel
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

        self::$unit = 2;

        //TODO: do something
        $data['user_id']    = $payConf['business_num']; //商户号
        $data['order_no']     = $order; //订单号
        $data['amount']      = $amount*100; //订单金额
        $data['notify_url']   = $ServerUrl; //服务端通知

        $data['memo'] = 'Jinzhu'; //商品名称
        $data['type'] = $bank;
        $stringSignTemp          = self::getSignStr($data,true,true);
        $data['signature']     = strtoupper(md5($stringSignTemp . '&key=' . $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    /**
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult($request, $mod)
    {
        $requestData = $request->all();
        //$result = trans("backField.{$mod}");
        $res['order_no']  = $requestData['order_no'];
        $res['amount'] = $requestData['amount']/100;
        return $res;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign    = strtoupper($data['signature']);
        unset($data['signature']);
        $str    = self::getSignStr($data,true,true);
        $signTrue = strtoupper(md5($str . '&key=' . $payConf['md5_private_key']));
        if (strtoupper($sign) == $signTrue && $data['status'] =='1') {
            return true;
        } else {
            return false;
        }
    }

}
