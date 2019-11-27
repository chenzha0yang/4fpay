<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class AImispay extends ApiModel
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

        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType = 'other';
        self::$unit = '2';

        if($payConf['pay_way'] == '1'){
            $bank = '80101';
            self::$isAPP = true;
        }
        $data = [];
        $data['src_code'] = $payConf['message1'];   //商户唯一标识
        $data['out_trade_no'] = $order;             //商户交易订单号
        $data['total_fee'] = $amount * 100;           //订单总金额，单位分
        $data['time_start'] = date('YmdHis');       //发起交易的时间
        $data['goods_name'] = $order;               //商品名称
        $data['trade_type'] = $bank;                //交易类型：微信扫码:50104;支付宝扫码:60104;
        $data['finish_url'] = $ServerUrl;           //支付完成页面的url
        $data['mchid'] = $payConf['business_num'];  //商户号
        $signStr = self::getSignStr($data, false , true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response,true);
        if( $result['respcd'] == '0000' ) {
            $result['pay_params'] = $result["data"]["pay_params"];
        }
        return $result;
    }
}



