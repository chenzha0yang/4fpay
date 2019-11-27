<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yonglipay extends ApiModel
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


        if (!strpos($payConf['md5_private_key'],'#')) {
            self::$errorData['order']  = $order;
            self::$errorData['amount'] = $amount;
            self::$errorData['msg']    = '商户MD5秘钥或src_code缺失，格式：MD5秘钥#src_code';
            return [];
        }
        $mchInfo = explode('@', $payConf['md5_private_key']);
        $data         = array(
            'src_code'     => $mchInfo[1],
            'out_trade_no' => $order,
            'total_fee'    => $amount * 100,
            'time_start'   => date('YmdHis'),
            'goods_name'   => 'goodsName',
            'trade_type'   => $bank,
            'finish_url'   => $returnUrl,
            'mchid'        => $payConf['business_num'],

        );
        $signStr      = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(md5($signStr . '&key=' . $mchInfo[0]));
        unset($reqData);
        return $data;
    }


    public static function getQrCode($res)
    {
        $arr               = json_decode($res, true);
        $return            = [];
        $return['respcd']  = $arr['respcd'];
        $return['respmsg'] = $arr['respmsg'];

        if ($arr['respcd'] == '0000') {
            $return['pay_params'] = $arr['data']['pay_params'];
        }
        return $return;
    }

    public static function getVerifyResult($request, $mod)
    {
        $data                = $request->all();
        $res['total_fee']    = $data['total_fee'] / 100;
        $res['out_trade_no'] = $data['out_trade_no'];

        return $res;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $mchInfo = explode('@', $payConf['md5_private_key']);
        $signStr = self::getSignStr($data, true, true);
        $mySign  = strtoupper(md5($signStr . '&key=' . $mchInfo[0]));

        if ($sign == $mySign && $data['order_status'] == '3') {
            return true;
        } else {
            return false;
        }
    }
}