<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Shouyinbaopay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];

        $data = array(
            'third_num'      => $payConf['business_num'],   //商户号
            'order_num'       => $order,
            'time'     => time(),
            'code_type'      => $bank,
            'redirect_url'     => $ServerUrl,
            'attach'   => 'goods',
            'order_price'        => $amount,
            'cip' => self::getIp()

        );
        $signStr      = "{$data['third_num']}{$data['order_price']}{$data['order_num']}{$data['time']}{$data['redirect_url']}{$data['attach']}";

        $data['sign'] = strtolower(md5($payConf['md5_private_key'].md5($signStr))); //MD5签名

        unset($reqData);
        return $data;
    }


    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        $mySign = md5($payConf['md5_private_key'].$data['third_num'].$data['msg'].$data['order_number'].$data['price'].$data['time'].$data['state']);
        if (strtolower($sign) == strtolower($mySign) && $data['state'] == '1') {
            return true;
        } else {
            return false;
        }
    }

}