<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yixinzfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $UserName = '';

    public static $changeUrl = false;
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
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        self::$changeUrl = true;
        //TODO: do something

        $data['appid']     = $payConf['business_num'];//商户号
        $data['currency_type']      = 'CNY';
        $data['order_type'] = 1;
        $data['out_trade_no']     = $order;//订单号
        $data['pay_id']     = $bank;//银行编码
        $data['return_url'] = $returnUrl;
        $data['sign_type'] = 'MD5';
        $data['total_fee']       = sprintf('%.2f',$amount);//订单金额
        $data['version'] = '1.0';
        $data['goods_name'] = base64_encode('test');
        $signStr                 = self::getSignStr($data, true, true);
        $data['sign']            = md5($signStr . $payConf['md5_private_key']);
        $data['user_id']   = self::$UserName;
        $data['notify_url']   = $ServerUrl;
        $data['pay_ip'] = self::getIp();

        $post['queryUrl'] = $reqData['formUrl'].'/pay/create';
        $post['data'] = $data;
        unset($reqData);
        return $post;
    }

    public static function getVerifyResult($request, $mod)
    {
        $res                = $request->getContent();
        $data = json_decode($res, true);

        return $data;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $json    = file_get_contents("php://input");
        $data = json_decode($json,true);
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = md5($signStr . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['resp_code'] == '00') {
            return true;
        }
        return false;
    }


}