<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xunfupay extends ApiModel
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

        $type = $bank;
        if($payConf['pay_way'] == '1'){
            $bank = 'ChengYiOnlineBank';
            $type = 'ChengYiOnlineBank';
        }
        $data = array();
        $data['pay_amount'] = $amount; //金额
        $data['pay_applydate'] = date('Y-m-d h:i:s', time()); //时间
        $data['pay_bankcode'] = $bank; //银行类型
        $data['pay_callbackurl'] = $returnUrl; //同步回调地址
        $data['pay_memberid'] = $payConf['business_num']; //商户id
        $data['pay_notifyurl'] = $ServerUrl; //异步回调地址
        $data['pay_orderid'] = $order; //商户订单号
        $signStr = '';
        foreach ($data as $key => $value) {
            $signStr .= "{$key}=>{$value}&";
        }
        $data['pay_md5sign'] = strtoupper(self::getMd5Sign("{$signStr}key=", $payConf['md5_private_key']));
        $data['tongdao'] = $type;
        unset($reqData);
        return $data;
    }
}