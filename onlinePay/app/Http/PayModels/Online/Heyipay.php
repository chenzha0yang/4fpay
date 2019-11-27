<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Heyipay extends ApiModel
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

        //TODO: do something

        $data = [];
        $data['apiName'] = "WEB_PAY_B2C";                  // 商户APINMAE，WEB渠道一般支付
        $data['apiVersion'] = "1.0.0.0";                  // 商户API版本
        $data['platformID'] = $payConf['business_num'];   // 商户在Mo宝支付的平台号
        $data['merchNo'] = $payConf['business_num'];      // Mo宝支付分配给商户的账号
        $data['orderNo'] = $order;
        $data['tradeDate'] = date("Ymd",time());     // 商户订单日期
        $data['amt'] = $amount;	                           // 商户交易金额
        $data['merchUrl'] = $ServerUrl;                   // 商户通知地址
        $data['merchParam'] = "abcd";	                   // 商户参数
        $data['tradeSummary'] = "chongzhi";	               // 商户交易摘要
        $strToSign = self::getSignStr($data, false);
        $data['signMsg'] = self::getMd5Sign($strToSign, $payConf['md5_private_key']);
        $data['bankCode'] = $bank;                        // 银行代码，不传输此参数则跳转Mo宝收银台

        if($payConf['pay_way'] != '1'){
            $data['bankCode'] = "";
            $data['choosePayType'] = $bank;
        }

        // 将中文转换为UTF-8
        if(!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['merchUrl'])){

            $data['merchUrl'] = iconv("GBK","UTF-8", $data['merchUrl']);
        }
        if(!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['merchParam'])){
            $data['merchParam'] = iconv("GBK","UTF-8", $data['merchParam']);
        }
        if(!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['tradeSummary'])){
            $data['tradeSummary'] = iconv("GBK","UTF-8", $data['tradeSummary']);
        }
        unset($reqData);
        return $data;
    }

}