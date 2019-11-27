<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Zhichengpay extends ApiModel
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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        $data = array();
        $data['apiVersion'] = '1.0.0.0';//接口版本
        $data['platformID'] = $payConf['pay_id'];//商户(合作伙伴)ID
        $data['merchNo'] = $payConf['business_num'];//商户账号
        $data['orderNo'] = $order;//商户订单号
        $data['tradeDate'] = date('Ymd');
        $data['amt'] = number_format($amount, 2);
        $data['merchUrl'] = $ServerUrl;//支付结果通知地址
        $data['merchParam'] = 'aa';
        $data['tradeSummary'] = 'name';
        if ($payConf['pay_way'] != '1') {
            $data['overTime'] = '100';
            $data['customerIP'] = '127.0.0.1';//支付结果通知地址
        }

        if (!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['merchUrl'])) {
            $data['merchUrl'] = iconv("GBK", "UTF-8", $data['merchUrl']);
        }

        if (!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['merchParam'])) {

            $data['merchParam'] = iconv("GBK", "UTF-8", $data['merchParam']);
        }

        if (!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['tradeSummary'])) {
            $data['tradeSummary'] = iconv("GBK", "UTF-8", $data['tradeSummary']);
        }
        $signStr = self::getSignStr($data, true);
        $data['sign'] = self::getMd5Sign($signStr, $payConf['md5_private_key']);
        if ($payConf['pay_way'] == '1') {
            $data['bankCode'] = $bank;
            $data['choosePayTy'] = '1';
            $data['act'] = 'postsub';
        }

        unset($reqData);
        return $data;
    }
}
