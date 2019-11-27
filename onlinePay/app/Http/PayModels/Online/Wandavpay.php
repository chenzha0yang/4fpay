<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Wandavpay extends ApiModel
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
        
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        
        $data               = [];
        $data['UserId'] = $payConf['business_num'];//商户号
        $data['Mode'] = 8;//支付类型
        $data['BankCode'] = $bank;
        $data['Message'] = 'goods';
        $data['ReturnUrl'] = $returnUrl;
        $data['CustomerId'] = $order;
        $data['Money'] = sprintf("%.2f",$amount);
        $data['CallBackUrl'] = $ServerUrl;
        $signStr = "BankCode={$data['BankCode']}&CallBackUrl={$data['CallBackUrl']}&CustomerId={$data['CustomerId']}&Message={$data['Message']}&Mode={$data['Mode']}&Money={$data['Money']}&ReturnUrl={$data['ReturnUrl']}&UserId={$data['UserId']}";
        $data['Sign']       = strtolower(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }
    public static function getQrCode($response)
    {
        $result = json_decode($response,true);
        if($result['code'] == '200'){
            $res['qrcode'] = $result['data']['payUrl'];
        } else {
            $res['code'] = $result['code'];
            $res['msg'] = $result['msg'];
        }
        return $res;
    }
    
    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $arr = json_decode($arr, true);
        return $arr;
    }
    
    public static function SignOther($model, $datas, $payConf)
    {
        $res = file_get_contents('php://input');
        $data = json_decode($res, true);
        $sign = $data['sign'];
        unset($data['sign']);
        
        $signStr = "CustomerId={$data['CustomerId']}&OrderId={$data['OrderId']}&Money={$data['Money']}&Status={$data['Status']}&Message={$data['Message']}&Type={$data['Type']}";
        $mySign      = strtolower(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        if (strtolower($sign) == $mySign && $data['status'] == 1) {
            return true;
        }
        return false;
        
    }
}