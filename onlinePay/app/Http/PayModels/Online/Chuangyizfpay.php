<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Chuangyizfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $UserName='';

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

        $data['member_id']    = $payConf['business_num'];//商户编号
        $data['order_id'] =   $order; //订单号
        $data['user_name']  = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        $data['order_money']  = $amount;//商户订单号
        $data['istype']  = $bank;//支付类型
        $data['notify_url']  = $ServerUrl;//异步通知URL
        $data['key']      = md5("{$data['istype']}{$data['member_id']}{$data['notify_url']}{$data['order_id']}{$data['order_money']}{$data['user_name']}{$payConf['md5_private_key']}");
        unset($reqData);
        return $data;
    }



    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['key'];
        unset($data['key']);
        $mySign =  md5( $data['money'].$data['order_id'].$data['realprice'].$data['status'].$payConf['md5_private_key']);
        if ($sign == $mySign && $data['status'] == 200) {
            return true;
        } else {
            return false;
        }
    }
}