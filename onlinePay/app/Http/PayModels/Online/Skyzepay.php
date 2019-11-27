<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Skyzepay extends ApiModel
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
     * jokin
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

        //TODO: do something
        $data   = array(
            //业务类型
            'p0_Cmd'          => 'Buy',
            //商户编号
            'p1_MerId'        => $payConf['business_num'],
            //商户订单号
            'p2_Order'        => $order,
            //支付金额
            'p3_Amt'          => sprintf('%.2f', $amount),
            //交易币种
            'p4_Cur'          => 'CNY',
            'p5_Pid'          => '',
            'p6_Pcat'         => '',
            'p7_Pdesc'        => '',
            'p8_Url'          => $ServerUrl,
            'p9_SAF'          => '',
            'pa_MP'           => '',
            'pd_FrpId'        => 'paydesk',//跳到收银台
            'pr_NeedResponse' => '',
        );
        $string = '';
        foreach ($data as $key => $value) {
            $string .= $value;
        }

        $sign = self::HmacMd5($string, $payConf['md5_private_key']);

        $data['hmac'] = $sign; //签名

        unset($reqData);
        return $data;
    }

    /**
     * @param $type    string 模型名
     * @param $data    array  回调数据
     * @param $payConf array  商户信息
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $string    = $data['p1_MerId'] . $data['r0_Cmd'] . $data['r1_Code'] . $data['r2_TrxId'] . $data['r3_Amt'] . $data['r4_Cur'] . $data['r5_Pid']
            . $data['r6_Order'] . $data['r7_Uid'] . $data['r8_MP'] . $data['r9_BType'];
        $signValue = self::HmacMd5($string, $payConf['md5_private_key']);
        if (strtoupper($signValue) == strtoupper($data['hmac'])) {
            return true;
        } else {
            return false;
        }
    }

    public static function HmacMd5($data, $key)
    {
        $key  = iconv("GB2312", "UTF-8", $key);
        $data = iconv("GB2312", "UTF-8", $data);

        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*", md5($key));
        }
        $key   = str_pad($key, $b, chr(0x00));
        $ipad  = str_pad('', $b, chr(0x36));
        $opad  = str_pad('', $b, chr(0x5c));
        $kpad  = $key ^ $ipad;
        $koPad = $key ^ $opad;

        return md5($koPad . pack("H*", md5($kpad . $data)));
    }
}
