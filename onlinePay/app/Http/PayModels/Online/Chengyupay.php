<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Chengyupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $icon = 'GB2312'; //返回信息字符编码

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
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';

        $data['pay_memberid'] = $payConf['business_num'];//商户号
        $data['pay_orderid'] = $order;//订单
        $data['pay_applydate'] = date("Y-m-d h:i:s");//订单时间
        $data['pay_bankcode'] = $bank;//支付类型
        $data['pay_notifyurl'] = $ServerUrl;
        $data['pay_callbackurl'] = $returnUrl;
        $data['pay_amount'] = number_format($amount, 2, '.', '');
        $signStr = self::getSignStr($data, true, true); //true1 为空不参与签名，true2排序
        $data['pay_md5sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        $data['pay_productname'] = 'vivo';

        unset($reqData);
        return $data;
    }
    public static function getQrCode($response)
        {
            $result = json_decode($response,true);
               if($result['pay_code'] == 'HL0000'){
                $result['pay_url'] =  $result['pay_url'];
                }
            return $result;
        }
}