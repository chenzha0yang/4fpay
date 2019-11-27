<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Dabofupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

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

        $data                 = [];
        $data['amount']       = $amount;//订单金额 元
        $data['pay_channel']  = $bank;//支付方式
        $data['merchant_no']  = $payConf['business_num'];//商户号
        $data['request_no']   = $order;//订单号
        $data['request_time'] = time();//下单请求时间戳
        $data['nonce_str']    = self::nonceStr();//随机字符串
        //$data['body'] = '测试一下', //非必要
        if ($payConf['pay_way'] == '1') {
            $data['return_url'] = $returnUrl;//页面通知地址
            $data['bankname']   = $bank;
        }
        $data['notify_url']   = $ServerUrl;//异步回调地址
        $data['account_type'] = '1';
        $data['sign']         = self::sign($data, $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    private static function nonceStr($len = 12)
    {
        $arr = array(
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
            'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y', 'Z',
        );
        $str = '';
        for ($i = 1; $i <= $len; $i++) {
            $str .= $arr[mt_rand(0, 35)];
        }
        return $str;
    }

    private static function sign($params, $key)
    {
        if (isset($params['sign'])) unset($params['sign']);
        $arr = array_diff($params, ['']);
        ksort($arr);
        return strtoupper(md5(urldecode(http_build_query($arr)) . "&key=" . $key));
    }
}