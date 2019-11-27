<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Wanmeipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

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
        self::$unit = 2;
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if($payConf['is_app'] == 1){
            self::$isAPP = true;
        }

        $data = [
            'mchId'     => $payConf['business_num'],//商户号
            'type'      => 1,
            'channelId' => $bank,//支付类型
            'order'     => $order,//订单
            'amount'    => $amount*100,
            'notifyUrl' => $ServerUrl,
            'successUrl'=> $returnUrl,
            'errorUrl'  => $returnUrl

        ];

        $data['extra'] = json_encode(['amount'=>$data['amount']]);//附加参数
        $signStr = $payConf['md5_private_key'].$data['mchId'].$data['order'].$data['amount'];
        $data['sign'] = hash("sha256", $signStr);//签名

        unset($reqData);
        return $data;
    }

    /**
     * 回掉特殊处理
     * @param $mod
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($mod, $data, $payConf)
    {
        $signStr = $data['mchId'].$payConf['md5_private_key'].$data['orderNum'].$data['amount'];
        $sign = hash("sha256", $signStr);//签名
        if($sign == $data['sign'] && $data['state'] == 'success'){
            return true;
        } else {
            return false;
        }
    }

}