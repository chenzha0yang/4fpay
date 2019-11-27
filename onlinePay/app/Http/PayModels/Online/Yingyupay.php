<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yingyupay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址
        //TODO: do something
        if ( $payConf['pay_way'] != '1' ) {
            self::$reqType = 'curl';
            self::$payWay = $payConf['pay_way'];
        }
        self::$unit = '2';
        // self::$resType = 'other';
        $data = [];
        $data['messageid'] = '200001';
        $data['back_notify_url'] = $ServerUrl;                   //..异步通知地址
        $data['branch_id'] = $payConf['business_num'];           //..商户号
        $data['nonce_str'] = self::randstr(32);                  //..随机数
        $data['out_trade_no'] = $order;                          //..订单号
        $data['prod_desc'] = 'proddesc';
        $data['prod_name'] ='prodname';
        $data['pay_type'] = $bank;                               //..支付类型
        $data['total_fee'] = $amount * 100;                      //..金额
        if ($payConf['pay_way'] == '1') {                        //..网银
            $data['pay_type'] = '30';
            $data['bank_flag'] = '0';
            $data['bank_code'] = $bank;
            $data['messageid'] = '200002';
            $data['front_notify_url'] = $returnUrl;              //..同步通知地址
        }
        ksort($data);
        $str = urldecode(http_build_query($data));
        $data['sign'] = strtoupper(self::getMd5Sign($str."&key=", $payConf['md5_private_key']));
        foreach($data as $k => $v) {
            $array[$k] = urlencode($v);
        }
        $DataStr = urldecode(json_encode($array));
        unset($reqData);
        return $DataStr;
    }

    //随机数
    public static function randstr($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $res = '';
        for ($i = 0; $i < $length; $i++) {
            $random = mt_rand(0, strlen($chars)-1);
            $res .= $chars{$random};
        }
        return $res;
    }

    //提交处理
    public static function getQrCode($response)
    {
        $result = json_decode($response,true);
        dd($result);
        return $result;
    }
}