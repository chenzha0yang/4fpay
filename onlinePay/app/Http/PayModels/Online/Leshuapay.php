<?php
/**
 * Created by PhpStorm.
 * User: JS-00036
 * Date: 2018/9/14
 * Time: 14:56
 */

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Leshuapay extends ApiModel
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
        $order     = $reqData['order'];  //订单号
        $amount    = $reqData['amount'];  //金额
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$unit = 2;
        if($payConf['is_app'] == 1){
            self::$isAPP = true;
        }

        $data = [
            'version'    => '1.0',                        //版本
            'customerid' => $payConf['business_num'],     //商户号
            'sdorderno'  => $order,                       //订单号
            'total_fee'  => number_format($amount,2,'.',''),
            'notifyurl'  => $ServerUrl,                   //异步通知地址
            'returnurl'  => $returnUrl,                   //同步通知地址
            'remark'     => '',
        ];
        if ( $payConf['pay_way'] == '1' ) { //网银
            $data['paytype'] = 'bank';
            $data['bankcode'] = $bank;
        } else {
            $data['paytype'] = $bank;
            $data['bankcode'] = '';
        }

        $signStr = "version={$data['version']}&customerid={$data['customerid']}&total_fee={$data['total_fee']}&sdorderno={$data['sdorderno']}&notifyurl={$data['notifyurl']}&returnurl={$data['returnurl']}&{$payConf['md5_private_key']}";
        $data['sign'] = md5($signStr); //加密

        unset($reqData);
        return $data;
    }





}