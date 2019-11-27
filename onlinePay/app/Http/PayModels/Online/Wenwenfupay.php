<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Extensions\SignCheck;
use Illuminate\Http\Request;

class Wenwenfupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str   other

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = true; //默认 false  true为curl提交参数 需要http_build_query

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

        if($bank == 'wxH5'){
            self::$resType = 'other';
        }

        //支付类型
        $payArrOne = ['wxPub', 'wxMicro', 'wxApp', 'qqQr', 'geteway','wxPubQR'];
        $payArrTwo = ['alipayQR','alipayH5'];

        $data['tradeType']   = 'cs.pay.submit';//交易类型
        $data['version']     = '1.5';//版本
        $data['channel']     = $bank;//支付类型
        $data['mchId']       = $payConf['business_num'];//商户号
        $data['body']        = 'vivo';//商品描述
        $data['outTradeNo']  = $order;//订单
        $data['amount']      = sprintf('%.2f', $amount);
        $data['timePaid']    = date('YmdHis');//订单创建时间
        $data['notifyUrl']   = $ServerUrl;
        $data['callbackUrl'] = $returnUrl;
        $data['settleCycle'] = '0';
        if (in_array($bank,$payArrOne)) {
            $data['limitPay']    = 'no_credit';
        }
        if (in_array($bank,$payArrTwo)) {
            $data['limitPay']    = 'moneyFund,balance,bankPay,pcredit,creditCard';
        }
        $signStr             = self::getSignStr($data, true, true);
        $data['sign']        = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        $json                = json_encode($data);

        self::$reqType = 'curl';
        self::$method  = 'header';
        self::$payWay  = $payConf['pay_way'];

        if ($payConf['is_app'] == 1 || $reqData['isApp'] == 1) {
            self::$isAPP = true;
        }
        $post['data']        = $json;
        $post['httpHeaders'] = array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($json)
        );
        $post['outTradeNo'] = $data['outTradeNo'];
        $post['amount']     = $data['amount'];
        unset($reqData);
        return $post;
    }


    public static function getVerifyResult(Request $request, $mod)
    {
        $json = $request->getContent();
        $data = json_decode($json, true);
        return $data;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $signText            = strtoupper($data['sign']);
        $data['amount'] = sprintf('%.2f', $data['amount']);
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        if ($signTrue == $signText && $data['status'] == '02' && $data['resultCode'] == '0') {
            SignCheck::$sign['respCode'] = 'SUCCESS';
            return true;
        } else {
            SignCheck::$sign['respCode'] = '';
            return false;
        }

    }

    public static function getQrCode($response)
    {
        $data = json_decode($response,true);
        if($data['returnCode'] == 0 && $data['resultCode'] == 0){
            echo $data['payCode'];
            exit();
        }
        return $data;
    }

}