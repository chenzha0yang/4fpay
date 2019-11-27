<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jiuyipay extends ApiModel
{
    public static $action = 'formPost';//提交方式

    public static $header = ''; //自定义请求头

    public static $pc = ''; //pc端直接跳转链接

    public static $imgSrc = '';

    public static $changeUrl = ''; //自定义请求地址

    public static $amount = 0; // callback  amount

    /**
     * @param array $reqData 接口传递的参数
     * @param array $payConf
     * @return array
     */
    public static function getData($reqData, $payConf)
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
        $data = array(
            'mch_id'        => $payConf['business_num'],        //商户号
            'out_trade_no'  => $order,                          //订单号
            'body'          => 'VIP',
            'callback_url'  => $returnUrl,
            'notify_url'    => $ServerUrl,                      //异步通知地址
            'total_fee'     => sprintf("%.2f",$amount), //金额
            'way'           => 'pay',
            'format'        => 'xml',
            'mch_create_ip' => self::getIP(),
        );
        if($payConf['pay_way'] == '1'){
            $data['service']   = 'wy';
            $data['goods_tag'] = $bank;
        }else{
            $data['service']   = $bank;
            $data['goods_tag'] = '';
        }
        //判断是否需要跳转链接  is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            $data['way'] = 'wap';
        }
        $signStr      = $data['mch_id'].$data['out_trade_no'].$data['callback_url'].$data['notify_url'].$data['total_fee'].$data['service'].$data['way'].$data['format'].$payConf['md5_private_key'];
        $data['sign'] = md5($signStr);


        unset($reqData);
        return $data;
    }


    public static function callback($request)
    {

        echo 'SUCCESS';

        $data = $request->all();

        $payConf = static::getPayConf($data['out_trade_no']);
        if (!$payConf) return false;

        $md5 = strtoupper(md5( $data['mch_id'].$data['time_end'].$data['out_trade_no'].$data['ordernumber'].$data['transtypeid'].$data['transaction_id'].$data['total_fee'].$data['service'].$data['way'].$data['result_code']. $payConf['md5_private_key']));

        if ($md5 == strtoupper($data['sign']) && $data['result_code'] == '0') {
            static::$amount = $data['total_fee'];  //注意转换成元
            return true;
        } else {
            static::addCallbackMsg($request);
            return false;
        }
    }
}