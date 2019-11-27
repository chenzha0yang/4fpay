<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huilfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $resData = [];

    /**
     * @param array       $reqData 接口传递的参数
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something

        $params     = array(
            'merchno' => $payConf['pay_id'],
            'amount'  => sprintf('%.2f', $amount),
            'traceno' => $order,
        );
        if ($payConf['pay_way'] == 9) {
            $params['interType']  = '2'; //接口类型固定值2
            $params['cardno']     = ''; //卡号
            $params['settleType'] = '2';
            $params['cardType']   = '1';
            $params['returnUrl']  = $returnUrl;
        } else {
            if ($payConf['pay_way'] == 1) {
                $params['channel']    = 2;
                $params['bankCode']   = $bank; //银行编码
                $params['settleType'] = 2;
            } else {
                $params['payType']    = $bank;
                $params['goodsName']  = 'huawei'; //商品名称
                $params['cust1']      = ''; //自定义域1 查询和通知的时候原样返回
                $params['cust2']      = ''; //自定义域2 查询和通知的时候原样返回
                $params['cust3']      = ''; //自定义域3 查询和通知的时候原样返回
                $params['settleType'] = '1'; //* 结算方式，默认1，1-T+1结算
            }
            if (self::$isAPP) {
                $params['ip'] = '127.0.0.1';
            }
        }
        $params['notifyUrl'] = $ServerUrl;
        $signStr      = self::getSignStr($params, true,true);
        $params['signature'] = strtoupper(self::getMd5Sign("{$signStr}&", $payConf['md5_private_key']));


        if ($payConf['pay_way'] != 1 && $payConf['pay_way'] != 9) {
            self::$reqType = 'curl';
            self::$payWay  = $payConf['pay_way'];
            self::$resType = 'other';
            self::$httpBuildQuery = true;
        }

        unset($reqData);
        return $params;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $res = iconv('GB2312', 'UTF-8', $response);
        $result = json_decode($res, true);
        if ($result['respCode'] == '00') {
            if (self::$isAPP) {
                $result['payUrl'] = $res['barCode'];
            } else {
                $result['codeUrl'] = $res['barCode'];
            }
        }
        return $result;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $signature = $data['signature'];
        unset($data['signature']);

        $signStr = self::getSignStr($data, true,true);
        $sign    = strtoupper(self::getMd5Sign("{$signStr}&", $payConf['md5_private_key']));

        if ($sign == $signature) {
            return true;
        } else {
            return true;
        }
    }
}