<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Haofubaopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        //TODO: do something
        self::$method = 'get';
        $data = [];
        $data['p0_Cmd']          = 'Buy';                           //业务类型
        $data['p1_MerId']        = $payConf['business_num'];        //商户号
        $data['p2_Order']        = $order;                          //商户订单号
        $data['p3_Amt']          = $amount;                         //支付金额 元
        $data['p4_Cur']          = 'CNY';                           //交易币种
        $data['p5_Pid']          = 'name';                          //商品名称
        $data['p6_Pcat']         = 'class';                         //商品种类
        $data['p7_Pdesc']        = 'desc';                          //商品描述
        $data['p8_Url']          = $ServerUrl;                      //商户接收支付成功数据的地址
        $data['pa_MP']           = '1';                             //商户扩展信息
        $data['pd_FrpId']        = $bank;                           //支付通道编码
        $data['pr_NeedResponse'] = '1';
        $hmac = self::getReqHmacString($data, $payConf['md5_private_key']);
        $data['hmac'] = $hmac;
        unset($reqData);
        return $data;
    }

    //排序签名
    public static function getReqHmacString($data, $merchantKey)
    {
        $sbOld = "";
        foreach ($data as $val) {
            $sbOld .= $val;
        }
        return self::HmacMd5($sbOld, $merchantKey);
    }

    public static function HmacMd5($data, $key)
    {
        //需要配置环境支持iconv，否则中文参数不能正常处理
        $key = iconv("GB2312", "UTF-8", $key);
        $data = iconv("GB2312", "UTF-8", $data);
        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*", md5($key));
        }
        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $kiPad = $key ^ $ipad;
        $koPad = $key ^ $opad;
        return md5($koPad . pack("H*", md5($kiPad . $data)));
    }

    // 回调处理
    public static function SignOther($type, $sign, $payConf)
    {
        if ($sign['hmac'] == self::getReqHmacString($sign, $payConf['md5_private_key'])) {
            return true;
        } else {
            return false;
        }
    }
}