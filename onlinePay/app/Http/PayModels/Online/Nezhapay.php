<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Nezhapay extends ApiModel
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

        $data['uid'] = $payConf['business_num'];
        $data['price'] = $amount;
        $data['paytype'] = $bank;
        $data['notify_url'] = $ServerUrl;
        $data['return_url'] = $returnUrl;
        $data['user_order_no'] = $order;
        $data['tm'] = date('Y-m-d h:i:s');
        $data['sign'] = md5($data['uid'].$data['price'].$data['paytype'].$data['notify_url'].$data['return_url'].$data['user_order_no'].$payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr=$request->getContent();
        $data = json_decode($arr,true);
        self::$array = $data;
        return $data;
    }

    //回调处理
    public static function SignOther($mod, $data, $payConf)
    {
        $data = self::$array;
        $sign = $data['sign3'];
        $isSign = strtoupper(md5('user_order_no='.$data['user_order_no'].'&orderno='.$data['orderno'].'&tradeno='.$data['tradeno'].'&price='.$data['price'].'&realprice='.$data['realprice'].'&status='.$data['status'].'&token='.$payConf['md5_private_key']));
        if (strtoupper($sign) == $isSign && $data['status'] == '3') {
            return true;
        }
        return false;
    }


}