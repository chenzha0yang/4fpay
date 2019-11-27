<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Fivefupay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something
        self::$unit = 2;

        if ($payConf['pay_way'] == '1') {
            //网银
            if ($payConf['pay_code'] == '2000047') {
                $svcName = 'pcQuickPay'; //网银快捷 PC
            } else {
                $svcName = 'gatewayPay'; //网银支付
            }
        } else {
            $svcName = 'UniThirdPay';
        }
        $data                 = array();
        $data['svcName']      = $svcName; //服务名称
        $data['merId']        = $payConf['business_num']; //商户编号
        $data['merchOrderId'] = $order; //商户订单号
        $data['tranType']     = $bank; //银行编号
        $data['pName']        = 'pname'; //商品名称
        $data['amt']          = $amount * 100; //订单金额 单位分
        $data['notifyUrl']   = $ServerUrl; //异步通知URL
        $data['retUrl']      = $returnUrl; //同步跳转URL
        $data['showCashier'] = '1'; //1 跳转收银台，默认返回json
        $data['merData']     = 'chongzhi';
        ksort($data);
        $string = '';
        foreach ($data as $key => $val) {
            if (!empty($val)) {
                $string .= $val;
            }
        }
        $sign             = strtoupper(md5($string . $payConf['md5_private_key']));
        $data['md5value'] = $sign; //签名
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request){
        $res = $request->all();
        $res['amt'] = $res['amt'] / 100;
        return $res;
    }
}