<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Gumapay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        //TODO: do something
        $data = [
            'order_no'   => $order,                   //订单号
            'pay_type'   => $bank,                    //银行类型
            'amount'     => sprintf('%2.f',$amount),                  //金额
            'return_url' => $returnUrl,
            'notify_url' => $ServerUrl,               //异步通知地址
            'remark'     => '',
        ];
        //商户ID
        $bid = $payConf['business_num'];
        //时间戳
        $_t  = self::getMillisecond();
        //将业务参数进行json_encode再进行base64_encode编码
        $dataBase    = base64_encode(json_encode($data));
        //生成签名并转换成大写
        $postData['sign'] = strtoupper(md5('bid='.$bid.'&_t='.$_t.'&data='.$dataBase.'&'.$payConf['md5_private_key']));
        $postData['bid']  = $bid;
        $postData['_t']   = $_t;
        $postData['data'] = $dataBase;

        unset($reqData);
        return $postData;
    }

    /**
     * 回调签名
     * @param $model
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($model, $data, $payConf){
        $arrData = [
            'bid'   => $data['bid'],
            '_t'    => $data['_t'],
            'data'  => $data['data'],
        ];
        //签名
        $signStr = self::getSignStr($arrData,false,false);
        $sign    = strtoupper(md5($signStr.'&'.$payConf['md5_private_key']));
        if ($sign == $data['sign']){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 时间戳 - 精确到毫秒
     * @return float
     */
    public static function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }
}