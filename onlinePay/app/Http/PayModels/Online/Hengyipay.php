<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Hengyipay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something
        self::$action = 'curlPost';

        $data = array(
            'pay_memberid'  => $payConf['business_num'],            //商户号
            'pay_orderid'  => $order,                               //订单号
            'pay_amount'  => $amount,                               //金额
            'pay_applydate'  => date('Y-m-d H:i:s'),         //提交时间
            'pay_bankcode'  =>  $bank,                              //支付类型
            'pay_notifyurl' => $ServerUrl,                          //异步通知地址
            'pay_callbackurl'  => $returnUrl,                       //同步通知地址
        );

        if($payConf['pay_way'] == '1'){
            $data['pay_bankcode'] = 'YHK';
        }


        ksort($data);                               //ASCII码排序
        reset($data);                               //定位到第一个下标
        $md5str = "";
        foreach ($data as $key => $val) {
            $md5str = $md5str.$key."=>".$val."&";
        }
        $md5str .= "key=".$payConf['md5_private_key'];
        $data['pay_md5sign'] = strtoupper(md5($md5str));

        $data['pay_requestIp'] = self::getIp();
        if($data['pay_bankcode'] == 'WXZF'){
            $data['tongdao'] = 'HYWXD0';
        }elseif($data['pay_bankcode'] == 'ALIPAY'){
            $data['tongdao'] = 'HYALID0';
        }elseif($data['pay_bankcode'] == 'YHK'){
            $data['tongdao'] = 'HYBANKD0';
        }
        $data['return_type'] = '1';
        unset($reqData);
        return $data;
    }

    public static function getQrCode($json)
    {
        $data = json_decode($json,true);//dd($data);
        if($data['code'] == 1){
            static::$result['appPath'] =$data['pay_url'];
            self::$pc = true;
        } else {
            static::$result['code'] =$data['code'];
            static::$result['msg'] =$data['msg'];
        }
    }

    /**
     * @param $type    string 模型名
     * @param $data    array  回调数据
     * @param $payConf array  商户信息
     * @return bool
     */
    public static function SignOther($type, $data ,$payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data);                               //ASCII码排序
        reset($data);                               //定位到第一个下标
        $md5str = "";
        foreach ($data as $key => $val) {
            $md5str = $md5str.$key."=>".$val."&";
        }
        $md5str .= "key=".$payConf['md5_private_key'];
        $signValue = strtoupper(md5($md5str));
        if ( strtoupper($signValue) == strtoupper($sign) && $data['returncode'] == '00' ) {
            return true;
        } else {

            return false;
        }
    }

    public static function callback($request)
    {
        echo 'ok'; // 接收callback 即同步返回 success msg

        $data = $request->all();
        if (!$payConf = self::getPayConf($data['orderid'])) return false;


        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data);                               //ASCII码排序
        reset($data);                               //定位到第一个下标
        $md5str = "";
        foreach ($data as $key => $val) {
            $md5str = $md5str.$key."=>".$val."&";
        }
        $md5str .= "key=".$payConf['md5_private_key'];
        $signValue = strtoupper(md5($md5str));



        if ( strtoupper($signValue) == strtoupper($sign) && $data['returncode'] == '00' ) {
            static::$amount = $data['amount']; // 下发金额设置 （注意单位）
            return true;
        } else {
            self::addCallbackMsg($data, $data['amount']);
            return false;
        }
    }
}