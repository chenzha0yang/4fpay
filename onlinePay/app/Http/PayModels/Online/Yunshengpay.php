<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yunshengpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    /**
     * @param array $reqData 接口传递的参数
     * @param array $payConf
     * @return array
     * jokin
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

        //TODO: do something

        if($payConf['pay_way'] == 1) {
            $PayType = '1';
        }elseif($payConf['pay_way'] == 2) {
            $PayType = '3';
        }elseif($payConf['pay_way'] == 3) {
            $PayType = '2';
        }
        
        $data['BillNo'] = $order;//商户订单编号
        $data['Amount'] = number_format($amount,2);         //订单金额
        $data['OrderDate'] = date('Ymdhis');         //订单日期
        $data['NotifyUrl'] = $ServerUrl;                    //异步返回的URL
        $data['ReturnUrl'] = $returnUrl;                    //支付结果成功返回的URL
        $data['Attach'] = 'chongzhi';                       //商户数据包
        $data['BankCode'] = $bank;                          //银行编号
        $data['MerchantId'] = $payConf['business_num'];     //商户编号
        $data['GoodsName'] = 'chongzhi';                    //商品名称
        $data['PayType'] = $PayType;                        //支付类型
        $signStr = $data['BillNo'].$data['Amount'].$data['OrderDate'];
        $postKey = strtolower(self::getMd5Sign("{$signStr}", $payConf['md5_private_key']));
        $data['Sign'] = $postKey;
        unset($reqData);
        return $data;
    }
}