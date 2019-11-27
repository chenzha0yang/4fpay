<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use function Couchbase\defaultDecoder;

class Shunzhifupay extends ApiModel
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

        $order     = $reqData['order']; //订单号
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something
//        self::$method = 'get';
//        $data = array(
//            'app_id'  => $payConf['business_num'],          //商户ID
//            'method'  => $bank,                             //接入类型
//            'sign_type' => 'MD5',                           //签名类型
//            'version'  => '1.0',                            //接口版本
//            'content'  => [],                               //业务参数集合
//        );
//        if ($payConf['pay_way'] == 1) {
//            $data['content'] = array(
//                'out_trade_no'  => $order,                      //订单号
//                'total_amount'  => sprintf("%.2f",$amount),                 //订单金额
//                'order_name'    => 'miaosu',                    //订单描述
//                'spbill_create_ip' => $_SERVER["REMOTE_ADDR"],  //用户端IP
//                'notify_url'  => $ServerUrl,                    //异步通知地址
//                'return_url'  => $returnUrl,                    //同步通知地址
//                'channel_type' => '07',                         //渠道类型
//                'subject'  => 'title',                          //订单标题
//                'bank_code' => $bank,
//            );
//        } else {
//            $data['content'] = array(
//                'out_trade_no'  => $order,                      //订单号
//                'total_amount'  => sprintf("%.2f",$amount),                 //订单金额
//                'order_name'    => 'miaosu',                    //订单描述
//                'spbill_create_ip' => '127.0.0.1',  //用户端IP
//                'notify_url'  => $ServerUrl,                    //异步通知地址
//                'return_url'  => $returnUrl,                    //同步通知地址
//            );
//        }
//
//        $data['content'] = json_encode($data['content']);
//        ksort($data);
//        $signStr = '';
//        foreach ($data as $k => $v) {
//            $signStr .= $k . '=' . $v . '&';
//        }
//        $signStr = $signStr . 'key=' . $payConf['md5_private_key'];
//        $data['sign'] = md5($signStr);
//        foreach ($data as $key => $val) {
//            $data[$key] = urlencode($val);
//        }






        $data = array();
        $data['app_id'] = $payConf['business_num'];//商户ID,请在后台获取
        $data['version'] = '1.0';//版本

        $content = array(
            'out_trade_no'=>$order,//商户订单号
            'order_name'=>'vivo',//商品描述
            'total_amount'=>sprintf("%.2f",$amount),//总金额
            'spbill_create_ip'=>'127.0.0.1',//用户端ip
            'notify_url'=>$ServerUrl,//异步回调地址
            'return_url'=>$returnUrl,//同步回调地址
        );
        if($payConf['pay_way'] == '1'){
            $content['channel_type'] = '07';
            $content['subject'] = 'zhif';
            $content['bank_code'] = $bank;
            $data['method'] = 'gateway';//支付方式
        }else{
            $data['method'] = $bank;//支付方式
        }
        $sysParams = array_merge(['content' => json_encode($content)], $data);//合并数组
        ksort($sysParams);
        $string = '';
        foreach($sysParams as $key => $val){
            $string .= $key.'='.$val.'&';
        }
        $sing = md5($string.'key='.$payConf['md5_private_key']);

        $data['content'] = json_encode($content);
        $data['sign'] = $sing;
        $data['sign_type'] = "MD5";
        unset($reqData);
        return $data;
    }

}