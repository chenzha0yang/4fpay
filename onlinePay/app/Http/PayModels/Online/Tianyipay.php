<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tianyipay extends ApiModel
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

        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        if( $payConf['is_app'] == '1') {
            self::$isAPP = true;
        }

        $data = [];
        $data['app_id'] = $payConf['business_num'];         //应用编号
        if ( $payConf['pay_way'] != '1' ) {
            $data['pay_type'] = $bank;                      //支付方式
        } else {
            $data['bank_code'] = $bank;
        }
        $data['order_id'] = $order;                         //商户订单号
        $data['order_amt'] = $amount;                       //订单金额
        $data['notify_url'] = $ServerUrl;                   //支付结果异步通知URL
        $data['return_url'] = $returnUrl;                   //支付返回URL
        $data['time_stamp'] = date('YmdHis');               //时间戳
        $signStr = self::getSignStr($data, false ,false);
        $data['sign']= self::getMd5Sign("{$signStr}&key=", md5($payConf['md5_private_key']));
        if ( $payConf['pay_way'] != '1' ) {
            $data['goods_num'] = 'zhif';                    //商品数量
            $data['goods_note'] = 'zhif';                   //商品说明
            $data['user_ip'] = '47.106.19.205';             //用户ip
        } else {
            $data['goods_name'] = 'zhif';                   //商品名称
            $data['card_type'] = '1';                       //银行卡类型
        }
        unset($reqData);
        return $data;
    }

    //回调处理
    public static function SignOther($mod, $data, $payConf)
    {
        $key = md5($payConf['md5_private_key']);
        $sign = md5("app_id=".$data['app_id']."&order_id=".$data['order_id']."&pay_seq=".$data['pay_seq']."&pay_amt=".$data['pay_amt']."&pay_result=".$data['pay_result']."&key=".$key);

        if(strtoupper($sign) == strtoupper($data['sign']) && $data['pay_result'] == '20'){
            return true;
        } else {
            return false;
        }
    }
}