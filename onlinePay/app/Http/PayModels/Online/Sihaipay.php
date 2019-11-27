<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Sihaipay extends ApiModel
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


        $data['p1_MerId']    = $payConf['business_num']; //商户号
        $data['p0_Cmd'] = 'Buy';
        $data['p2_Order']     = $order; //订单号
        $data['p3_Amt']      = sprintf('%.2f',$amount);; //订单金额
        $data['p4_Cur'] = 'CNY'; //交易币种
        $data['p5_Pid'] = 'Jiefubao'; //商品名称
        $data['p6_Pcat'] = 'abc'; //商品种类
        $data['p7_Pdesc'] = 'abc'; //商品描述
        $data['pa_MP'] = 'abc'; //商户扩展信息
        $data['p8_Url']   = $ServerUrl; //服务端通知
        $data['pd_FrpId'] = $bank;
        $data['pr_NeedResponse'] = '1';
        $stringSignTemp          = self::getSignStr($data, true,true);
        $data['hmac']            = self::HmacMd5($stringSignTemp, $payConf['md5_private_key']);

        if(!self::$isAPP){
            self::$reqType = 'curl';
            self::$payWay  = $payConf['pay_way'];
            self::$httpBuildQuery = true;
        }
        unset($reqData);
        return $data;
    }


    public static function HmacMd5($data, $key)
    {
        $key  = iconv("GB2312", "UTF-8", $key);
        $data = iconv("GB2312", "UTF-8", $data);

        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*", md5($key));
        }
        $key    = str_pad($key, $b, chr(0x00));
        $ipad   = str_pad('', $b, chr(0x36));
        $opad   = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack("H*", md5($k_ipad . $data)));
    }


    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign     = $data['hmac'];
        $signStr  = $data['p1_MerId'] . $data['r0_Cmd'] . $data['r1_Code'] . $data['r2_TrxId'] . $data['r3_Amt'] . $data['r4_Cur'] .
            $data['r5_Pid'] . $data['r6_Order'] . $data['r7_Uid'] . $data['r8_MP'] . $data['r9_BType'];
        $signTrue = self::HmacMd5($signStr, $payConf['md5_private_key']);
        if (strtolower($sign) == strtolower($signTrue) && $data['r1_Code'] == '1') {
            return true;
        } else {
            return false;
        }
    }
}