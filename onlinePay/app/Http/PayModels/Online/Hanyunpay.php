<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Hanyunpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $is_app = '';

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

        //TODO: do something
        self::$reqType  = 'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$postType = true;

        $data              = [];
        $data['appid']     = $payConf['business_num']; //商户号
        $data['orderno']   = $order; //订单号
        $data['amount']    = sprintf("%.2f", $amount); //金额
        $data['remark']    = $order;
        $data['paytype']   = $bank; //支付类型
        $data['returnurl'] = $ServerUrl;
        $signStr           = self::getSignStr($data, true, true);
        $data['sign']      = strtoupper(self::getMd5Sign($signStr . '&key=', $payConf['md5_private_key']));
        $post['data']      = json_encode($data);
        $post['orderno']   = $data['orderno'];
        $post['amount']    = $data['amount'];
        unset($reqData);
        return $post;
    }

    public static function getRequestByType($post)
    {
        return $post['data'];
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign    = $data['sign'];
        unset($data['sign']);
        $str    = "amount={$data['amount']}&appid={$data['appid']}&orderno={$data['orderno']}&remark={$data['remark']}";
        $mySign = strtoupper(md5($str . '&key=' . $payConf['md5_private_key']));
        if ($sign == $mySign && $data['result_code'] == '200') {
            return true;
        }
        return false;
    }
}