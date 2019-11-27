<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Kuaihuiyunpay extends ApiModel
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
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        $data = array(
            'p0_Cmd'          => 'Buy',
            'p1_MerId'        => $payConf['business_num'],
            'p2_Order'        => $order,
            'p3_Amt'          => sprintf('%.2f', $amount),
            'p4_Cur'          => 'CNY',
            'p5_Pid'          => 'goodsName',
            'p6_Pcat'         => 'PK',
            'p7_Pdesc'        => 'PKGoodsName',
            'p8_Url'          => $ServerUrl,
            'p9_SAF'          => '0',
            'pa_MP'           => '',
            'pd_FrpId'        => $bank,
            'pr_NeedResponse' => '1'
        );
        $data['hmac'] = self::getReqHmacString($data, $payConf['md5_private_key']);  //调用签名函数生成签名串
        unset($reqData);
        return $data;
    }


    /**
     * @param $data
     * @param $Key
     * @return string
     */
    public static function getReqHmacString($data, $Key)
    {
        ksort($data);
        $sbOld = "";
        foreach ($data as $value) {
            $sbOld .= $value;
        }
        return self::HmacMd5($sbOld,$Key);

    }

    /**
     * [HmacMd5 description]
     * @param [type] $data [description]
     * @param [type] $key  [description]
     * @return string
     */
    public static function HmacMd5($data,$key)
    {
        //需要配置环境支持iconv，否则中文参数不能正常处理
        $key = iconv("GB2312","UTF-8",$key);
        $data = iconv("GB2312","UTF-8",$data);

        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*",md5($key));
        }
        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $kiPad = $key ^ $ipad ;
        $koPad = $key ^ $opad;

        return md5($koPad . pack("H*",md5($kiPad . $data)));
    }

    /**
     * 回调处理
     * @param [type] $type    [description]
     * @param [type] $sign    [description]
     * @param [type] $payConf [description]
     * @return bool
     */
    public static function SignOther($type, $sign, $payConf)
    {
        if($sign['hmac'] == static::getReqHmacString($sign,$payConf['private_key'])) {
            return true;
        }else{
            return false;
        }
    }

}