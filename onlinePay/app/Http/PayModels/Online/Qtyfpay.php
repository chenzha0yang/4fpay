<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Common;


class Qtyfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $data = [];

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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址




        //TODO: do something
        if($reqData['bank']=='wxwap' || $reqData['bank']=='alipaywap'){
            self::$reqType = 'form';
            self::$isAPP = true;
        }else{
            self::$reqType = 'curl';
            self::$payWay  = $payConf['pay_way'];
            self::$httpBuildQuery = true;
            self::$resType = 'other';
        }


        $data                    = [];
        $data['p0_Cmd'] = 'Buy';
        $data['p4_Cur'] = 'CNY';
        $data['p1_MerId'] = $payConf['business_num'];//商户号
        $data['pd_FrpId'] = $bank;//银行编码
        $data['p2_Order'] = $order;//订单号
        $data['p3_Amt'] = $amount;//订单金额
        $data['p8_Url'] = $ServerUrl;
        $data['p5_Pid'] = 'test';
        $data['p6_Pcat'] = 'test';
        $data['p7_Pdesc'] = 'test';
        $data['pa_MP'] = 'test';
        $stringSignTemp          = self::getSignStr($data, true, true);
        $data['hmac']            = self::HmacMd5("{$stringSignTemp}", $payConf['md5_private_key']);
        //$data['hmac']            = self::HmacMd5($stringSignTemp, $payConf['md5_private_key']);

        unset($reqData);
//        dd($data);
        return $data;

    }

    public static function getQrCode($response)
    {
        $responseData = json_decode($response,true);

            if(!empty($responseData['payImg']) && $responseData['status'] == '0'){
                $data['payImg'] = $responseData['payImg'];
                return $data;
            }else{
                $data['code'] = $responseData['status'];
                $data['msg'] = $responseData['Msg'];
                return $data;
            }

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
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['hmac'];
        unset($data['hmac']);
        unset($data['rb_BankId']);
        unset($data['ro_BankOrderId']);
        unset($data['rp_PayDate']);
        unset($data['rq_CardNo']);
        unset($data['ru_Trxtime']);
        ksort($data);
        $signStr = '';
        foreach ($data as $key => $value) {
            $signStr .= $value;
        }

        $signTrue           = self::HmacMd5("{$signStr}", $payConf['md5_private_key']);
        if (strtoupper($signTrue) == strtoupper($sign) && $data['r1_Code'] == '1') {
            return true;
        } else {
            return false;
        }
    }
}