<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yafupay extends ApiModel
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

        if($payConf['pay_way'] == '1'){
            $payType = '0101';
        }else{
            $payType = $bank;
            $bank = '';
        }
        $data = array();
        $data['version']    = '3.0';//固定值:3.0
        $data['consumerNo'] = $payConf['business_num'];//平台提供的商户编号
        $data['merOrderNo'] = $order;//客户方生成的订单编号,不能重复
        $data['transAmt']   = $amount;//支付金额,单位:元,保留两位小数
        $data['backUrl']    = $ServerUrl;//支付完成时回调客户方结果接收地址,属于服务器与服务器之间的后台异步通知接口
        $data['frontUrl']   = $returnUrl;//当采用页面跳转提交时,可提供前台通知地址,支付结果将同步跳转到该地址
        $data['bankCode']   = $bank;//网关支付必传
        $data['payType']    = $payType;//支付方式
        $data['goodsName']  = 'chongzhi';//
        $data['merRemark']  = '';//
        $data['buyIp']      = '';//
        $data['buyPhome']   = '';//
        $data['shopName']   = '';//
        $signStr = self::getSignStr($data, true, true);
        $data['sign']= self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }
}