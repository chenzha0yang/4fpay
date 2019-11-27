<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tongfupay extends ApiModel
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


        self::$unit = 2;
        $data       = [];
        $TF         = explode('@', $payConf['vircarddoin']);
        if ($payConf['pay_way'] == '1' || $payConf['pay_way'] == '9') {
            $data['bankLink']  = $bank;////银联号
            $data['cardType']  = '0';//卡类型
            $data['currency']  = 'CNY';//币种
            $data['returnUrl'] = $returnUrl;
            $data['signType']  = 'MD5';
            $data['command']   = 'GWUP001';   //网银支付
            if ($payConf['pay_way'] == '9') {
                $data['command'] = $bank;   //快捷支付
            }
        } else {
            $data['shopCode']          = '123';//商户门店编号
            $data['shopTerminateCode'] = 'G1';//商户机具终端编号
            $data['payeeName']         = 'name';//收款商户名称
            $data['command']           = $bank;//接口请求编码
        }

        $data['dateTime']     = date('YmdHis');//交易时间
        $data['groupCode']    = $TF['0'];//合作方编号
        $data['merchantCode'] = $payConf['business_num'];//合作方商户编号
        $data['notifyUrl']    = $ServerUrl;//异步通知 URL
        $data['orderNum']     = $order;//订单号
        $data['payMoney']     = $amount * 100;//交易金额，单位分
        $data['productName']  = 'name';//商品名称
        $data['terminalCode'] = $TF['1'];//终端编号

        ksort($data);
        $signval = "";
        while (list ($key, $val) = each($data)) {
            $val     = addslashes(strip_tags(htmlspecialchars(trim($val))));
            $signval .= $val;
        }
        $data['sign'] = md5($signval . $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

}