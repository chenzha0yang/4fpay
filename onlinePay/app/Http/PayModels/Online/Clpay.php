<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Clpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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

        self::$method = 'get';
        self::$postType = true;
        self::$unit = 2;

        $res = explode('@', $payConf['business_num']);
        $data['mercNo']         = $res[0];
        $data['orderNo']       = $order;
        $data['orderAmt']      = $amount * 100;
        $data['goodsName']     = 'goods';
        $data['notifyUrl']   = $ServerUrl;
        $data['pageReturnUrl'] = $returnUrl;

        $signStr               = self::getSignStr($data, true, true);
        $data['sign']          = strtoupper(md5($signStr . $payConf['md5_private_key']));
        $str = self::getSignStr($data, true, true);
        $str = base64_encode($str);
        $str = str_replace('+', '=J=', $str);
        $str = str_replace('/', '=X=', $str);

        $result['data'] = $str;

        unset($reqData);
        return $result;
    }


    public static function getRequestByType ($data) {

        $str = self::getSignStr($data, true, true);
        $str = base64_encode($str);
        $str = str_replace('+', '=J=', $str);
        $str = str_replace('/', '=X=', $str);
        $result['data'] = $str;
        return $result;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        $arr['orderAmt'] = $arr['orderAmt'] / 100;
        return $arr;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign    = $data['sign'];
        unset($data['sign']);
        $signStr            = self::getSignStr($data, true, true);
        $mysign             = strtoupper(md5($signStr . $payConf['md5_private_key']));
        if (strtoupper($sign) == $mysign && $data['orderState'] == '01') {
            return true;
        } else {
            return false;
        }
    }


}