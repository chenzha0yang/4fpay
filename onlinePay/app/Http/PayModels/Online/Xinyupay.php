<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xinyupay extends ApiModel
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

        //TODO: do something

        $data = array();
        $data['V'] = 'V4.0';//版本号,默认V4.0
        $data['UserNo'] = $payConf['business_num']; //商家Id
        if($payConf['pay_way'] == 1){
            $data['pid'] = 'cxkzf';//支付编码
        }else{
            $data['pid'] = $bank;  //支付编码
            self::$reqType = 'curl';
            self::$payWay = $payConf['pay_way'];
            self::$httpBuildQuery = true;
        }
        self::$unit = 2;
        $data['amount'] = $amount*100;             //提交金额
        $data['ordNo'] = $order;              //订单Id号
        $data['ordTime'] = date("YmdHis",time()+(12*60*60));//商户提交订单时间
        $data['notifyUrl'] = $ServerUrl;      //下行url地址
        $data['frontUrl'] = $returnUrl;      //同步url地址
        $data['remark'] = 'zhifu';//扩展字段
        $data['ip'] = '100.101.23.12';//终端用户的真实IP
        $signStr = $data['V']."|".$data['UserNo']."|".$data['ordNo']."|".$data['ordTime']."|".$data['amount']."|".$data['pid']."|".$data['notifyUrl']."|".$data['frontUrl']."|".$data['remark']."|".$data['ip']."|";
        $postKey = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);
        $data["sign"] = $postKey;
        unset($reqData);
        return $data;
    }
}