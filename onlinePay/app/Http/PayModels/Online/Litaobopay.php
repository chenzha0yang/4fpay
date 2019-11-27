<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Litaobopay extends ApiModel
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

        //TODO: do something
        if ( $payConf['pay_way'] == '1' ) {
            self::$method = "post";
        } else {
            self::$reqType = "curl";
            self::$httpBuildQuery = true;
            self::$payWay = $payConf['pay_way'];
            self::$resType = "other";
        }

        $data = [];
        $data['version'] = 'V1.0';                          //版本号
        $data['merNo'] = $payConf['business_num'];          //商户号
        $data['transTime'] = date("YmdHis",time());         //订单生成时间
        $data['orderNo'] = $order;//订单
        $data['amount'] = number_format($amount,2, '.', '');
        $data['memberIp'] = '127.0.0.1';
        if ( $payConf['pay_way'] == '1' ) {
            $data['tradeType'] = '4001';                    //网关
            $data['bankCode'] = '';                         //网关银行编码该字段不传，则跳转三方收银台
            $data['payType'] = 'BANK_B2C';
            $data['bankCardType'] = '';                     //bankCode有值时，该字段也不能为空
            $data['currencyCode'] = '1';                    //1-人民币
            $data['directFlag'] = '1';                      //直连银行
            $data['identityNo'] = '';
            $data['mobileNo'] = '';
        } else {
            $data['authCode'] = '';
            $data['tradeType'] = $bank;
            $data['subMerNo'] = '';
            $data['subMerName'] = '';
            $data['metaOption'] = '';
        }
        $data['notifyUrl'] = $ServerUrl;
        $data['returnUrl'] = $returnUrl;
        $data['goodsId'] = 'vivo';                          //商品描述
        $data['goodsInfo'] = 'x20';                         //附加信息
        $data['fileId1'] = $order;                          //备用字段，原值返回
        $data['fileId2'] = '';                              //备用字段，原值返回
        $data['fileId3'] = '';                              //备用字段，原值返回
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));//签名
        unset($reqData);
        return $data;
    }

    //二维码处理
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ( $result['respCode'] == '000A' ) {
            $result['qrcode'] = urldecode($result['qrcode']);
        }
        return $result;
    }
}