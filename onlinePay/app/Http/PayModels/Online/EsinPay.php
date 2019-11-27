<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class EsinPay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str   other

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
        $signStr = '';
        $data = array(
            'P_UserID' => $payConf['business_num'],         //商户ID
            'P_OrderID' => $order,                          //订单号
            'P_CardID'  =>  '',                             //充值卡号
            'P_CardPass' => '',                             //充值卡密
            'P_FaceValue'  => sprintf("%.2f", $amount),  //金额
            'P_ChannelID'  => $bank,                        //支付方式
        );
        foreach ($data as $k => $v) {
            $signStr .= $v . '|';
        }
        $data['P_PostKey'] = md5($signStr . $payConf['md5_private_key']);
        $data['P_Subject'] = 'dulex';           //商品标题
        $data['P_Price'] = $amount;             //商品售价
        $data['P_ISsmart'] = 0;                 //固定值0
        $data['P_Result_URL'] = $ServerUrl;     //异步通知地址
        $data['P_Notify_URL'] = $returnUrl;     //同步通知地址
        unset($reqData);
        return $data;
    }

}