<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Heshengpay extends ApiModel
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

        if($payConf['pay_way'] == '1'){
            $ApiNamePay = 'TRADE.B2C';//网银
        } else{
            $ApiNamePay = 'TRADE.SCANPAY';//扫码
        }

        $data = array();
        $data['service']    = $ApiNamePay;// 商户APINMAE，WEB渠道一般支付
        $data['version']    = '1.0.0.0';// 商户API版本
        $data['merId']      = $payConf['business_num'];// 商户在支付平台的的平台号
        if($payConf['pay_way'] != '1'){
            $data['typeId'] = $bank;   // 接收扫码代码
        }
        $data['tradeNo']    = $order;//商户订单号
        $data['tradeDate']  = date('Ymd');// 商户订单日期
        $data['amount']     = sprintf("%.2f", $amount); // 商户交易金额 元
        $data['notifyUrl']  = $ServerUrl;// 商户通知地址
        $data['extra']      = 'extra';// 商户扩展字段
        $data['summary']    = 'summary';// 商户交易摘要
        $data['expireTime'] = 60 * 30;//超时时间 秒
        $data['clientIp']   = '127.0.0.1';//客户端ip
        if($payConf['pay_way'] == '1'){
            $data['bankId'] = $bank;   // 接收银行代码
        }
        // 将中文转换为UTF-8
        if(!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['notifyUrl'])){
            $data['notifyUrl'] = iconv("GBK","UTF-8", $data['notifyUrl']);
        }
        if(!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['extra'])){
            $data['extra'] = iconv("GBK","UTF-8", $data['extra']);
        }
        if(!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['summary'])){
            $data['summary'] = iconv("GBK","UTF-8", $data['summary']);
        }

        $signStr       = self::getSignStr($data, false);//true1 为空不参与签名，true2排序
        $data['sign']  = self::getMd5Sign($signStr, $payConf['md5_private_key']);

        if($payConf['pay_way'] != '1'){
            self::$payWay = $payConf['pay_way'];
            self::$reqType = 'curl'; // 扫码
            self::$resType = 'other'; //返回数据类型
            self::$httpBuildQuery = true;
        }
        unset($reqData);
        return $data;
    }

    /**
     * @param $resultData
     * @return array
     */
    public static function getQrCode($resultData)
    {
        $respCode = $respDesc = '';
        $code = [];
        preg_match('{<code>(.*?)</code>}', $resultData, $match);
        if(isset($match[1]) && !empty($match[1])){
            $respCode = $match[1];
        }
        // 响应信息
        preg_match('{<desc>(.*?)</desc>}', $resultData, $match);
        if(isset($match[1]) && !empty($match[1])){
            $respDesc = $match[1];
        }
        if($respCode == '00'){
            preg_match('{<qrCode>(.*?)</qrCode>}', $resultData, $match);
            if(isset($match[1]) && !empty($match[1])){
                $respqrCode= $match[1];
            }
            $base64 =$respqrCode;
            $codeUrl = base64_decode($base64);
            $code['codeUrl'] = $codeUrl;
            $code['resultCode'] = '0000';
        }else{
            $code['error_msg'] = $respDesc;
            $code['error_code'] = $respCode;
            $code['success_code'] = '';
        }
        return $code;
    }
}