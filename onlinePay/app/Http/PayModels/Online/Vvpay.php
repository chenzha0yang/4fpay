<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Vvpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false;
    private static $signStr; // 判断是否跳转APP 默认false

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

        //TODO: do something

        $data              = array();
        $data['cid']       = $payConf['business_num']; //商户id
        $data['order']     = $order; //订单号
        $data['fee']       = sprintf('%.2f', $amount);
        $data['paytype']   = $bank; //支付类型:
        $data['notifyurl'] = $ServerUrl; //支付完成通知回调 url
        $data['returnurl'] = $returnUrl;
        $signStr           = $data['cid'] . $data['paytype'] . $data['order'] . $data['fee'] . $data['returnurl'] . $data['notifyurl'] . $payConf['md5_private_key'];
        $data['safe']      = md5($signStr);
        unset($reqData);
        return $data;
    }
}
