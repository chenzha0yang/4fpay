<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Ruijietongpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $pay['version']         = 'V2.0.0.0'; # 版本号
        $pay['merNo']           = $payConf['business_num']; #商户号
        $pay['netway']          = $bank; #WX 或者 ZFB
        $pay['random']          = (string) rand(1000, 9999); #4位随机数    必须是文本型
        $pay['orderNum']        = "20" . $order; #商户订单号
        $pay['amount']          = (string) ($amount * 100); #默认分为单位 转换成元需要 * 100   必须是文本型
        $pay['goodsName']       = 'abcd'; #商品名称
        $pay['charset']         = 'utf-8'; # 系统编码
        $pay['callBackUrl']     = $ServerUrl; #通知地址 可以写成固定
        $pay['callBackViewUrl'] = ""; #暂时没用
        ksort($pay); #排列数组 将数组已a-z排序
        $sign                = strtoupper(md5(json_encode($pay) . $payConf['md5_private_key']));
        $post                = array('data' => json_encode($pay));

        unset($reqData);
        return $post;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['stateCode'] == '00') {
            $data['qrCode'] = $data['qrcodeUrl'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['amount'])) {
            $arr['amount'] = $arr['amount'] / 100;
        }
        if(isset($arr['orderNum'])){
            $arr['orderNum']= substr($arr['orderNum'], 2);
        }
        
        return $arr;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $data      = json_decode(stripslashes($datas['data']), true);
        $sign = $data['sign'];
        unset($data['sign']);

        $arr     = array();
        foreach ($data as $key => $v) {
            if ($key !== 'sign') {
                #删除签名
                $arr[$key] = $v;
            }
        }
        ksort($arr);
        $signTrue = strtoupper(md5(json_encode($arr) . $payConf['md5_private_key'])); #生成签名

        if (strtoupper($sign) == strtoupper($signTrue) && $data['payResult']== '00') {
            return true;
        }
        return false;
    }


}