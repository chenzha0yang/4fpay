<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Juhejypay extends ApiModel
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

        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$isAPP          = true;
        self::$unit           = 2;
        self::$resType        = 'other';

        $data                 = [];
        $data['MerId']        = $payConf['business_num'];
        $data['MerOrderNo']   = $order;
        $data['PayType']      = $bank;
        $data['MerOrderTime'] = date("YmdHis");
        $data['Amount']       = $amount * 100;
        $data['UserId']       = time();
        $data['GoodsName']    = 'ys';
        $data['GoodsDesc']    = 'ys';
        $data['GoodsRemark']  = 'ys';
        $data['NotifyUrl']    = $ServerUrl;
        $data['SuccessUrl']   = $returnUrl;
        $data['TimeoutUrl']   = $returnUrl;
        $data['ErrorUrl']     = $returnUrl;
        $data['Sign']         = strtoupper(md5(strtoupper(md5($data['Amount'] . '|' . $data['MerOrderNo'] . '|' . $data['MerOrderTime'] . '|' . $payConf['md5_private_key']))));
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $data              = $request->all();
        $res['Amount']     = $data['Amount'] / 100;
        $res['MerOrderNo'] = $data['MerOrderNo'];
        return $res;
    }

    public static function getQrCode($data)
    {
        $result = json_decode($data, true);
        if ($result['code'] == "success") {
            $res['PayUrl'] = $result['data']['PayUrl'];
        } else {
            $res['code']    = $result['code'];
            $res['message'] = $result['message'];
        }
        return $res;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $mysign = strtoupper(md5(strtoupper(md5($data['Amount'] . '|' . $data['PayOrderNo'] . '|' . $data['PayOrderTime'] . '|' . $payConf['md5_private_key']))));
        if ($mysign == $data['Sign'] && $data['PayStatus'] == 'success') {
            return true;
        } else {
            return false;
        }
    }
}
