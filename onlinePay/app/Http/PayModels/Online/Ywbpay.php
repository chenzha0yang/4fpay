<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Ywbpay extends ApiModel
{
    public static $method = 'get'; //提交方式 必加属性 post or get

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

        $data = [];
        $data['version']      = '2.0';//版本号
        $data['customer_id'] = $payConf['business_num'];        //商户号
        $data['order_no'] = $order;          //商户订单号
        $data['money'] = number_format($amount,2);;            //金额
        $data['type'] = $bank;              //银行类型
        $data['bank_code'] = '';              //银行编码
        $data['callback_url'] = $ServerUrl;      //下行异步通知地址
        $data['sync_url'] = $returnUrl;      //下行同步通知地址
        $signStr = 'version='.$data['version'].'&customer_id='.$data['customer_id'].'&money='.$data['money'].'&order_no='.$data['order_no'].'&callback_url='.$data['callback_url'].'&sync_url='.$data['sync_url'].'&'.$payConf['md5_private_key'];
        $postKey = self::getMd5Sign("{$signStr}", '');
        $data['remark']       = '';//描述
        $data["sign"] = $postKey;
        unset($reqData);
        return $data;
    }
}