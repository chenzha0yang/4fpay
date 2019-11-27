<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Haolianpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something

        if($payConf['pay_way'] == '1'){
            $paytype = 'bank';
        }else{
            $paytype = $bank;
            $bank = '';
        }

        $data = [];
        $data['version'] = '1.0';                                            //版本号
        $data['customerid'] = $payConf['business_num'];                      //商户编号
        $data['total_fee'] = number_format($amount,2,'.','');
        $data['sdorderno'] = $order;			                             //商户订单号
        $data['notifyurl'] = $ServerUrl;			                         //异步通知URL
        $data['returnurl'] = $returnUrl;			                         //同步跳转URL
        $singStr  =  self::getSignStr($data);
        $sign  =  self::getMd5Sign("{$singStr}&", $payConf['md5_private_key']);
        $data['sign'] = $sign;					                             //md5签名串
        $data['get_code'] = '0';					                         //获取微信二维码
        $data['paytype'] = $paytype;	                                     //支付方式
        $data['remark'] = '';						                         //订单备注说明
        $data['bankcode'] = $bank;				                             //银行编号
        unset($reqData);
        return $data;
    }
}