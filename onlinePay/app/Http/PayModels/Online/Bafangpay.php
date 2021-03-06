<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Bafangpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        self::$isAPP = true;
        self::$reqType = 'fileGet';
        self::$unit = 2;
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';

        //TODO: do something
        list($t1, $t2) = explode(' ', microtime());
        $time =(float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);

        $data = array(
            'goodsname'  => 'goodsname',
            'type'     => 'ali',
            'notify' => $ServerUrl,
            'order'    => $order,
            'ver'   => '2',//版本号,目前固定值为2
            'amount'      => $amount * 100,
            'ip' => self::getIp(),
            'uuid'      => $payConf['business_num'],
            'mid'        => $payConf['business_num'],
            'time'       => $time
        );
        $signStr      = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    public static function getQrCode($response){
        $responseData = json_decode($response,true);
        return $responseData;
    }

    public static function getVerifyResult($request, $mod)
    {
        $data = $request->all();
        $data['amount'] = $data['amount'] / 100;
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr =  self::getSignStr($data, true,true);
        $signTrue = strtoupper(md5($signStr."&key=".$payConf['md5_private_key'])); //MD5签名
        if (strtoupper($sign) == $signTrue && $data['pay_result'] == '1') {
            return true;
        } else {
            return false;
        }
    }


}