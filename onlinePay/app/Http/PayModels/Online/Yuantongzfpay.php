<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yuantongzfpay extends ApiModel
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
        $data['uid'] = $payConf['business_num'];//商户号
        $data['paytype'] = $bank;//银行编码
        $data['user_order_no'] = $order;//订单号
        $data['price'] = sprintf('%.2f',$amount);//订单金额
        $data['return_url'] = $returnUrl;
        $data['notify_url'] = $ServerUrl;
        $signStr =  $data['uid'].$data['price'].$data['paytype'].$data['notify_url'].$data['return_url'].$data['user_order_no'];
        $data['sign'] = strtolower(md5($signStr .$payConf['md5_private_key']));
        $data['tm'] = date('Y-m-d H:i:s',time());

        unset($reqData);
        return $data;
    }

     public static function getVerifyResult($request, $mod)
    {
        $res                = $request->getContent();
        $data = json_decode($res, true);

        return $data;
    }

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $datas, $payConf)
    {
        $json    = file_get_contents("php://input");
        $data = json_decode($json,true);
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr =  $data['user_order_no'] .$data['orderno'] .$data['tradeno'] .$data['price'] .$data['realprice'];
        $signTrue = md5($signStr .$payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue)) {
            return true;
        } else {
            return false;
        }
    }

}