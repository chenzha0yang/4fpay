<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Weixiaopay extends ApiModel
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

        $data = array();
        $data['MerchantID'] = $payConf['business_num'];             //商户号
        $data['Memo'] = 'dindan';                                   //订单备注
        if ( $payConf['pay_way'] == '1' ) {
            $data['PayMode'] = 'Bank';                              //支付方式
            $data['Bank'] = "";                                     //银行代码
        }else{
            $data['PayMode'] = $bank;                               //支付方式
            $data['Bank'] = $bank;                                  //银行代码
        }
        $data['OrderNo'] = $order;                                  //商户网站唯一订单号
        $data['OrderAmount'] = $amount;                             //交易金额
        $data['Goods'] = 'zhif';                                    //商品名称
        $data['NotifyUrl'] = $ServerUrl;                            //支付成功后，异步通知支付结果地址
        $data['ReturnUrl'] = $returnUrl;
        $data['EncodeType'] = "SHA2";                               //签名方式
        $SHA2Key = $payConf['rsa_private_key'];
        $HashIV  = $payConf['public_key'];

        $SignData = "SHA2Key=".$SHA2Key."&bank=".$data['Bank']."&encodeType=".$data['EncodeType']."&goods=".$data['Goods']."&merchantId=".$data['MerchantID']."&notifyUrl=".$data['NotifyUrl']."&orderAmount=".$data['OrderAmount']."&orderNo=".$data['OrderNo']."&payMode=".$data['PayMode']."&returnUrl=".$data['ReturnUrl']."&HashIV=".$HashIV;

        $SignData = urlencode($SignData);                           //签名明文以 URL 编码
        $SignData = strtolower($SignData);                          //签名明文urlencode编码后转小写
        $sign = hash("sha256", $SignData);
        $data['signSHA2'] = strtoupper($sign);                      //签名
        unset($reqData);
        return $data;
    }

    //回调处理
    public static function SignOther($mod, $data, $payConf)
    {
        $SignData = "SHA2Key=".$payConf['private_key']."&encodeType=".$data['encodeType']."&merchantId=".$data['merchantId']."&orderAmount=".$data['orderAmount']."&orderNo=".$data['orderNo']."&payMode=".$data['payMode']."&tradeNo=".$data['tradeNo']."&HashIV=".$payConf['public_key'];
        $SignData = urlencode($SignData);    //签名明文以 URL 编码
        $SignData = strtolower($SignData);   //签名明文urlencode编码后转小写
        $signs = hash("sha256", $SignData);
        $sign = strtoupper($signs);//签名

        if(strtoupper($sign) == strtoupper($data['signSHA2']) && $data['success'] == 'Y'){
            return true;
        } else {
            return false;
        }
    }
}