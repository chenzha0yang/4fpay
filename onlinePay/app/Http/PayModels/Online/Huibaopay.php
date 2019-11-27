<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huibaopay extends ApiModel
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
     * @param array $reqData       接口传递的参数
     * @param array $payConf
     * @return array
     * @internal param null|string $user
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
            $bank = '1003';
        }
        $data=array();
        $data['syncURL'] = $returnUrl;                //同步通知地址
        $data['asynURL'] = $ServerUrl;                //异步通知地址
        $data['merId'] = $payConf['business_num'];    //商户号
        $data['terId'] = $payConf['message1'];        //终端号
        $data['businessOrdid'] = $order;              //订单号
        $data['tradeMoney'] = $amount * 100;          //金额分
        $data['payType'] = $bank;                     //支付类型
        $data['appSence'] = '1001';                   //应用场景。默认pc
        $data['selfParam'] = '';                      //参数
        $data['orderName'] = 'chongzhi';              //订单名
        $signStr = self::getSignStr($data, true , true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    /**
     * @param $response
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult($response, $mod) {
        $result = trans("backField.{$mod}");
        $res['amount'] = $response[$result['amount']] / 100;  //金额
        $res['order']  = $response[$result['order']]; //订单号
        return $res;
    }
}
