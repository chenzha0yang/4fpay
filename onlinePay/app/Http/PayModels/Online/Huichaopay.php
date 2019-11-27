<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huichaopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

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

        //TODO: do something

        $data = array();
        $data['MerNo'] = $payConf['business_num']; //商户号
        $data['BillNo'] = $order; //[必填]订单号(商户自己产生：要求不重复)
        $data['Amount'] = $amount; //[必填]订单金额
        $data['OrderTime'] =date('YmdHis'); //[必填]交易时间YYYYMMDDHHMMSS
        if($payConf['pay_way'] == '1'){
            $data['ReturnURL'] = $returnUrl; //同步通知地址
        }
        $data['AdviceUrl'] =$ServerUrl; //[必填]支付完成后，后台接收支付结果，可用来更新数据库值
        $signStr = self::getSignStr($data,false);
        $data['SignInfo'] = strtoupper(self::getMd5Sign("{$signStr}&", $payConf['md5_private_key']));
        $data['remark'] = "100";  //[选填]升级。
        $data['products']="products";// '------------------物品信息
        if($payConf['pay_way'] == '1'){
            $data['defaultBankNumber'] = $bank; //[选填]银行代码
            unset($reqData);
            return $data;
        }else{
            $data['payType'] = $bank;   //[选填]银行代码
            $xml = '';
            $xml .= '<?xml version="1.0" encoding="utf-8"?>';
            $xml .= '<ScanPayRequest>';
            foreach ($data as $key => $value) {
                $xml .= "<{$key}>{$value}</{$key}>";
            }
            $xml .= '</ScanPayRequest>';
            $post['requestDomain'] = base64_encode($xml);
            $post['BillNo'] = $order; //页面展示订单号
            $post['Amount'] = $amount; //页面展示金额
            self::$reqType = 'fileGet';
            self::$payWay  = $payConf['pay_way'];
            self::$resType = 'xml';
            unset($reqData);
            return $post;
        }
    }
}