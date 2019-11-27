<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Heibaopay extends ApiModel
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
        //判断是否需要跳转链接是否是手机端 is_app=1开启 2-关闭
//        if($payConf['is_app'] == 1){
//            self::$isAPP = true;
//        }
        self::$reqType          = 'curl';
        self::$httpBuildQuery   = true;
        self::$payWay           = $payConf['pay_way'];

        $data = [
            'appId'         => $payConf['business_num'],            //商户号
            'money'         => $amount,                             //充值金额
            'payType'       => $bank,                               //支付类型
            'orderNumber'   => $order,                              //商户订单号
            'notifyUrl'     => $ServerUrl,                          //异步通知URL
            'returnUrl'     => $returnUrl,                          //同步跳转URL
            'remark'        => '',                                  //备注信息（该备注信息会通过结果通知接口回调）
        ];
        $signStr = '';
        foreach ( $data as $val ) {
            if($val != ''){
                $signStr .= $val."&";
            }
        };
        $signStr            .= $payConf['md5_private_key'];
        $data['signature']   = strtoupper(md5($signStr)); //MD5签名

        unset($reqData);
        return $data;
    }


}