<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Qidzfpay extends ApiModel
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

        self::$isAPP = true;
        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$httpBuildQuery = true;

        $data       = array(
            "fxid"        => $payConf['business_num'], //商户号
            "fxddh"       => $order, //商户订单号
            "fxdesc"      => "test", //商品名
            "fxfee"       => $amount, //支付金额 单位元
            "fxattch"     => 'mytest', //附加信息 会员账号或者ID
            "fxnotifyurl" => $ServerUrl, //异步回调 , 支付结果以异步为准
            "fxbackurl"   => $returnUrl, //同步回调 不作为最终支付结果为准，请以异步回调为准
            "fxpay"       => $bank, //支付类型 微信H5网页 wxwap 微信扫码：wxsm 支付宝H5网页：zfbwap 支付宝扫码：zfbsm
            "fxip"        => self::getIp(), //支付端ip地址
            'fxbankcode'  => '', //用于网银直连模式，请求的银行编号，参考银行附录,仅网银接口可用。
            'fxbankcard'  => '', //用于银联快捷直连模式,仅银联快捷接口可用。
            'fxsmstyle'   => '1', //用于扫码模式（sm），仅带sm接口可用，默认0返回扫码图片，为1则返回扫码跳转地址。
            'fxfs'        => '',
        );
        if ((int)$payConf['pay_way'] === 1) {
            $data['fxpay'] = 'pcbank';
            $data['fxbankcode'] = $bank;
        }
        $data["fxsign"]      = md5($data["fxid"] . '&' . $data["fxddh"] . '&' . $data["fxfee"] . '&' . $data["fxnotifyurl"] . '&' . $payConf['md5_private_key']); //加密

        unset($reqData);
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['fxsign'];
        $signTrue  = md5($data['fxstatus'] . '&' . $data['fxid'] . '&' . $data['fxddh'] . '&' . $data['fxfee'] . '&' . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['fxstatus'] == '1') {
            return true;
        }
        return false;
    }

}
