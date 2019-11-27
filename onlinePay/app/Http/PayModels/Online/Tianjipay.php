<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tianjipay extends ApiModel
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
        self::$resType = 'other';
        self::$method  = 'header';
        self::$unit = '2';
        if( $payConf['is_app'] == '1' ) {
            self::$isAPP = true;
        }

        $data = [];
        $data['merchant_id'] = $payConf['business_num'];//商户号
        $data['order_id']    = $order;//流水号
        if ( $payConf['pay_way'] == '1' ) {
            $data['pay_type'] = '03';//交易类型
        } else {
            $data['pay_type'] = $bank;//交易类型
        }

        $data['bank_id']     = '';  //银行代码
        $data['trans_amt']   = $amount*100;//交易金额
        $data['back_url']    = $ServerUrl;//后台通知地址
        $data['front_url']   = '';
        $data['goods_title'] = 'apple';//商品标题
        $data['goods_desc']  = 'desc';//商品描述
        $data['send_ip']     = "118.163.212.115" ; //购买者Ip
        $data['send_time']   = date('Ymdhis');//订单日期
        $data['pay_desc']    = "1882" ; //购买 时间front_url
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = self::getMd5Sign($signStr.'&key=', $payConf['md5_private_key']);
        $post = [];
        $post['httpHeaders'] = [
            "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "content-type:application/json"
        ];
        $post['data'] = json_encode($data);
        $post['order_id'] = $data['order_id'];
        $post['trans_amt'] = $data['trans_amt'];
        unset($reqData);
        return $post;
    }

    //提交处理
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ( $result['ret_code'] == '200' && $result['ret_msg'] == 'success' ) {
             $result['pay_link'] = $result['result']['pay_link'];
        } else {
            $result['ret_code'] = $result['ret_code'];
            $result['ret_msg'] = $result['ret_msg'];
        }
        return $result;
    }

    //回调处理
    public static function SignOther($type, $data, $payConf)
    {
        $arrayData = json_decode($data, true);
        $signStr = self::getSignStr($arrayData, true, true);
        $sign = self::getMd5Sign($signStr.'&key=', $payConf['md5_private_key']);
        if ( $sign == $arrayData['sign'] && $arrayData['resp_code'] == '0000' ) {
           return true;
        } else {
            return false;
        }
    }
}