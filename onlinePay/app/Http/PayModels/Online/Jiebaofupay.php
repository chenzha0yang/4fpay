<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jiebaofupay extends ApiModel
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
        $ServerUrl = str_replace("https","http",$ServerUrl);
        self::$method = 'get';

        $data['p0_Cmd']          = 'Buy'; //业务类型
        $data['p1_MerId']        = $payConf['business_num']; //商户号
        $data['p2_Order']        = $order; //商户订单号
        $data['p3_Amt']          = $amount; //支付金额 元
        $data['p4_Cur']          = 'CNY'; //交易币种
        $data['p5_Pid']          = 'name'; //商品名称
        $data['p6_Pcat']         = 'class'; //商品种类
        $data['p7_Pdesc']        = 'desc'; //商品描述
        $data['p8_Url']          = $ServerUrl; //商户接收支付成功数据的地址
        $data['pa_MP']           = '1'; //商户扩展信息
        $data['pd_FrpId']        = $bank; //支付通道编码
        $data['pr_NeedResponse'] = '1';
        $hmac                    = self::getReqHmacString($data, $payConf['md5_private_key']);
        $data['hmac']            = $hmac;
        unset($reqData);
        return $data;
    }


    public static function getReqHmacString($data, $merchantKey)
    {
        #进行签名处理，一定按照文档中标明的签名顺序进行
        $sbOld = "";
        foreach ($data as $val) {
            $sbOld .= $val;
        }
        return self::HmacMd5($sbOld, $merchantKey);
    }

    public static function HmacMd5($data, $key)
    {
        //需要配置环境支持iconv，否则中文参数不能正常处理
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


    public static function CheckHmac($signData, $p1_MerId, $merchantKey, $hmac)
    {
        if ($hmac == self::getCallbackHmacString($signData, $p1_MerId, $merchantKey)) {
            return true;
        } else {
            return false;
        }

    }

    public static function getCallbackHmacString($signData, $p1_MerId, $merchantKey)
    {
        #取得加密前的字符串
        $sbOld = $p1_MerId;
        foreach ($signData as $val) {
            $sbOld .= $val;
        }
        return self::HmacMd5($sbOld, $merchantKey);
    }



    public static function SignOther($type, $data, $payConf)
    {
        $signData['r0_Cmd']   = $data['r0_Cmd']; //业务类型 固定值”Buy”.
        $signData['r1_Code']  = $data['r1_Code']; //支付结果固定值“1”, 代表支付成功.
        $signData['r2_TrxId'] = $data['r2_TrxId']; //支付交易流水号
        $signData['r3_Amt']   = $data['r3_Amt']; //支付金额
        $signData['r4_Cur']   = $data['r4_Cur']; //交易币种 返回时是"RMB"
        $signData['r5_Pid']   = $data['r5_Pid']; //商品名称支付返回商户设置的商品名称.此参数如用到中文，请注意转码.
        $signData['r6_Order'] = $data['r6_Order']; //商户订单号
        $signData['r7_Uid']   = $data['r7_Uid']; //支付会员ID 如果用户使用的易宝支付会员进行支付则返回该用户的支付会员ID;反之为''.
        $signData['r8_MP']    = $data['r8_MP']; //商户扩展信息 此参数如用到中文，请注意转码.
        $signData['r9_BType'] = $data['r9_BType']; //交易结果返回类型 为“1”: 浏览器重定向; 为“2”: 服务器点对点通讯.
        $hmac                 = $data['hmac']; //签名数据
        $p1_MerId             = $payConf['business_num'];
        $merchantKey          = $payConf['md5_private_key'];
        #   判断返回签名是否正确（True/False）
        $bRet = self::CheckHmac($signData, $p1_MerId, $merchantKey, $hmac);
        if ($bRet && $signData['r1_Code'] == "1") {
            return true;
        } else {
            return false;
        }
    }

}