<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Ypay extends ApiModel
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
        self::$isAPP = true;
        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$changeUrl = true;

        $data['aid']     = (int)$payConf['business_num'];//商户号
        $data['price']       = sprintf('%.2f',$amount);//订单金额
        $data['out_order_id']     = $order;//订单号
        $data['type']     = $bank;//银行编码
        $data['notifyurl']   = $ServerUrl;
        $data['returnurl'] = $returnUrl;
        $signStr = '';
        foreach ($data as $key => $value) {
            $signStr .= $value;
        }
        $data['sign']            = md5(md5($signStr).$payConf['md5_private_key']);
        $data['out_uid'] = self::$UserName;

        $postData['queryUrl'] = $reqData['formUrl'].'/addons/pay/api/create';
        $postData['data'] = http_build_query($data);
        $postData['out_order_id'] = $data['out_order_id'];
        $postData['price'] = $data['price'];
        unset($reqData);
        return $postData;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == 1) {
            $data['qrCode'] = $data['data']['payurl'];
        }
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        $signStr  = $data['order_id'].$data['out_order_id'].$data['price'].$data['realprice'].$data['type'].$data['paytime'].$data['extend'];
        $signTrue = md5(md5($signStr).$payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue)) {
            return true;
        }
        return false;
    }


}