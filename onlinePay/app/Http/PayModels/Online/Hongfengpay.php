<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Hongfengpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

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

        self::$reqType='curl';
        self::$method='get';
        self::$payWay = $payConf['pay_way'];
        self::$isAPP = true;
        self::$unit =2;
        self::$resType ='other';

        $data = array(
            'subject'  => 'goodsname',
            'body'     => '商品',
            'productId'     => $bank,
            'notifyUrl' => $ServerUrl,
            'mchOrderNo'    => $order,
            'currency'   => 'cny',
            'amount'      => (Int)$amount * 100,
            'clientIp'    => self::getIp(),
            'returnUrl' => $returnUrl,
            'mchId'        => $payConf['business_num']
        );
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(md5($signStr . "&key=" . $payConf['md5_private_key']));
        //$result['params'] = json_encode($data,320);
        $result['params'] = json_encode($data,320);
        $result['mchOrderNo']=$order;
        $result['amount'] = $amount;
        unset($reqData);
        return $result;
    }
    public static function getQrCode($request)
    {
        $res =  json_decode($request,true);
        if(isset($res['payParams']['payUrl'])){
            $res['payUrl'] =$res['payParams']['payUrl'];
        }
        return $res;
    }
    public static function getVerifyResult($request, $mod)
    {
        $data = $request->all();
        $data['amount'] = $data['amount'] / 100;
        return $data;
    }
    //回调处理
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data,true,true);
        $signTrue = md5($signStr ."&key=".$payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['status'] == '2') {
            return true;
        }
        return false;
    }

}


